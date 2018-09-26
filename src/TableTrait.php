<?php

namespace Greg\Orm;

use Greg\Orm\Query\SelectQuery;

trait TableTrait
{
    use QueryBuilderTrait;

    protected $name;

    protected $alias;

    protected $primaryKey;

    protected $autoIncrement;

    protected $unique = [];

    protected $casts = [];

    protected $defaultRecord = [];

    protected $customRecord = [];

    private $tableInitEvents = [];

    public function name(): string
    {
        if (!$this->name) {
            $this->name = (new \ReflectionClass($this))->getShortName();
        }

        return $this->name;
    }

    public function alias(): ?string
    {
        $this->prepareTableInfo();

        return $this->alias;
    }

    public function primaryKey(): ?array
    {
        $this->prepareTableInfo();

        return $this->primaryKey;
    }

    public function autoIncrement(): ?string
    {
        $this->prepareTableInfo();

        return $this->autoIncrement;
    }

    public function defaultRecord(): array
    {
        $this->prepareTableInfo();

        return $this->autoIncrement;
    }

    public function customRecord(): array
    {
        return $this->autoIncrement;
    }

    public function uniqueKeys(): array
    {
        $this->prepareTableInfo();

        $keys = (array) $this->unique;

        foreach ($keys as &$key) {
            $key = (array) $key;
        }
        unset($key);

        return $keys;
    }

    public function firstUniqueKey(): array
    {
        if ($primary = $this->primaryKey()) {
            return $primary;
        }

        if ($this->unique) {
            reset($this->unique);

            return (array) $this->unique[key($this->unique)];
        }

        throw new \Exception('No unique keys found in `' . $this->name() . '`.');
    }

    public function casts(): array
    {
        return $this->casts;
    }

    public function cast(string $name): ?string
    {
        return $this->casts[$name] ?? null;
    }

    /**
     * @param array $customRecord
     *
     * @return $this
     */
    public function setCustomRecord(array $customRecord)
    {
        $this->customRecord = $customRecord;

        return $this;
    }

    public function getCustomRecord(): array
    {
        return $this->customRecord;
    }

    public function describe(): array
    {
        return $this->connection()->describe($this->name());
    }

