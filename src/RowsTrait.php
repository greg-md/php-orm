<?php

namespace Greg\Orm;

use Greg\Orm\Clause\WhereClause;
use Greg\Support\Arr;
use Greg\Support\DateTime;
use Greg\Support\Str;

trait RowsTrait
{
    use TableTrait;

    private $rows = [];

    private $rowsTotal = 0;

    private $rowsOffset = 0;

    private $rowsLimit = 0;

    public function row()
    {
        if ($record = $this->rowsQueryInstance()->fetch()) {
            return $this->cleanClone()->appendRecord($record);
        }

        return null;
    }

    public function rowOrFail()
    {
        if (!$row = $this->row()) {
            throw new QueryException('Row was not found.');
        }

        return $row;
    }

    public function rows()
    {
        $rows = $this->cleanClone();

        foreach ($this->rowsQueryInstance()->fetchYield() as $record) {
            $rows->appendRecord($record);
        }

        return $rows;
    }

    public function rowsYield()
    {
        foreach ($this->rowsQueryInstance()->fetchYield() as $record) {
            yield $this->cleanClone()->appendRecord($record);
        }
    }

    public function chunkRows($count, callable $callable, $callOneByOne = false)
    {
        $newCallable = function ($record) use ($callable, $callOneByOne) {
            if ($callOneByOne) {
                return call_user_func_array($callable, [$this->cleanClone()->appendRecord($record)]);
            }

            $rows = $this->cleanClone();

            foreach ($record as $item) {
                $rows->appendRecord($item);
            }

            return call_user_func_array($callable, [$rows]);
        };

        return $this->chunkQuery($this->rowsQueryInstance()->selectQuery(), $count, $newCallable, $callOneByOne);
    }

    public function find($key)
    {
        return $this->whereMultiple($this->combineFirstUnique($key))->row();
    }

    public function findOrFail($keys)
    {
        if (!$row = $this->find($keys)) {
            throw new QueryException('Row was not found.');
        }

        return $row;
    }

    public function firstOrNew(array $values)
    {
        if (!$row = $this->whereMultiple($values)->row()) {
            $row = $this->create($values);
        }

        return $row;
    }

    public function firstOrCreate(array $data)
    {
        return $this->firstOrNew($data)->save();
    }

    public function create(array $record = [])
    {
        return $this->cleanClone()->appendRecord($record, true);
    }

    public function save(array $values = [])
    {
        $this->setMultiple($values);

        foreach ($this->getIterator() as $row) {
            if ($row->isNew()) {
                $this->insert($row->original());

                if ($column = $this->getAutoIncrement()) {
                    $row[$column] = (int) $this->lastInsertId();
                }

                $row->markAsOld();
            } elseif ($record = $row->originalModified()) {
                $this->whereMultiple($this->getFirstUnique())->update($record);
            }
        }

        return $this;
    }

    public function destroy()
    {
        $keys = [];

        foreach ($this->getIterator() as $row) {
            $keys[] = $row->firstUnique();

            $row->markAsNew();
        }

        $this->where($this->firstUnique(), $keys)->delete();

        return $this;
    }

    public function appendRecord(array $record, bool $isNew = false, array $modified = [])
    {
        $record = array_merge($this->defaultRecord(), $record);

        $record = $this->prepareRecord($record);

        $this->rows[] = [
            'record'   => $record,
            'isNew'    => $isNew,
            'modified' => $modified,
        ];

        return $this;
    }

    public function appendRecordRef(array &$record, bool &$isNew = false, array &$modified = [])
    {
        $record = array_merge($this->defaultRecord(), $record);

        $record = $this->prepareRecord($record);

        $this->rows[] = [
            'record'   => &$record,
            'isNew'    => &$isNew,
            'modified' => &$modified,
        ];

        return $this;
    }

    public function cleanup()
    {
        $this->rows = [];

        $this->query = null;

        $this->clauses = [];

        return $this;
    }

    public function rowsTotal(): int
    {
        return $this->rowsTotal;
    }

    public function rowsOffset(): int
    {
        return $this->rowsOffset;
    }

