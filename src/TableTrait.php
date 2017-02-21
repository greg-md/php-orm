<?php

namespace Greg\Orm;

use Greg\Orm\Driver\StatementStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Support\Arr;

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

    protected $unique;

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

        if ($unique = (array) Arr::first($this->unique)) {
            return $unique;
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

    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function describe()
    {
        return $this->driver()->describe($this->fullName());
    }

    public function create(array $record = [])
    {
        return $this->cleanClone()->appendRecord($record, true);
    }

    public function fetch(): ?array
    {
        return $this->selectQueryInstance()->execute()->fetch();
    }

    public function fetchOrFail(): array
    {
        if (!$record = $this->fetch()) {
            throw new QueryException('Row was not found.');
        }

        return $record;
    }

    public function fetchAll(): array
    {
        return $this->selectQueryInstance()->execute()->fetchAll();
    }

    public function fetchYield()
    {
        return $this->selectQueryInstance()->execute()->fetchYield();
    }

    public function fetchColumn(string $column = '0'): string
    {
        return $this->selectQueryInstance()->execute()->column($column);
    }

    public function fetchColumnAll(string $column = '0'): array
    {
        return $this->selectQueryInstance()->execute()->columnAll($column);
    }

    public function fetchColumnYield(string $column = '0'): array
    {
        return $this->selectQueryInstance()->execute()->columnYield($column);
    }

    public function fetchPairs(string $key = '0', string $value = '1'): array
    {
        return $this->selectQueryInstance()->execute()->pairs($key, $value);
    }

    public function fetchPairsYield(string $key = '0', string $value = '1'): array
    {
        return $this->selectQueryInstance()->execute()->pairsYield($key, $value);
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
            throw new QueryException('Row was not found.');
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
            throw new QueryException('Row was not found.');
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
            throw new QueryException('Row was not found.');
        }

        return $row;
    }

    public function firstOrNew(array $data)
    {
        if (!$row = $this->first($data)) {
            $row = $this->create($data);
        }

        return $row;
    }

    public function firstOrCreate(array $data)
    {
        return $this->firstOrNew($data)->save();
    }

    public function pairs()
    {
        if (!$columnName = $this->nameColumn()) {
            throw new QueryException('Undefined column name for table `' . $this->name() . '`.');
        }

        if ($this->hasSelect()) {
            throw new QueryException('You cannot select table pairs while you have custom SELECT columns.');
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

        $this->chunkQuery($this->rowsQueryInstance()->selectQuery(), $count, $newCallable, $callOneByOne, $yield);

        return $this;
    }

    public function exists(): bool
    {
        return (bool) $this->clearSelect()->selectRaw(1)->fetchColumn();
    }

    public function update(array $columns = []): int
    {
        return $this->setValues($columns)->execute()->affectedRows();
    }

    public function insert(array $data): int
    {
        $data = array_merge($data, $this->defaults);

        return $this->executeQuery($this->newInsertQuery()->data($data))->affectedRows();
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

        return $this->executeQuery($this->newInsertQuery()->columns($columns)->select($query))->affectedRows();
    }

    /**
     * @todo Need to inject columns into raw select
     *
     * @param array     $columns
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return int
     */
    public function insertSelectRaw(array $columns, string $sql, string ...$params): int
    {
        $columns = array_unique(array_merge($columns, array_keys($this->defaults)));

        return $this->executeQuery($this->newInsertQuery()->columns($columns)->selectRaw($sql, ...$params))->affectedRows();
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

        return $instance->execute()->affectedRows();
    }

    public function erase($primary)
    {
        $query = $this->newDeleteQuery()->whereMultiple($this->combinePrimary($primary));

        return $this->executeQuery($query);
    }

    public function truncate()
    {
        return $this->driver()->truncate($this->fullName());
    }

    public function prepare(): StatementStrategy
    {
        return $this->prepareQuery($this->query());
    }

    public function execute(): StatementStrategy
    {
        return $this->executeQuery($this->query());
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
        $schema = $this->describe();

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
            throw new QueryException('You cannot fetch as rows while you have custom SELECT columns.');
        }

        return $this->selectOnly('*');
    }

    protected function chunkQuery(SelectQuery $query, int $count, callable $callable, bool $callOneByOne = false, bool $yield = true)
    {
        if ($count < 1) {
            throw new QueryException('Chunk count should be greater than 0.');
        }

        $offset = 0;

        $query = clone $query;

        while (true) {
            $stmt = $this->executeQuery($query->limit($count)->offset($offset));

            if ($callOneByOne) {
                $k = 0;

                foreach ($yield ? $stmt->fetchYield() : $stmt->fetchAll() as $record) {
                    if (call_user_func_array($callable, [$record]) === false) {
                        $k = 0;

                        break;
                    }

                    ++$k;
                }
            } else {
                $records = $stmt->fetchAll();

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

    protected function prepareQuery(QueryStrategy $query): StatementStrategy
    {
        list($sql, $params) = $query->toSql();

        $stmt = $this->driver()->prepare($sql);

        if ($params) {
            $stmt->bindMultiple($params);
        }

        return $stmt;
    }

    protected function executeQuery(QueryStrategy $query): StatementStrategy
    {
        $stmt = $this->prepareQuery($query);

        $stmt->execute();

        return $stmt;
    }
}