    /**
     * @param array $record
     *
     * @return $this
     */
    public function new(array $record = [])
    {
        $record = $this->prepareRecord($record);

        return $this->cleanClone()->addPristineRecord($record)->markAsNew();
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

    public function fetch(): ?array
    {
        [$sql, $params] = $this->selectQueryInstanceToSql();

        return $this->connection()->sqlFetch($sql, $params);
    }

    public function fetchOrFail(): array
    {
        if (!$record = $this->fetch()) {
            throw new SqlException('Record not found.');
        }

        return $record;
    }

    public function fetchAll(): array
    {
        [$sql, $params] = $this->selectQueryInstanceToSql();

        return $this->connection()->sqlFetchAll($sql, $params);
    }

    public function generate(?int $chunkSize = null): \Generator
    {
        if ($chunkSize) {
            yield from $this->selectQueryInstance()->selectQuery()->generate($chunkSize);
        } else {
            [$sql, $params] = $this->selectQueryInstanceToSql();

            yield from $this->connection()->sqlGenerate($sql, $params);
        }
    }

    public function generateInChunks(int $chunkSize): \Generator
    {
        yield from $this->selectQueryInstance()->selectQuery()->generateInChunks($chunkSize);
    }

    public function fetchColumn(string $column = '0'): string
    {
        [$sql, $params] = $this->selectQueryInstanceToSql();

        return $this->connection()->sqlFetchColumn($sql, $params, $column);
    }

    public function fetchAllColumn(string $column = '0'): array
    {
        [$sql, $params] = $this->selectQueryInstanceToSql();

        return $this->connection()->sqlFetchAllColumn($sql, $params, $column);
    }

    public function fetchPairs(string $key = '0', string $value = '1'): array
    {
        [$sql, $params] = $this->selectQueryInstanceToSql();

        return $this->connection()->sqlFetchPairs($sql, $params, $key, $value);
    }

    /**
     * @return $this|null
     */
    public function fetchRow()
    {
        [$sql, $params] = $this->rowsQueryInstanceToSql();

        if ($record = $this->connection()->sqlFetch($sql, $params)) {
            return $this->cleanClone()->addPristineRecord($record);
        }

        return null;
    }

    /**
     * @throws SqlException
     *
     * @return $this|null
     */
    public function fetchRowOrFail()
    {
        if (!$row = $this->fetchRow()) {
            throw new SqlException('Row was not found.');
        }

        return $row;
    }

    /**
     * @return $this
     */
    public function fetchRows()
    {
        [$sql, $params] = $this->rowsQueryInstanceToSql();

        $records = $this->connection()->sqlFetchAll($sql, $params);

        return $this->cleanClone()->setPristineRecords($records);
    }

    /**
     * @param int|null $chunkSize
     *
     * @return \Generator|$this[]
     */
    public function generateRows(?int $chunkSize = null): \Generator
    {
        if ($chunkSize) {
            $recordsGenerator = $this->rowsQueryInstance()->selectQuery()->generate($chunkSize);
        } else {
            [$sql, $params] = $this->rowsQueryInstanceToSql();

            $recordsGenerator = $this->connection()->sqlGenerate($sql, $params);
        }

        foreach ($recordsGenerator as $record) {
            yield $this->cleanClone()->addPristineRecord($record);
        }
    }

    /**
     * @param int $chunkSize
     *
     * @return \Generator|$this[]
     */
    public function generateRowsInChunks(int $chunkSize): \Generator
    {
        $recordsGenerator = $this->rowsQueryInstance()->selectQuery()->generateInChunks($chunkSize);

        foreach ($recordsGenerator as $records) {
            yield $this->cleanClone()->setPristineRecords($records);
        }
    }

    public function fetchCount(string $column = '*', string $alias = null): int
    {
        if ($this->query) {
            return $this->clearSelect()->selectCount($column, $alias)->fetchColumn();
        }

        return $this->connection()->sqlFetchColumn($this->connection()->dialect()->selectCount($column, $alias));
    }

    public function fetchMax(string $column, string $alias = null): int
    {
        if ($this->query) {
            return $this->clearSelect()->selectMax($column, $alias)->fetchColumn();
        }

        return $this->connection()->sqlFetchColumn($this->connection()->dialect()->selectMax($column, $alias));
    }

    public function fetchMin(string $column, string $alias = null): int
    {
        if ($this->query) {
            return $this->clearSelect()->selectMin($column, $alias)->fetchColumn();
        }

        return $this->connection()->sqlFetchColumn($this->connection()->dialect()->selectMin($column, $alias));
    }

    public function fetchAvg(string $column, string $alias = null): float
    {
        if ($this->query) {
            return $this->clearSelect()->selectAvg($column, $alias)->fetchColumn();
        }

        return $this->connection()->sqlFetchColumn($this->connection()->dialect()->selectAvg($column, $alias));
    }

    public function fetchSum(string $column, string $alias = null): string
    {
        if ($this->query) {
            return $this->clearSelect()->selectSum($column, $alias)->fetchColumn();
        }

        return $this->connection()->sqlFetchColumn($this->connection()->dialect()->selectSum($column, $alias));
    }

    /**
     * @param $primary
     *
     * @return $this|null
     */
    public function find($primary)
    {
        return $this->whereMultiple($this->combinePrimary($primary))->fetchRow();
    }

    /**
     * @param $primary
     *
     * @throws SqlException
     *
     * @return $this|null
     */
    public function findOrFail($primary)
    {
        if (!$row = $this->find($primary)) {
            throw new SqlException('Row was not found.');
        }

        return $row;
    }

    /**
     * @param array $data
     *
     * @return $this|null
     */
    public function first(array $data)
    {
        return $this->whereMultiple($data)->fetchRow();
    }

    /**
     * @param array $data
     *
     * @throws SqlException
     *
     * @return $this|null
     */
    public function firstOrFail(array $data)
    {
        if (!$row = $this->first($data)) {
            throw new SqlException('Row was not found.');
        }

        return $row;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
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

    public function exists(): bool
    {
        return (bool) $this->clearSelect()->selectRaw(1)->limit(1)->fetchColumn();
    }

    public function update(array $columns = []): int
    {
        [$sql, $params] = $this->setValues($columns)->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    public function insert(array $data): int
    {
        $data = array_merge($data, $this->customRecord);

        [$sql, $params] = $this->newInsertQuery()->data($data)->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    public function insertSelect(array $columns, SelectQuery $query): int
    {
        $columns = array_unique(array_merge($columns, array_keys($this->customRecord)));

        if ($this->customRecord) {
            $query = clone $query;

            foreach ($this->customRecord as $column => $value) {
                $query->columnRaw('? as ' . $this->connection()->dialect()->quoteName($column), $value);
            }
        }
        [$sql, $params] = $this->newInsertQuery()->columns($columns)->select($query)->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    /**
     * @todo Need to inject columns into raw select
     *
     * @param array    $columns
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return int
     */
    public function insertSelectRaw(array $columns, string $sql, string ...$params): int
    {
        $columns = array_unique(array_merge($columns, array_keys($this->customRecord)));

        [$sql, $params] = $this->newInsertQuery()->columns($columns)->selectRaw($sql, ...$params)->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    public function insertForEach(string $column, array $values, array $data = []): int
    {
        $count = 0;

        foreach ($values as $value) {
            $count += $this->insert([$column => $value] + $data);
        }

        return $count;
    }

    public function delete(string ...$tables)
    {
        $instance = $this->deleteQueryInstance();

        if ($tables) {
            $instance->rowsFrom(...$tables);
        }
        [$sql, $params] = $instance->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    public function erase($primary)
    {
        $query = $this->newDeleteQuery()->whereMultiple($this->combinePrimary($primary));

        [$sql, $params] = $query->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    public function truncate()
    {
        return $this->connection()->truncate($this->name());
    }

    private function prepareRecord(array $record): array
    {
        return array_merge($this->defaultRecord, $this->customRecord, $record);
    }

    protected function castValue(string $columnName, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->cast($columnName)) {
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

    private function combinePrimary($value)
    {
        $value = (array) $value;

        $keys = $this->primaryKey();

        if (count($keys) !== count($value)) {
            throw new \Exception('Unique keys count should be the same as values count.');
        }

        return array_combine($keys, $value);
    }

    private function rowsQueryInstance()
    {
        if ($this->hasSelect()) {
            throw new SqlException('You cannot fetch as rows while you have custom SELECT columns.');
        }

        return $this->selectOnly('*');
    }

    private function selectQueryInstanceToSql()
    {
        if ($this->query) {
            return $this->selectQueryInstance()->toSql();
        }

        return [$this->connection()->dialect()->selectAll($this->name()), []];
    }

    private function rowsQueryInstanceToSql()
    {
        if ($this->query) {
            return $this->rowsQueryInstance()->toSql();
        }

        return [$this->connection()->dialect()->selectAll($this->name()), []];
    }

    protected function onTableInit(callable $callable)
    {
        $this->tableInitEvents[] = $callable;

        return $this;
    }

    private function prepareTableInfo()
    {
        foreach ($this->tableInitEvents as $event) {
            call_user_func_array($event, []);
        }

        return $this;
    }
}
