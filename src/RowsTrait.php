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

    /**
     * @param array $record
     * @param bool $isNew
     * @param array $modified
     * @param bool $isTrusted
     * @return $this
     */
    public function appendRecord(array $record, bool $isNew = false, array $modified = [], bool $isTrusted = false)
    {
        if (!$isTrusted) {
            $record = array_merge($this->defaultRecord(), $record);

            $record = $this->prepareRecord($record);

            $modified = $this->prepareRecord($modified);
        }

        $this->rows[] = [
            'record'   => $record,
            'isNew'    => $isNew,
            'modified' => $modified,
        ];

        return $this;
    }

    /**
     * @param array $record
     * @param bool $isNew
     * @param array $modified
     * @param bool $isTrusted
     * @return $this
     */
    public function appendRecordRef(array &$record, bool &$isNew = false, array &$modified = [], bool $isTrusted = false)
    {
        if (!$isTrusted) {
            $record = array_merge($this->defaultRecord(), $record);

            $record = $this->prepareRecord($record);

            $modified = $this->prepareRecord($modified);
        }

        $this->rows[] = [
            'record'   => &$record,
            'isNew'    => &$isNew,
            'modified' => &$modified,
        ];

        return $this;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param callable|null $totalQuery
     * @return $this
     */
    public function pagination(int $limit = 20, int $offset = 0, ?callable $totalQuery = null)
    {
        $query = clone $this->selectQueryInstance();

        return $query->paginate($limit, $offset, $totalQuery);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param callable|null $totalQuery
     * @return $this
     */
    public function paginate(int $limit = 20, int $offset = 0, ?callable $totalQuery = null)
    {
        $this->rowsLimit = $limit;

        $this->rowsOffset = $offset;

        foreach ($this->limit($limit)->offset($offset)->generate() as $record) {
            $this->appendRecord($record, false, [], true);
        }

        if ($totalQuery) {
            $query = $this->newSelectQuery();

            call_user_func_array($totalQuery, [$query]);
        } else {
            $query = clone $this->getSelectQuery();

            $query->clearLimit();
            $query->clearOffset();
        }
        [$sql, $params] = $query->clearColumns()->count()->toSql();

        $this->rowsTotal = $this->driver()->column($sql, $params);

        return $this;
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

        return (bool) $this->rows;
    }

    /**
     * @param string $column
     * @param $value
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

        foreach ($this->rows as &$row) {
            $this->setInRow($row, $column, $value);
        }
        unset($row);

        return $this;
    }

    /**
     * @param array $values
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

    /**
     * @param array $values
     * @return $this
     */
    public function save(array $values = [])
    {
        $this->setMultiple($values);

        foreach ($this->rows as &$row) {
            if ($row['isNew']) {
                $record = $this->prepareRecord($row['record'], true);

                $query = $this->newInsertQuery()->data($record);

                $this->driver()->execute(...$query->toSql());

                if (!$this->getAutoIncrement() and $column = $this->autoIncrement()) {
                    $row['record'][$column] = (int) $this->driver()->lastInsertId();
                }

                $row['isNew'] = false;
            } elseif ($modified = $this->prepareRecord($row['modified'], true)) {
                $query = $this->newUpdateQuery()
                    ->setMultiple($modified)
                    ->whereMultiple($this->rowFirstUnique($row));

                $this->driver()->execute(...$query->toSql());
            }

            $row['modified'] = [];
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

        foreach ($this->rows as &$row) {
            if ($row['isNew']) {
                continue;
            }

            $keys[] = $this->rowFirstUnique($row);

            $this->markRowAsNew($row);
        }
        unset($row);

        if ($keys) {
            $query = $this->newDeleteQuery()->where($this->firstUnique(), $keys);

            $this->driver()->execute(...$query->toSql());
        }

        return $this;
    }

    /**
     * @param int $number
     * @return $this|null
     */
    public function row(int $number = 0)
    {
        if (!isset($this->rows[$number])) {
            return null;
        }

        return $this->cleanClone()->appendRecordRef(
            $this->rows[$number]['record'],
            $this->rows[$number]['isNew'],
            $this->rows[$number]['modified'],
            true
        );
    }

    public function records(bool $full = false): array
    {
        if ($full) {
            return $this->rows;
        }

        return array_column($this->rows, 'record');
    }

    /**
     * @return $this
     */
    public function markAsNew()
    {
        foreach ($this->rows as &$row) {
            $this->markRowAsNew($row);
        }
        unset($row);

        return $this;
    }

    /**
     * @return $this
     */
    public function markAsOld()
    {
        foreach ($this->rows as &$row) {
            $row['isNew'] = false;
        }
        unset($row);

        return $this;
    }

    /**
     * @param callable $callable
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
        foreach (array_keys($this->rows) as $key) {
            yield $this->cleanClone()->appendRecordRef(
                $this->rows[$key]['record'],
                $this->rows[$key]['isNew'],
                $this->rows[$key]['modified'],
                true
            );
        }
    }

    protected function hasInRow(array &$row, string $column): bool
    {
        return (bool) ($row['record'][$column] ?? false);
    }

    protected function setInRow(array &$row, string $column, $value)
    {
        if ($row['record'][$column] !== $value) {
            if ($row['isNew']) {
                $row['record'][$column] = $value;
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

        return $this->castValue($column, $row['record'][$column] ?? null);
    }

    protected function rowFirstUnique(array &$row)
    {
        $keys = [];

        foreach ($this->firstUnique() as $key) {
            $keys[$key] = $row['record'][$key];
        }

        return $keys;
    }

    protected function markRowAsNew(array &$row)
    {
        $row['isNew'] = true;

        $row['record'] = array_merge($row['record'], $row['modified']);

        $row['modified'] = [];

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
}
