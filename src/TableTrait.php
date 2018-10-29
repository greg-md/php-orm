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

    protected $uniqueKeys = [];

    public function name(): string
    {
        if (!$this->name) {
            $this->name = (new \ReflectionClass($this))->getShortName();
        }

        return $this->name;
    }

    public function alias(): ?string
    {
        return $this->alias;
    }

    public function primaryKey(): ?array
    {
        return $this->primaryKey;
    }

    public function autoIncrement(): ?string
    {
        return $this->autoIncrement;
    }

    public function uniqueKeys(): array
    {
        $keys = (array) $this->uniqueKeys;

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

        if ($this->uniqueKeys) {
            reset($this->uniqueKeys);

            return (array) $this->uniqueKeys[key($this->uniqueKeys)];
        }

        throw new \Exception('No unique keys found in `' . $this->name() . '`.');
    }

    public function describe(): array
    {
        return $this->connection()->describe($this->name());
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

    public function exists(): bool
    {
        return (bool) $this->clearSelect()->selectRaw(1)->limit(1)->fetchColumn();
    }

    public function update(array $columns = []): int
    {
        [$sql, $params] = $this->setValues($columns)->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    public function insert(array $record): int
    {
        [$sql, $params] = $this->newInsertQuery()->data($record)->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    public function insertSelect(array $columns, SelectQuery $query): int
    {
        [$sql, $params] = $this->newInsertQuery()->columns($columns)->select($query)->toSql();

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
        $query = $this->newDeleteQuery()->whereMultiple($this->combinePrimaryKey($primary));

        [$sql, $params] = $query->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    public function truncate()
    {
        return $this->connection()->truncate($this->name());
    }

    protected function combinePrimaryKey($value)
    {
        $value = (array) $value;

        $keys = $this->primaryKey();

        if (count($keys) !== count($value)) {
            throw new \Exception('Unique keys count should be the same as values count.');
        }

        return array_combine($keys, $value);
    }

    private function selectQueryInstanceToSql()
    {
        if ($this->query) {
            return $this->selectQueryInstance()->toSql();
        }

        return [$this->connection()->dialect()->selectAll($this->name()), []];
    }
}
