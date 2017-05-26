<?php

namespace Greg\Orm;

use Greg\Orm\Query\SelectQuery;

trait TableTrait
{
    use QueryBuilderTrait;

    protected $prefix;

    protected $name;

    protected $alias;

    protected $label;

    private $columns = false;

    private $primary = false;

    private $autoIncrement = false;

    protected $unique = [];

    protected $nameColumn;

    protected $casts = [];

    protected $defaults = [];

    public function prefix(): ?string
    {
        return $this->prefix;
    }

    public function name(): string
    {
        if (!$this->name) {
            throw new \Exception('Table name is required in model.');
        }

        return $this->name;
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
            return $this->unique[0];
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

    public function setCast(string $name, string $type): ?string
    {
        $this->casts[$name] = $type;

        return $name;
    }

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
        return $this->driver()->describe($this->fullName());
    }

    public function new(array $record = [])
    {
        return $this->cleanClone()->appendRecord($record, true);
    }

    public function create(array $record = [])
    {
        return $this->cleanClone()->appendRecord($record, true)->save();
    }

    public function fetch(): ?array
    {
        [$sql, $params] = $this->selectQueryInstance()->toSql();

        return $this->driver()->fetch($sql, $params);
    }

    public function fetchOrFail(): array
    {
        if (!$record = $this->fetch()) {
            throw new SqlException('Row was not found.');
        }

        return $record;
    }

    public function fetchAll(): array
    {
        [$sql, $params] = $this->selectQueryInstance()->toSql();

        return $this->driver()->fetchAll($sql, $params);
    }

    public function fetchYield()
    {
        [$sql, $params] = $this->selectQueryInstance()->toSql();

        yield from $this->driver()->fetchYield($sql, $params);
    }

    public function fetchColumn(string $column = '0'): string
    {
        [$sql, $params] = $this->selectQueryInstance()->toSql();

        return $this->driver()->column($sql, $params, $column);
    }

    public function fetchColumnAll(string $column = '0'): array
    {
        [$sql, $params] = $this->selectQueryInstance()->toSql();

        return $this->driver()->columnAll($sql, $params, $column);
    }

    public function fetchColumnYield(string $column = '0')
    {
        [$sql, $params] = $this->selectQueryInstance()->toSql();

        yield from $this->driver()->columnYield($sql, $params, $column);
    }

    public function fetchPairs(string $key = '0', string $value = '1'): array
    {
        [$sql, $params] = $this->selectQueryInstance()->toSql();

        return $this->driver()->pairs($sql, $params, $key, $value);
    }

    public function fetchPairsYield(string $key = '0', string $value = '1')
    {
        [$sql, $params] = $this->selectQueryInstance()->toSql();

        yield from $this->driver()->pairsYield($sql, $params, $key, $value);
    }