    public function rowsLimit(): int
    {
        return $this->rowsLimit;
    }

    public function has(string $column): bool
    {
        if (!$this->rows) {
            return false;
        }

        foreach ($this->rows as &$row) {
            if (!$this->hasInRow($row, $column)) {
                return false;
            }
        }
        unset($row);

        return true;
    }

    public function hasMultiple(array $columns): bool
    {
        foreach ($columns as $column) {
            if (!$this->has($column)) {
                return false;
            }
        }

        return true;
    }

    public function set(string $column, $value)
    {
        $this->validateFillableColumn($column);

        $value = $this->prepareValue($column, $value);

        foreach ($this->rows as &$row) {
            $this->setInRow($row, $column, $value);
        }
        unset($row);

        return $this;
    }

    public function setMultiple(array $values)
    {
        foreach ($values as $column => $value) {
            $this->set($column, $value);
        }

        return $this;
    }

    public function get(string $column)
    {
        $this->validateColumn($column);

        $values = [];

        foreach ($this->rows as &$row) {
            $values[] = $this->getFromRow($row, $column);
        }
        unset($row);

        return $values;
    }

    public function getMultiple(array $columns)
    {
        $values = [];

        foreach ($columns as $column) {
            $values[$column] = $this->get($column);
        }

        return $values;
    }

    public function toArray($full = false)
    {
        if ($full) {
            return $this->rows;
        }

        return array_column($this->rows, 'record');
    }

    /**
     * @return \Generator|$this[]
     */
    public function getIterator()
    {
        foreach ($this->rows as $key => &$row) {
            yield $this->cleanClone()->appendRecordRef($row['record'], $row['isNew'], $row['modified']);
        }
    }

    public function getRowsIterator()
    {
        $rows = [];

        foreach ($this->rows as $row) {
            $rows[] = $this->cleanClone()->appendRecordRef($row['record'], $row['isNew'], $row['modified']);
        }

        return $rows;
    }

    public function count()
    {
        return count($this->rows);
    }

    public function markAsNew()
    {
        foreach ($this->rows as &$row) {
            $row['isNew'] = true;
        }
        unset($row);

        return $this;
    }

    public function markAsOld()
    {
        foreach ($this->rows as &$row) {
            $row['isNew'] = false;
        }
        unset($row);

        return $this;
    }

    /**
     * @param callable|null $callable
     * @param bool          $yield
     *
     * @return $this|null
     */
    public function first(callable $callable = null, bool $yield = true)
    {
        foreach ($yield ? $this->getIterator() : $this->getRowsIterator() as $key => $row) {
            if ($callable !== null) {
                if (call_user_func_array($callable, [$row, $key])) {
                    return $row;
                }
            } else {
                return $row;
            }
        }

        return null;
    }

    public function firstWhere(string $column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        return $this->first(function ($item) use ($column, $operator, $value) {
            if ($operator === '>') {
                return $item[$column] > $value;
            }

            if ($operator === '<') {
                return $item[$column] < $value;
            }

            if ($operator === '!=' or $operator === '<>') {
                return $item[$column] != $value;
            }

            if (strtolower($operator) === 'in') {
                return in_array($item[$column], (array) $value);
            }

            return $item[$column] == $value;
        });
    }

    public function hasMany($relationshipTable, $relationshipKey, $tableKey = null)
    {
        $relationshipTable = $this->getTableInstance($relationshipTable);

        if ($this->count()) {
            $relationshipKey = (array) $relationshipKey;

            if (!$tableKey) {
                $tableKey = $this->primary();
            }

            $tableKey = (array) $tableKey;

            $values = $this->get($tableKey);

            $relationshipTable->setWhereApplier(function (WhereClause $query) use ($relationshipKey, $values) {
                $query->where($relationshipKey, $values);
            });

            $filters = array_combine($relationshipKey, $this->getFirst($tableKey));

            $relationshipTable->setDefaults($filters);
        }

        return $relationshipTable;
    }

