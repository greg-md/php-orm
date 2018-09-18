<?php

namespace Greg\Orm\Query;

use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\JoinClauseTrait;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\LimitClauseTrait;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\OrderByClauseTrait;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Clause\WhereClauseTrait;
use Greg\Orm\Connection\ConnectionStrategy;
use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\SqlAbstract;
use Greg\Orm\SqlException;

class UpdateQuery extends SqlAbstract implements
    QueryStrategy,
    JoinClauseStrategy,
    WhereClauseStrategy,
    OrderByClauseStrategy,
    LimitClauseStrategy
{
    use QueryTrait,
        JoinClauseTrait,
        WhereClauseTrait,
        OrderByClauseTrait,
        LimitClauseTrait;

    /**
     * @var array[]
     */
    private $tables = [];

    /**
     * @var array[]
     */
    private $set = [];

    public function __construct(SqlDialect $dialect = null, ConnectionStrategy $connection = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);

        if ($connection) {
            $this->setConnection($connection);
        }
    }

    /**
     * @param $table
     * @param array ...$tables
     *
     * @throws SqlException
     *
     * @return $this
     */
    public function table($table, ...$tables)
    {
        array_unshift($tables, $table);

        foreach ($tables as $table) {
            list($tableAlias, $tableName) = $this->dialect()->parseTable($table);

            if (!is_scalar($tableName)) {
                throw new SqlException('Derived tables are not supported in UPDATE statement.');
            }

            $tableKey = $tableAlias ?: $tableName;

            $tableName = $this->dialect()->quoteTable($tableName);

            if ($tableAlias) {
                $tableAlias = $this->dialect()->quoteName($tableAlias);
            }

            $this->tables[$tableKey] = [
                'tableKey' => $tableKey,
                'table'    => $tableName,
                'alias'    => $tableAlias,
            ];
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasTables(): bool
    {
        return (bool) $this->tables;
    }

    /**
     * @return array
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @return $this
     */
    public function clearTables()
    {
        $this->tables = [];

        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function set(string $column, string $value)
    {
        $this->setLogic($this->dialect()->quoteName($column) . ' = ?', [$value]);

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function setMultiple(array $columns)
    {
        foreach ($columns as $column => $value) {
            $this->set($column, $value);
        }

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function setRaw(string $sql, string ...$params)
    {
        $this->setLogic($this->dialect()->quote($sql), $params);

        return $this;
    }

    /**
     * @param string $column
     * @param int    $step
     *
     * @return $this
     */
    public function increment(string $column, int $step = 1)
    {
        $column = $this->dialect()->quoteName($column);

        $this->setLogic($column . ' = ' . $column . ' + ?', [$step]);

        return $this;
    }

    /**
     * @param string $column
     * @param int    $step
     *
     * @return $this
     */
    public function decrement(string $column, int $step = 1)
    {
        $column = $this->dialect()->quoteName($column);

        $this->setLogic($column . ' = ' . $column . ' - ?', [$step]);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSet(): bool
    {
        return (bool) $this->set;
    }

    /**
     * @return array
     */
    public function getSet(): array
    {
        return $this->set;
    }

    /**
     * @return $this
     */
    public function clearSet()
    {
        $this->set = [];

        return $this;
    }

    public function update(array $columns = []): int
    {
        if ($columns) {
            $this->setMultiple($columns);
        }

        [$sql, $params] = $this->toSql();

        return $this->connection()->execute($sql, $params);
    }

    public function updateToSql(): array
    {
        if (!$this->tables) {
            throw new SqlException('Undefined UPDATE table.');
        }

        $sql = $params = [];

        foreach ($this->tables as $table) {
            $sqlPart = $table['table'];

            if ($table['alias']) {
                $sqlPart .= ' AS ' . $table['alias'];
            }

            list($joinSql, $joinParams) = $this->joinToSql($table['tableKey']);

            if ($joinSql) {
                $sqlPart .= ' ' . $joinSql;

                $params = array_merge($params, $joinParams);
            }

            $sql[] = $sqlPart;
        }

        $sql = 'UPDATE ' . implode(', ', $sql);

        return [$sql, $params];
    }

    public function updateToString(): string
    {
        return $this->updateToSql()[0];
    }

    /**
     * @throws SqlException
     *
     * @return array
     */
    public function setToSql(): array
    {
        if (!$this->set) {
            throw new SqlException('Undefined UPDATE SET columns.');
        }

        $sql = $params = [];

        foreach ($this->set as $item) {
            $sqlPart = $item['sql'];

            $item['params'] && $params = array_merge($params, $item['params']);

            $sql[] = $sqlPart;
        }

        $sql = 'SET ' . implode(', ', $sql);

        return [$sql, $params];
    }

    public function setToString(): string
    {
        return $this->setToSql()[0];
    }

    /**
     * @return array
     */
    public function toSql(): array
    {
        list($sql, $params) = $this->updateToSql();

        $sql = [$sql];

        list($joinSql, $joinParams) = $this->joinToSql();

        if ($joinSql) {
            $sql[] = $joinSql;

            $params = array_merge($params, $joinParams);
        }

        list($setSql, $setParams) = $this->setToSql();

        $sql[] = $setSql;

        $params = array_merge($params, $setParams);

        list($whereSql, $whereParams) = $this->whereToSql();

        if ($whereSql) {
            $sql[] = $whereSql;

            $params = array_merge($params, $whereParams);
        }

        list($orderBySql, $orderByParams) = $this->orderByToSql();

        if ($orderBySql) {
            $sql[] = $orderBySql;

            $params = array_merge($params, $orderByParams);
        }

        $sql = implode(' ', $sql);

        if ($limit = $this->getLimit()) {
            $sql = $this->dialect()->limit($sql, $limit);
        }

        return [$sql, $params];
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->toSql()[0];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        try {
            return $this->toString();
        } catch (SqlException $e) {
            return $e->getMessage();
        }
    }

    public function __clone()
    {
        $this->whereClone();
    }

    /**
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    protected function setLogic($sql, array $params = [])
    {
        $this->set[] = [
            'sql'    => $sql,
            'params' => $params,
        ];

        return $this;
    }
}
