<?php

namespace Greg\Orm;

use Greg\Orm\Query\SelectQuery;

trait TableTrait
{
    use QueryBuilderTrait;

    protected $prefix;

    protected $alias;

    protected $label;

    private $columns = false;

    private $primary = false;

    private $autoIncrement = false;

    protected $unique = [];

    protected $nameColumn;

    protected $casts = [];

    protected $defaults = [];

    abstract public function name(): string;

    public function prefix(): ?string
    {
        return $this->prefix;
    }

    public function fullName(): string
    {
        return $this->prefix() . $this->name();
    }

    public function alias(): ?string
    {
        return $this->alias;
    }

    public function label(): ?string
    {
        return $this->label;
    }

    public function columns(): array
    {
        if ($this->columns === false) {
            $this->loadSchema();
        }

        return (array) $this->columns;
    }

    public function hasColumn(string $name): bool
    {
        if ($this->columns === false) {
            $this->loadSchema();
        }

        return isset($this->columns[$name]);
    }

    public function column(string $name): array
    {
        $this->validateColumn($name);

        return $this->columns[$name];
    }

    public function primary(): array
    {
        if ($this->primary === false) {
            $this->loadSchema();
        }

        return (array) $this->primary;
    }

    public function unique(): array
    {
        $keys = (array) $this->unique;

        foreach ($keys as &$key) {
            $key = (array) $key;
        }
        unset($key);

        return $keys;
    }

    public function firstUnique(): array
    {
        if ($primary = $this->primary()) {
            return $primary;
        }

        if ($this->unique) {
            reset($this->unique);

            return (array) $this->unique[key($this->unique)];
        }

        throw new \Exception('No unique keys found in `' . $this->name() . '`.');
    }

    public function autoIncrement(): ?string
    {
        if ($this->autoIncrement === false) {
            $this->loadSchema();
        }

        return $this->autoIncrement;
    }

    public function nameColumn(): ?string
    {
        return $this->nameColumn;
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
     * @param array $defaults
     *
     * @return $this
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }

    public function describe(): array
    {
        return $this->connection()->describe($this->fullName());
    }

    /**
     * @param array $record
     *
     * @return $this
     */
    public function new(array $record = [])
    {
        $record = $this->prepareRecord($record);

        $record = array_merge($this->defaultRecord(), $record);

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

    /**
     * @throws SqlException
     *
     * @return array
     */
    public function pairs()
    {
        if (!$columnName = $this->nameColumn()) {
            throw new SqlException('Undefined column name for table `' . $this->name() . '`.');
        }

        if ($this->hasSelect()) {
            throw new SqlException('You cannot select table pairs while you have custom SELECT columns.');
        }

        return $this
            ->selectConcat($this->firstUnique(), ':')
            ->selectColumn($columnName)
            ->fetchPairs();
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
        $data = array_merge($data, $this->defaults);

        [$sql, $params] = $this->newInsertQuery()->data($data)->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    public function insertSelect(array $columns, SelectQuery $query): int
    {
        $columns = array_unique(array_merge($columns, array_keys($this->defaults)));

        if ($this->defaults) {
            $query = clone $query;

            foreach ($this->defaults as $column => $value) {
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
        $columns = array_unique(array_merge($columns, array_keys($this->defaults)));

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
        return $this->connection()->truncate($this->fullName());
    }

    public function prepareRecord(array $record, $reverse = false): array
    {
        foreach ($record as $columnName => &$value) {
            $value = $this->prepareValue($columnName, $value, $reverse);
        }
        unset($value);

        return $record;
    }

    public function prepareValue(string $columnName, $value, bool $reverse = false)
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

        switch ($this->cast($columnName) ?: $column['type']) {
            case 'datetime':
            case 'timestamp':
                $value = $this->connection()->dialect()->dateTimeString(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);

                break;
            case 'date':
                $value = $this->connection()->dialect()->dateString($value);

                break;
            case 'time':
                $value = $this->connection()->dialect()->timeString($value);

                break;
            case 'bool':
            case 'boolean':
                $value = (bool) $value;

                break;
            case 'array':
                $value = $reverse ? json_encode($value) : json_decode($value, true);

                break;
        }

        return $value;
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

    protected function validateColumn(string $name)
    {
        if ($this->columns === false) {
            $this->loadSchema();
        }

        if (!isset($this->columns[$name])) {
            throw new \Exception('Column `' . $name . '` not found in table `' . $this->name() . '`.');
        }

        return $this;
    }

    protected function combinePrimary($value)
    {
        $value = (array) $value;

        $keys = $this->primary();

        if (count($keys) !== count($value)) {
            throw new \Exception('Unique keys count should be the same as values count.');
        }

        return array_combine($keys, $value);
    }

    protected function loadSchema()
    {
        $schema = (array) $this->describe() + [
            'columns' => [],
            'primary' => [],
        ];

        if ($this->columns === false) {
            $this->columns = $schema['columns'];
        }

        if ($this->primary === false) {
            $this->primary = $schema['primary'];
        }

        if ($this->autoIncrement === false) {
            $this->autoIncrement = null;

            foreach ($schema['columns'] as $column) {
                if ($column['extra']['autoIncrement'] ?? false) {
                    $this->autoIncrement = $column['name'];

                    break;
                }
            }
        }

        return $this;
    }

    protected function rowsQueryInstance()
    {
        if ($this->hasSelect()) {
            throw new SqlException('You cannot fetch as rows while you have custom SELECT columns.');
        }

        return $this->selectOnly('*');
    }

    protected function selectQueryInstanceToSql()
    {
        if ($this->query) {
            return $this->selectQueryInstance()->toSql();
        }

        return [$this->connection()->dialect()->selectAll($this->name()), []];
    }

    protected function rowsQueryInstanceToSql()
    {
        if ($this->query) {
            return $this->rowsQueryInstance()->toSql();
        }

        return [$this->connection()->dialect()->selectAll($this->name()), []];
    }
}