    public function belongsTo($referenceTable, $tableKey, $referenceTableKey = null)
    {
        $referenceTable = $this->getTableInstance($referenceTable);

        $tableKey = (array) $tableKey;

        if (!$referenceTableKey) {
            $referenceTableKey = $referenceTable->primary();
        }

        $referenceTableKey = (array) $referenceTableKey;

        $values = $this->get($tableKey);

        return $referenceTable->where($referenceTableKey, $values)->row();

        /*
        $referenceTable->applyOnWhere(function (WhereClauseInterface $query) use ($referenceTableKey, $values) {
            $query->where($referenceTableKey, $values);
        });

        $filters = array_combine($referenceTableKey, $this->getFirst($tableKey));

        $referenceTable->setDefaults($filters);

        return $referenceTable;
        */
    }

    protected function hasInRow(array &$row, string $column)
    {
        return !array_key_exists($column, $row['record']);
    }

    protected function setInRow(array &$row, string $column, string $value)
    {
        $recordValue = &Arr::getRef($row['record'], $column);

        if ($recordValue !== $value) {
            if ($row['isNew']) {
                $recordValue = $value;
            } else {
                $row['modified'][$column] = $value;
            }
        } else {
            unset($row['modified'][$column]);
        }

        return $this;
    }

    protected function getFromRow(array &$row, string $column)
    {
        if (array_key_exists($column, $row['modified'])) {
            return $row['modified'][$column];
        }

        if (array_key_exists($column, $row['record'])) {
            return $row['record'][$column];
        }

        return null;
    }

    protected function validateFillableColumn(string $column)
    {
        $this->validateColumn($column);

        if (($this->fillable !== '*' and !in_array($column, (array) $this->fillable))
            or ($this->guarded === '*' or in_array($column, (array) $this->guarded))) {
            throw new \Exception('Column `' . $column . '` is not fillable in the row.');
        }

        return $this;
    }

    protected function cleanClone()
    {
        $cloned = clone $this;

        $cloned->cleanup();

        return $cloned;
    }

    protected function rowsQueryInstance()
    {
        $instance = $this->selectQueryInstance();

        if ($instance->hasSelect()) {
            throw new QueryException('You cannot fetch as rows while you have custom SELECT columns.');
        }

        $instance->selectFrom($this, '*');

        return $instance;
    }

    protected function defaultRecord()
    {
        $record = [];

        foreach ($this->columns() as $column) {
            $record[$column['name']] = $column['default'];
        }

        return $record;
    }

    protected function prepareRecord(array $record, $reverse = false)
    {
        foreach ($record as $columnName => &$value) {
            $value = $this->prepareValue($columnName, $value, $reverse);
        }
        unset($value);

        return $record;
    }

    protected function prepareValue(string $columnName, $value, bool $reverse = false)
    {
        $column = $this->column($columnName);

        if ($value === '') {
            $value = null;
        }

        if (!$column['null']) {
            $value = (string) $value;
        }

        if ($value === null) {
            return $value;
        }

        if ($column['extra']['isInt'] and (!$column['null'] or $value !== null)) {
            $value = (int) $value;
        }

        if ($column['extra']['isFloat'] and (!$column['null'] or $value !== null)) {
            $value = (float) $value;
        }

        switch ($this->cast($columnName)) {
            case 'datetime':
            case 'timestamp':
                $value = DateTime::dateTimeString(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);

                break;
            case 'date':
                $value = DateTime::dateString($value);

                break;
            case 'time':
                $value = DateTime::timeString($value);

                break;
            case 'systemName':
                $value = $reverse ? Str::systemName($value) : $value;

                break;
            case 'boolean':
                $value = (bool) $value;

                break;
            case 'array':
                $value = $reverse ? json_encode($value) : json_decode($value, true);

                break;
        }

        return $value;
    }

    /**
     * @param $table
     *
     * @throws \Exception
     *
     * @return $this
     */
    protected function getTableInstance($table)
    {
        if (is_scalar($table)) {
            if (!is_subclass_of($table, self::class)) {
                throw new \Exception('`' . $table . '` is not an instance of `' . self::class . '`.');
            }

            $table = new $table([], $this->driver());
        }

        return $table;
    }
}
