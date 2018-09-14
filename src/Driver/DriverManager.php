<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Dialect\DialectStrategy;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;

class DriverManager implements DriverStrategy
{
    private $drivers = [];

    private $defaultDriverName;

    /**
     * @param string $name
     * @return $this
     * @throws \Exception
     */
    public function setDefaultDriverName(string $name)
    {
        if (!isset($this->drivers[$name])) {
            throw new \Exception('Driver `' . $name . '` was not defined.');
        }

        $this->defaultDriverName = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDefaultDriverName(): ?string
    {
        return $this->defaultDriverName;
    }

    /**
     * @param $name
     * @param callable $callable
     * @param bool $default
     * @return $this
     */
    public function register($name, callable $callable, bool $default = false)
    {
        $this->drivers[$name] = $callable;

        if ($default) {
            $this->setDefaultDriverName($name);
        }

        return $this;
    }

    /**
     * @param $name
     * @param DriverStrategy $strategy
     * @param bool $default
     * @return $this
     */
    public function registerStrategy($name, DriverStrategy $strategy, bool $default = false)
    {
        $this->drivers[$name] = $strategy;

        if ($default) {
            $this->setDefaultDriverName($name);
        }

        return $this;
    }

    public function driver(?string $name = null): DriverStrategy
    {
        if (!$name = $name ?: $this->defaultDriverName) {
            throw new \Exception('Default driver strategy was not defined.');
        }

        if (!$strategy = $this->drivers[$name] ?? null) {
            throw new \Exception('Driver strategy `' . $name . '` was not defined.');
        }

        if (is_callable($strategy)) {
            $strategy = call_user_func_array($strategy, []);

            if (!($strategy instanceof DriverStrategy)) {
                throw new \Exception('Driver strategy `' . $name . '` must be an instance of `' . DriverStrategy::class . '`');
            }

            $this->drivers[$name] = $strategy;
        }

        return $this->drivers[$name];
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function transaction(callable $callable)
    {
        $this->driver()->transaction($callable);

        return $this;
    }

    /**
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->driver()->inTransaction();
    }

    /**
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->driver()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->driver()->commit();
    }

    /**
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->driver()->rollBack();
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return int
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->driver()->execute($sql, $params);
    }

    /**
     * @param string|null $sequenceId
     *
     * @return string
     */
    public function lastInsertId(string $sequenceId = null): string
    {
        if ($sequenceId !== null) {
            return $this->driver()->lastInsertId($sequenceId);
        }

        return $this->driver()->lastInsertId();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function quote(string $value): string
    {
        return $this->driver()->quote($value);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[]
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        return $this->driver()->fetch($sql, $params);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->driver()->fetchAll($sql, $params);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]|\Generator
     */
    public function fetchYield(string $sql, array $params = []): \Generator
    {
        yield from $this->driver()->fetchYield($sql, $params);
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return string
     */
    public function column(string $sql, array $params = [], string $column = '0')
    {
        return $this->driver()->column($sql, $params, $column);
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return string[]
     */
    public function columnAll(string $sql, array $params = [], string $column = '0'): array
    {
        return $this->driver()->columnAll($sql, $params, $column);
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return mixed|\Generator
     */
    public function columnYield(string $sql, array $params = [], string $column = '0'): \Generator
    {
        yield from $this->driver()->columnYield($sql, $params, $column);
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $key
     * @param string $value
     *
     * @return string[]
     */
    public function pairs(string $sql, array $params = [], string $key = '0', string $value = '1'): array
    {
        return $this->driver()->pairs($sql, $params, $key, $value);
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $key
     * @param string $value
     *
     * @return mixed|\Generator
     */
    public function pairsYield(string $sql, array $params = [], string $key = '0', string $value = '1'): \Generator
    {
        yield from $this->driver()->pairsYield($sql, $params, $key, $value);
    }

    /**
     * @return DialectStrategy
     */
    public function dialect(): DialectStrategy
    {
        return $this->driver()->dialect();
    }

    /**
     * @param string $tableName
     *
     * @return int
     */
    public function truncate(string $tableName): int
    {
        return $this->driver()->truncate($tableName);
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function listen(callable $callable)
    {
        $this->driver()->listen($callable);

        return $this;
    }

    /**
     * @param string $tableName
     * @param bool   $force
     *
     * @return array
     */
    public function describe(string $tableName, bool $force = false): array
    {
        return $this->driver()->describe($tableName, $force);
    }

    /**
     * @return SelectQuery
     */
    public function select(): SelectQuery
    {
        return $this->driver()->select();
    }

    /**
     * @return InsertQuery
     */
    public function insert(): InsertQuery
    {
        return $this->driver()->insert();
    }

    /**
     * @return DeleteQuery
     */
    public function delete(): DeleteQuery
    {
        return $this->driver()->delete();
    }

    /**
     * @return UpdateQuery
     */
    public function update(): UpdateQuery
    {
        return $this->driver()->update();
    }

    /**
     * @return FromClause
     */
    public function from(): FromClause
    {
        return $this->driver()->from();
    }

    /**
     * @return JoinClause
     */
    public function join(): JoinClause
    {
        return $this->driver()->join();
    }

    /**
     * @return WhereClause
     */
    public function where(): WhereClause
    {
        return $this->driver()->where();
    }

    /**
     * @return HavingClause
     */
    public function having(): HavingClause
    {
        return $this->driver()->having();
    }

    /**
     * @return OrderByClause
     */
    public function orderBy(): OrderByClause
    {
        return $this->driver()->orderBy();
    }

    /**
     * @return GroupByClause
     */
    public function groupBy(): GroupByClause
    {
        return $this->driver()->groupBy();
    }

    /**
     * @return LimitClause
     */
    public function limit(): LimitClause
    {
        return $this->driver()->limit();
    }

    /**
     * @return OffsetClause
     */
    public function offset(): OffsetClause
    {
        return $this->driver()->offset();
    }
}