    public function fetchRow()
    {
        if ($record = $this->rowsQueryInstance()->fetch()) {
            $record = $this->prepareRecord($record);

            return $this->cleanClone()->appendRecord($record, false, [], true);
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
        $rows = $this->cleanClone();

        foreach ($this->rowsQueryInstance()->fetchYield() as $record) {
            $record = $this->prepareRecord($record);

            $rows->appendRecord($record, false, [], true);
        }

        return $rows;
    }

    /**
     * @return \Generator|$this[]
     */
    public function fetchRowsYield()
    {
        foreach ($this->rowsQueryInstance()->fetchYield() as $record) {
            $record = $this->prepareRecord($record);

            yield $this->cleanClone()->appendRecord($record, false, [], true);
        }
    }

    public function fetchCount(string $column = '*', string $alias = null): int
    {
        return $this->clearSelect()->selectCount($column, $alias)->fetchColumn();
    }

    public function fetchMax(string $column, string $alias = null): int
    {
        return $this->clearSelect()->selectMax($column, $alias)->fetchColumn();
    }

    public function fetchMin(string $column, string $alias = null): int
    {
        return $this->clearSelect()->selectMin($column, $alias)->fetchColumn();
    }

    public function fetchAvg(string $column, string $alias = null): float
    {
        return $this->clearSelect()->selectAvg($column, $alias)->fetchColumn();
    }

    public function fetchSum(string $column, string $alias = null): string
    {
        return $this->clearSelect()->selectSum($column, $alias)->fetchColumn();
    }

    public function find($primary)
    {
        return $this->whereMultiple($this->combinePrimary($primary))->fetchRow();
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
        return $this->whereMultiple($data)->fetchRow();
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

    public function firstOrCreate(array $data)
    {
        if (!$row = $this->first($data)) {
            $row = $this->create($data);
        }

        return $row;
    }

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

    public function chunk(int $count, callable $callable, bool $callOneByOne = false, bool $yield = true)
    {
        $this->chunkQuery($this->selectQueryInstance()->selectQuery(), $count, $callable, $callOneByOne, $yield);

        return $this;
    }

    public function chunkRows($count, callable $callable, $callOneByOne = false, bool $yield = true)
    {
        $this->chunkQuery($this->rowsQueryInstance()->selectQuery(), $count, function ($record) use ($callable, $callOneByOne) {
            if ($callOneByOne) {
                return call_user_func_array($callable, [$this->cleanClone()->appendRecord($record)]);
            }

            $rows = $this->cleanClone();

            foreach ($record as $item) {
                $rows->appendRecord($item);
            }

            return call_user_func_array($callable, [$rows]);
        }, $callOneByOne, $yield);

        return $this;
    }

    public function exists(): bool
    {
        return (bool) $this->clearSelect()->selectRaw(1)->fetchColumn();
    }

    public function update(array $columns = []): int
    {
        [$sql, $params] = $this->setValues($columns)->toSql();

        return $this->driver()->execute($sql, $params);
    }

    public function insert(array $data): int
    {
        $data = array_merge($data, $this->defaults);

        [$sql, $params] = $this->newInsertQuery()->data($data)->toSql();

        return $this->driver()->execute($sql, $params);
    }

    public function insertSelect(array $columns, SelectQuery $query): int
    {
        $columns = array_unique(array_merge($columns, array_keys($this->defaults)));

        if ($this->defaults) {
            $query = clone $query;

            foreach ($this->defaults as $column => $value) {
                $query->columnRaw('? as ' . $this->driver()->dialect()->quoteName($column), $value);
            }
        }
        [$sql, $params] = $this->newInsertQuery()->columns($columns)->select($query)->toSql();

        return $this->driver()->execute($sql, $params);
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

        return $this->driver()->execute($sql, $params);
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

        return $this->driver()->execute($sql, $params);
    }

    public function erase($primary)
    {
        $query = $this->newDeleteQuery()->whereMultiple($this->combinePrimary($primary));

        [$sql, $params] = $query->toSql();

        return $this->driver()->execute($sql, $params);
    }

    public function truncate()
    {
        return $this->driver()->truncate($this->fullName());
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
                $value = $this->driver()->dialect()->dateTimeString(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);

                break;
            case 'date':
                $value = $this->driver()->dialect()->dateString($value);

                break;
            case 'time':
                $value = $this->driver()->dialect()->timeString($value);

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

    protected function chunkQuery(SelectQuery $query, int $count, callable $callable, bool $callOneByOne = false, bool $yield = true)
    {
        if ($count < 1) {
            throw new SqlException('Chunk count should be greater than 0.');
        }

        $offset = 0;

        $query = clone $query;

        while (true) {
            [$sql, $params] = $query->limit($count)->offset($offset)->toSql();

            if ($callOneByOne) {
                $records = $yield
                    ? $this->driver()->fetchYield($sql, $params)
                    : $this->driver()->fetchAll($sql, $params);

                $k = 0;

                foreach ($records as $record) {
                    if (call_user_func_array($callable, [$record]) === false) {
                        $k = 0;

                        break;
                    }

                    ++$k;
                }
            } else {
                $records = $this->driver()->fetchAll($sql, $params);

                $k = count($records);

                if ($records and call_user_func_array($callable, [$records]) === false) {
                    $k = 0;
                }
            }

            if ($k < $count) {
                break;
            }

            $offset += $count;
        }

        return $this;
    }
}
