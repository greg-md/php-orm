<?php

namespace Greg\Orm;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Connection\ConnectionStrategy;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;

trait RowsTrait
{
    protected $casts = [];

    protected $uncasts = [];

    protected $defaultRecord = [];

    protected $fillable = '*';

    protected $guarded = [];

    private $rows = [];

    private $rowsTotal = 0;

    private $rowsOffset = 0;

    private $rowsLimit = 0;

    private $rowsState = [];

    public function defaultRecord(): array
    {
        return $this->defaultRecord;
    }

    /**
     * @param array $record
     *
     * @return $this
     */
    public function new(array $record = [])
    {
        $record = $this->prepareBaseRecord($record);

        return $this->cleanClone()->addPristineRecord($record, ['isCasted' => true])->markAsNew();
    }

    /**
     * @param array $record
     *
     * @return $this
     */
    public function create(array $record = [])
    {
        return $this->new($record)->save();
    }

    public function insert(array $record): int
    {
        $record = $this->prepareBaseRecord($record);

        return parent::insert($record);
    }

    public function fetchRow()
    {
        [$sql, $params] = $this->rowsQueryInstanceToSql();

        if ($record = $this->connection()->sqlFetch($sql, $params)) {
            return $this->prepareRowInstance($record);
        }

        return null;
    }

    public function fetchRowOrFail()
    {
        if (!$row = $this->fetchRow()) {
            throw new SqlException('Row was not found.');
        }

        return $row;
    }

    public function fetchRows()
    {
        [$sql, $params] = $this->rowsQueryInstanceToSql();

        $records = $this->connection()->sqlFetchAll($sql, $params);

        return $this->prepareRowsInstance($records);
    }

    public function generateRows(?int $chunkSize = null): \Generator
    {
        if ($chunkSize) {
            $recordsGenerator = $this->rowsQueryInstance()->selectQuery()->generate($chunkSize);
        } else {
            [$sql, $params] = $this->rowsQueryInstanceToSql();

            $recordsGenerator = $this->connection()->sqlGenerate($sql, $params);
        }

        foreach ($recordsGenerator as $record) {
            yield $this->prepareRowInstance($record);
        }
    }

    public function generateRowsInChunks(int $chunkSize): \Generator
    {
        $recordsGenerator = $this->rowsQueryInstance()->selectQuery()->generateInChunks($chunkSize);

        foreach ($recordsGenerator as $records) {
            yield $this->prepareRowsInstance($records);
        }
    }

    public function find($primary)
    {
        return $this->whereMultiple($this->combinePrimaryKey($primary))->fetchRow();
    }

    public function findOrFail($primary)
    {
        if (!$row = $this->find($primary)) {
            throw new SqlException('Row was not found.');
        }

        return $row;
    }

    public function first(array $data)
    {
        return $this->whereMultiple($data)->limit(1)->fetchRow();
    }

    public function firstOrFail(array $data)
    {
        if (!$row = $this->first($data)) {
            throw new SqlException('Row was not found.');
        }

        return $row;
    }

