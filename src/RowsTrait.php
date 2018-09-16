<?php

namespace Greg\Orm;

use Greg\Orm\Clause\WhereClause;

trait RowsTrait
{
    use TableTrait;

    protected $fillable = '*';

    protected $guarded = [];

    private $rows = [];

    private $rowsTotal = 0;

    private $rowsOffset = 0;

    private $rowsLimit = 0;

    private $rowsState = [];

    public function fillable()
    {
        return $this->fillable === '*' ? $this->fillable : (array) $this->fillable;
    }

    public function guarded()
    {
        return $this->guarded === '*' ? $this->guarded : (array) $this->guarded;
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

    public function setPristineRecords(array $records, array $recordsState = [])
    {
        $this->rows = $records;

        $this->rowsState = $recordsState;

        return $this;
    }

    public function addPristineRecord(array $record, array $recordState = null)
    {
        $this->rows[] = $record;

        if ($recordState) {
            end($this->rows);

            $this->rowsState[key($this->rows)] = $recordState;
        }

        return $this;
    }

    /**
     * @param array $record
     * @param array $recordState
     *
     * @return $this
     */
    public function addPristineRecordRef(array &$record, array &$recordState = null)
    {
        $this->rows[] = &$record;

        if ($recordState) {
            end($this->rows);

            $this->rowsState[key($this->rows)] = &$recordState;
        }

        return $this;
    }

    /**
     * @param int           $limit
     * @param int           $offset
     * @param callable|null $totalQuery
     *
     * @return $this
     */
    public function pagination(int $limit = 20, int $offset = 0, ?callable $totalQuery = null)
    {
        $query = clone $this->selectQueryInstance();

        return $query->paginate($limit, $offset, $totalQuery);
    }

    /**
     * @param int           $limit
     * @param int           $offset
     * @param callable|null $totalQuery
     *
     * @return $this
     */
    public function paginate(int $limit = 20, int $offset = 0, ?callable $totalQuery = null)
    {
        $this->rowsLimit = $limit;

        $this->rowsOffset = $offset;

        $records = $this->limit($limit)->offset($offset)->fetchAll();

        $this->setPristineRecords($records);

        if ($totalQuery) {
            $query = $this->newSelectQuery();

            call_user_func_array($totalQuery, [$query]);
        } else {
            $query = clone $this->getSelectQuery();

            $query->clearLimit();
            $query->clearOffset();
        }
        [$sql, $params] = $query->clearColumns()->count()->toSql();

        $this->rowsTotal = $this->connection()->column($sql, $params);

        return $this;
    }

    public function has(string $column): bool
    {
        if (!$this->rows) {
            return false;
        }

        foreach ($this->rows as $row) {
            if (!$this->hasInRow($row, $column)) {
                return false;
            }
        }

        return true;
    }

    public function hasMultiple(array $columns): bool
    {
        foreach ($columns as $column) {
            if (!$this->has($column)) {
                return false;
            }
        }

        return (bool) $this->rows;
    }

    /**
     * @param string $column
     * @param $value
     *
     * @return $this
     */
    public function set(string $column, $value)
    {
        if (method_exists($this, $this->getAttributeSetMethod($column))) {
            foreach ($this as $row) {
                $row[$column] = $value;
            }

            return $this;
        }

        $this->validateFillableColumn($column);

        $value = $this->prepareValue($column, $value);

        foreach (array_keys($this->rows) as $key) {
            $this->setInRow($key, $column, $value);
        }
        unset($row);

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function setMultiple(array $values)
    {
        foreach ($values as $column => $value) {
            $this->set($column, $value);
        }

        return $this;
    }

    public function get(string $column)
    {
        if (method_exists($this, $this->getAttributeGetMethod($column))) {
            $values = [];

            foreach ($this as $row) {
                $values[] = $row[$column];
            }

            return $values;
        }

        $this->validateColumn($column);

        $values = [];

        foreach (array_keys($this->rows) as $key) {
            $values[] = $this->getFromRow($key, $column);
        }

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

    /**
     * @param array $values
     *
     * @return $this
     */
    public function save(array $values = [])
    {
        $this->setMultiple($values);

        foreach ($this->rows as $key => &$row) {
            if ($this->rowStateIsNew($key)) {
                $record = $this->prepareRecord($row, true);

                $query = $this->newInsertQuery()->data($record);

                $this->connection()->execute(...$query->toSql());

                if (!$this->getAutoIncrement() and $column = $this->autoIncrement()) {
                    $row[$column] = (int) $this->connection()->lastInsertId();
                }

                unset($this->rowsState[$key]);
            } else {
                $modified = $this->rowStateGetModified($key);

                if ($modified) {
                    $modified = $this->prepareRecord($modified, true);
                }

                if ($modified) {
                    $query = $this->newUpdateQuery()
                        ->setMultiple($modified)
                        ->whereMultiple($this->rowFirstUnique($row));

                    $this->connection()->execute(...$query->toSql());

                    unset($this->rowsState[$key]);
                }
            }
        }
        unset($row);

        return $this;
    }

    /**
     * @return $this
     */
    public function destroy()
    {
        $keys = [];

        foreach ($this->rows as $key => $row) {
            if ($this->rowStateIsNew($key)) {
                continue;
            }

            $keys[] = $this->rowFirstUnique($row);

            $this->markRowAsNew($key);
        }

        if ($keys) {
            [$sql, $params] = $this->newDeleteQuery()->where($this->firstUnique(), $keys)->toSql();

            $this->connection()->execute($sql, $params);
        }

        return $this;
    }

    /**
     * @param int $number
     *
     * @return $this|null
     */
    public function row(int $number = 0)
    {
        if (!isset($this->rows[$number])) {
            return null;
        }

        return $this->cleanClone()->addPristineRecordRef(
            $this->rows[$number],
            $this->rowGetState($number)
        );
    }

    public function records(): array
    {
        return $this->rows;
    }

    /**
     * @return $this
     */
    public function markAsNew()
    {
        foreach (array_keys($this->rows) as $key) {
            $this->markRowAsNew($key);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function markAsOld()
    {
        foreach ($this->rowsState as $key => $rowState) {
            if ($rowState['isNew']) {
                unset($this->rowsState[$key]);
            }
        }

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this|null
     */
    public function search(callable $callable)
    {
        foreach ($this as $key => $row) {
            if (call_user_func_array($callable, [$row, $key])) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @param string $column
     * @param $operator
     * @param null $value
     *
     * @return $this|null
     */
    public function searchWhere(string $column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        return $this->search(function ($item) use ($column, $operator, $value) {
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

    /**
     * @param Model $relationshipTable
     * @param $relationshipKey
     * @param null $tableKey
     *
     * @return Model
     */
    public function hasMany(Model $relationshipTable, $relationshipKey, $tableKey = null)
    {
        $relationshipTable = clone $relationshipTable;

        $relationshipTable->cleanup();

        if ($this->count()) {
            $relationshipKey = (array) $relationshipKey;

            if (!$tableKey) {
                $tableKey = $this->primary();
            }

            $tableKey = (array) $tableKey;

            $values = $this->getMultiple($tableKey);

            $relationshipTable->setWhereApplier(function (WhereClause $query) use ($relationshipKey, $values) {
                $query->where($relationshipKey, $values);
            });

            $filters = array_combine($relationshipKey, $this->getFirstMultiple($tableKey));

            $relationshipTable->setDefaults($filters);
        }

        return $relationshipTable;
    }

    /**
     * @param Model $referenceTable
     * @param $tableKey
     * @param null $referenceTableKey
     *
     * @return Model
     */
    public function belongsTo(Model $referenceTable, $tableKey, $referenceTableKey = null)
    {
        $referenceTable = clone $referenceTable;

        $referenceTable->cleanup();

        $tableKey = (array) $tableKey;

        if (!$referenceTableKey) {
            $referenceTableKey = $referenceTable->primary();
        }

        $referenceTableKey = (array) $referenceTableKey;

        $values = $this->getMultiple($tableKey);

        //        return $referenceTable->where($referenceTableKey, $values)->fetchRow();

        $referenceTable->setWhereApplier(function (WhereClause $query) use ($referenceTableKey, $values) {
            $query->where($referenceTableKey, $values);
        });

        $filters = array_combine($referenceTableKey, $this->getFirstMultiple($tableKey));

        $referenceTable->setDefaults($filters);

        return $referenceTable;
    }

    public function count()
    {
        return count($this->rows);
    }

    /**
     * @return \Generator|$this[]
     */
    public function getIterator()
    {
        foreach ($this->rows as $key => $row) {
            yield $key => $this->cleanClone()->addPristineRecordRef(
                $this->rows[$key],
                $this->rowGetState($key)
            );
        }
    }

    protected function firstRowKey(): int
    {
        if (!$this->rows) {
            throw new \Exception('Model row is not found.');
        }

        reset($this->rows);

        return key($this->rows);
    }

    protected function &firstRow(): array
    {
        return $this->rows[$this->firstRowKey()];
    }

    protected function hasInRow(array $row, string $column): bool
    {
        return (bool) ($row[$column] ?? false);
    }

    protected function setInRow(int $key, string $column, $value)
    {
        if ($this->rows[$key][$column] !== $value) {
            if ($this->rowStateIsNew($key)) {
                $this->rows[$key][$column] = $value;
            } else {
                $rowsState = &$this->rowGetState($key);

                $rowsState['modified'][$column] = $value;
            }
        } else {
            unset($this->rowsState[$key]['modified'][$column]);
        }

        return $this;
    }

    protected function getFromRow(int $key, string $column)
    {
        if (isset($this->rowsState[$key]) and array_key_exists($column, $this->rowsState[$key]['modified'])) {
            return $this->rowsState[$key]['modified'][$column];
        }

        return $this->castValue($column, $this->rows[$key][$column] ?? null);
    }

    protected function rowFirstUnique(array $row)
    {
        $keys = [];

        foreach ($this->firstUnique() as $key) {
            $keys[$key] = $row[$key];
        }

        return $keys;
    }

    protected function markRowAsNew(int $key)
    {
        $rowState = &$this->rowGetState($key);

        $rowState['isNew'] = true;

        if ($rowState['modified']) {
            $this->rows[$key] = array_merge($this->rows[$key], $rowState['modified']);

            $rowState['modified'] = [];
        }

        return $this;
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

    protected function defaultRecord(): array
    {
        $record = [];

        foreach ($this->columns() as $column) {
            $record[$column['name']] = $column['default'];
        }

        return $record;
    }

    protected function getAttributeGetMethod(string $column): string
    {
        return 'get' . ucfirst($column) . 'Attribute';
    }

    protected function getAttributeSetMethod(string $column): string
    {
        return 'set' . ucfirst($column) . 'Attribute';
    }

    protected function &rowGetState(int $key)
    {
        if (!isset($this->rowsState[$key])) {
            $this->rowsState[$key] = [
                'isNew'    => false,
                'modified' => [],
            ];
        }

        return $this->rowsState[$key];
    }

    protected function rowStateIsNew(int $key)
    {
        return $this->rowsState[$key]['isNew'] ?? false;
    }

    protected function rowStateGetModified(int $key)
    {
        return $this->rowsState[$key]['modified'] ?? [];
    }
}
