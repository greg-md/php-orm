<?php

namespace Greg\Orm\Connection;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;

class ConnectionManager implements ConnectionStrategy
{
    private $connections = [];

    private $defaultConnectionName;

    /**
     * @param string $name
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setDefaultConnectionName(string $name)
    {
        if (!isset($this->connections[$name])) {
            throw new \Exception('Connection `' . $name . '` was not defined.');
        }

        $this->defaultConnectionName = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDefaultConnectionName(): ?string
    {
        return $this->defaultConnectionName;
    }

    public function actAs(string $connectionName)
    {
        $this->setDefaultConnectionName($connectionName);

        return $this;
    }

    /**
     * @param $name
     * @param callable $callable
     * @param bool     $default
     *
     * @return $this
     */
    public function register($name, callable $callable, bool $default = false)
    {
        $this->connections[$name] = $callable;

        if ($default) {
            $this->setDefaultConnectionName($name);
        }

        return $this;
    }

    /**
     * @param $name
     * @param ConnectionStrategy $strategy
     * @param bool       $default
     *
     * @return $this
     */
    public function registerStrategy($name, ConnectionStrategy $strategy, bool $default = false)
    {
        $this->connections[$name] = $strategy;

        if ($default) {
            $this->setDefaultConnectionName($name);
        }

        return $this;
    }

    public function connection(?string $name = null): ConnectionStrategy
    {
        if (!$name = $name ?: $this->defaultConnectionName) {
            throw new \Exception('Default connection was not defined.');
        }

        if (!$strategy = $this->connections[$name] ?? null) {
            throw new \Exception('Connection `' . $name . '` was not defined.');
        }

        if (is_callable($strategy)) {
            $strategy = call_user_func_array($strategy, []);

            if (!($strategy instanceof ConnectionStrategy)) {
                throw new \Exception('Connection `' . $name . '` must be an instance of `' . ConnectionStrategy::class . '`');
            }

            $this->connections[$name] = $strategy;
        }

        return $this->connections[$name];
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function transaction(callable $callable)
    {
        $this->connection()->transaction($callable);

        return $this;
    }

    /**
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->connection()->inTransaction();
    }

    /**
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->connection()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection()->commit();
    }

    /**
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->connection()->rollBack();
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return int
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->connection()->execute($sql, $params);
    }

    /**
     * @param string|null $sequenceId
     *
     * @return string
     */
    public function lastInsertId(string $sequenceId = null): string
    {
        if ($sequenceId !== null) {
            return $this->connection()->lastInsertId($sequenceId);
        }

        return $this->connection()->lastInsertId();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function quote(string $value): string
    {
        return $this->connection()->quote($value);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[]
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        return $this->connection()->fetch($sql, $params);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->connection()->fetchAll($sql, $params);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]|\Generator
     */
    public function generate(string $sql, array $params = []): \Generator
    {
        yield from $this->connection()->generate($sql, $params);
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
        return $this->connection()->column($sql, $params, $column);
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
        return $this->connection()->columnAll($sql, $params, $column);
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
        return $this->connection()->pairs($sql, $params, $key, $value);
    }

    /**
     * @return SqlDialectStrategy
     */
    public function dialect(): SqlDialectStrategy
    {
        return $this->connection()->dialect();
    }

    /**
     * @param string $tableName
     *
     * @return int
     */
    public function truncate(string $tableName): int
    {
        return $this->connection()->truncate($tableName);
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function listen(callable $callable)
    {
        $this->connection()->listen($callable);

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
        return $this->connection()->describe($tableName, $force);
    }

    /**
     * @return SelectQuery
     */
    public function select(): SelectQuery
    {
        return $this->connection()->select();
    }

    /**
     * @return InsertQuery
     */
    public function insert(): InsertQuery
    {
        return $this->connection()->insert();
    }

    /**
     * @return DeleteQuery
     */
    public function delete(): DeleteQuery
    {
        return $this->connection()->delete();
    }

    /**
     * @return UpdateQuery
     */
    public function update(): UpdateQuery
    {
        return $this->connection()->update();
    }

    /**
     * @return FromClause
     */
    public function from(): FromClause
    {
        return $this->connection()->from();
    }

    /**
     * @return JoinClause
     */
    public function join(): JoinClause
    {
        return $this->connection()->join();
    }

    /**
     * @return WhereClause
     */
    public function where(): WhereClause
    {
        return $this->connection()->where();
    }

    /**
     * @return HavingClause
     */
    public function having(): HavingClause
    {
        return $this->connection()->having();
    }

    /**
     * @return OrderByClause
     */
    public function orderBy(): OrderByClause
    {
        return $this->connection()->orderBy();
    }

    /**
     * @return GroupByClause
     */
    public function groupBy(): GroupByClause
    {
        return $this->connection()->groupBy();
    }

    /**
     * @return LimitClause
     */
    public function limit(): LimitClause
    {
        return $this->connection()->limit();
    }

    /**
     * @return OffsetClause
     */
    public function offset(): OffsetClause
    {
        return $this->connection()->offset();
    }
}