    public function firstOrNew(array $data)
    {
        if (!$row = $this->first($data)) {
            $row = $this->new($data);
        }

        return $row;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function firstOrCreate(array $data)
    {
        if (!$row = $this->first($data)) {
            $row = $this->create($data);
        }

        return $row;
    }

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

            $this->rowsState[key($this->rows)] = $recordState + $this->defaultRowState();
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

            $recordState += $this->defaultRowState();

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

        $this->rowsTotal = $this->connection()->sqlFetchColumn($sql, $params);

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
            $data = $row;
            if ($this->rowStateIsCasted($key)) {
                $data = $this->uncastRecord($data);
            }

            if ($this->rowStateIsNew($key)) {
                $query = $this->newInsertQuery()->data($data);

                $this->connection()->sqlExecute(...$query->toSql());

                if (!$this->getAutoIncrement() and $column = $this->autoIncrement()) {
                    $row[$column] = (int) $this->connection()->lastInsertId();
                }

                unset($this->rowsState[$key]);
            } else {
                $modified = $this->rowStateGetModified($key);

                if ($modified) {
                    $query = $this->newUpdateQuery()
                        ->setMultiple($modified)
                        ->whereMultiple($this->rowFirstUnique($row));

                    $this->connection()->sqlExecute(...$query->toSql());

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
            [$sql, $params] = $this->newDeleteQuery()->where($this->firstUniqueKey(), $keys)->toSql();

            $this->connection()->sqlExecute($sql, $params);
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

    public function hasMany(Model $relationshipTable, $relationshipKey, $tableKey = null)
    {
        $relationshipTable = $relationshipTable->cleanClone();

        if ($this->count()) {
            $relationshipKey = (array) $relationshipKey;

            if (!$tableKey) {
                $tableKey = $this->primaryKey();
            }

            $tableKey = (array) $tableKey;

            $values = $this->getMultiple($tableKey);

            $relationshipTable->setWhereApplier(function (WhereClause $query) use ($relationshipKey, $values) {
                $query->where($relationshipKey, $values);
            });
        }

        return $relationshipTable;
    }

    public function belongsTo(Model $referenceTable, $tableKey, $referenceTableKey = null)
    {
        $referenceTable = $referenceTable->cleanClone();

        $tableKey = (array) $tableKey;

        if (!$referenceTableKey) {
            $referenceTableKey = $referenceTable->primaryKey();
        }

        $referenceTableKey = (array) $referenceTableKey;

        $values = $this->getMultiple($tableKey);

        $referenceTable->setWhereApplier(function (WhereClause $query) use ($referenceTableKey, $values) {
            $query->where($referenceTableKey, $values);
        });

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

    private function rowsQueryInstance()
    {
        if ($this->hasSelect()) {
            throw new SqlException('You cannot fetch as rows while you have custom SELECT columns.');
        }

        return $this->selectOnly('*');
    }

    private function rowsQueryInstanceToSql()
    {
        if ($this->getQuery()) {
            return $this->rowsQueryInstance()->toSql();
        }

        return [$this->connection()->dialect()->selectAll($this->name()), []];
    }

    protected function prepareRowInstance(array $record)
    {
        return $this->cleanClone()->addPristineRecord($record);
    }

    protected function prepareRowsInstance(array $records)
    {
        return $this->cleanClone()->setPristineRecords($records);
    }

    protected function prepareBaseRecord(array $record)
    {
        return array_merge($this->defaultRecord, $record);
    }

    private function firstRowKey(): int
    {
        if (!$this->rows) {
            throw new \Exception('Model row is not found.');
        }

        reset($this->rows);

        return key($this->rows);
    }

    private function &firstRow(): array
    {
        return $this->rows[$this->firstRowKey()];
    }

    private function hasInRow(array $row, string $column): bool
    {
        return (bool) ($row[$column] ?? false);
    }

    private function setInRow(int $key, string $column, $value)
    {
        $this->castRowByKey($key);

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

    private function getFromRow(int $key, string $column)
    {
        $this->castRowByKey($key);

        $modified = $this->rowStateGetModified($key);

        if (array_key_exists($column, $modified)) {
            return $modified[$column];
        }

        return $this->rows[$key][$column] ?? null;
    }

    private function castRowByKey($key)
    {
        foreach (array_keys($this->casts) as $columnName) {
            $this->rows[$key][$columnName] = $this->castValue($columnName, $this->rows[$key][$columnName] ?? null);
        }

        $rowState = &$this->rowGetState($key);

        $rowState['isCasted'] = true;

        return $this;
    }

    private function uncastRecord(array $record)
    {
        foreach (array_keys($this->casts) as $columnName) {
            $record[$columnName] = $this->castValue($columnName, $record[$columnName] ?? null);
        }

        return $record;
    }

    protected function castValue(string $columnName, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        $castType = $this->casts[$columnName] ?? null;

        if ($castType instanceof \Closure) {
            return call_user_func_array($castType, [$value]);
        }

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                switch ((string) $value) {
                    case 'Infinity':
                        return INF;
                    case '-Infinity':
                        return -INF;
                    case 'NaN':
                        return NAN;
                    default:
                        return (float) $value;
                }
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return json_decode($value, false);
            case 'array':
            case 'json':
                return json_decode($value, true);
            case 'date':
                return $this->connection()->dialect()->dateString($value);
            case 'time':
                return $this->connection()->dialect()->timeString($value);
            case 'datetime':
                return $this->connection()->dialect()->dateTimeString(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);
            case 'timestamp':
                return ctype_digit((string) $value) ? $value : strtotime(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);
            default:
                return $value;
        }
    }

    protected function uncastValue(string $columnName, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        $uncastType = $this->uncasts[$columnName] ?? $this->casts[$columnName] ?? null;

        if (is_callable($uncastType)) {
            return call_user_func_array($uncastType, $value);
        }

        switch ($uncastType) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                if ($value === INF) {
                    return 'Infinity';
                }
                if ($value === -INF) {
                    return '-Infinity';
                }
                if ($value === NAN) {
                    return 'NaN';
                }
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return json_encode($value);
            case 'array':
            case 'json':
                return json_encode($value);
            case 'timestamp':
                return ctype_digit((string) $value) ? $value : strtotime(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);
            default:
                return $value;
        }
    }

    private function rowFirstUnique(array $row)
    {
        $keys = [];

        foreach ($this->firstUniqueKey() as $key) {
            $keys[$key] = $row[$key];
        }

        return $keys;
    }

    private function markRowAsNew(int $key)
    {
        $rowState = &$this->rowGetState($key);

        $rowState['isNew'] = true;

        if ($rowState['modified']) {
            $this->rows[$key] = array_merge($this->rows[$key], $rowState['modified']);

            $rowState['modified'] = [];
        }

        return $this;
    }

    private function validateFillableColumn(string $column)
    {
        if (($this->fillable !== '*' and !in_array($column, (array) $this->fillable))
            or ($this->guarded === '*' or in_array($column, (array) $this->guarded))) {
            throw new \Exception('Column `' . $column . '` is not fillable in the row.');
        }

        return $this;
    }

    private function getAttributeGetMethod(string $column): string
    {
        return 'get' . ucfirst($column) . 'Attribute';
    }

    private function getAttributeSetMethod(string $column): string
    {
        return 'set' . ucfirst($column) . 'Attribute';
    }

    private function &rowGetState(int $key)
    {
        if (!isset($this->rowsState[$key])) {
            $this->rowsState[$key] = $this->defaultRowState();
        }

        return $this->rowsState[$key];
    }

    private function rowStateIsNew(int $key): bool
    {
        return $this->rowsState[$key]['isNew'] ?? false;
    }

    private function rowStateIsCasted(int $key): bool
    {
        return $this->rowsState[$key]['isCasted'] ?? false;
    }

    private function rowStateGetModified(int $key)
    {
        return $this->rowsState[$key]['modified'] ?? [];
    }

    private function defaultRowState()
    {
        return [
            'isNew'    => false,
            'modified' => [],
            'isCasted'   => false,
        ];
    }

    /**
     * @return $this
     */
    abstract protected function selectQueryInstance();

    /**
     * @param int $number
     * @return $this
     */
    abstract public function limit(int $number);

    /**
     * @param int $number
     * @return $this
     */
    abstract public function offset(int $number);

    abstract public function getSelectQuery(): ?SelectQuery;

    abstract public function connection(): ConnectionStrategy;

    abstract public function newInsertQuery(): InsertQuery;

    abstract public function newUpdateQuery(): UpdateQuery;

    abstract public function newDeleteQuery(): DeleteQuery;

    /**
     * @return $this
     */
    abstract public function cleanClone();
}
