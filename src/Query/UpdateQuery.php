<?php

namespace Greg\Orm\Query;

use Greg\Orm\Clause\JoinClauseTrait;
use Greg\Orm\Clause\LimitClauseTrait;
use Greg\Orm\Clause\OrderByClauseTrait;
use Greg\Orm\Clause\WhereClauseTrait;
use Greg\Orm\QueryException;
use Greg\Orm\WhenTrait;

abstract class UpdateQuery implements UpdateQueryStrategy
{
    use JoinClauseTrait,
        WhereClauseTrait,
        OrderByClauseTrait,
        LimitClauseTrait,
        WhenTrait;

    /**
     * @var array[]
     */
    private $tables = [];

    /**
     * @var array[]
     */
    private $set = [];

    /**
     * @param $table
     * @param array ...$tables
     *
     * @throws QueryException
     *
     * @return $this
     */
    public function table($table, ...$tables)
    {
        array_unshift($tables, $table);

        foreach ($tables as $table) {
            list($tableAlias, $tableName) = $this->parseAlias($table);

            if (!is_scalar($tableName)) {
                throw new QueryException('Derived tables are not supported in UPDATE statement.');
            }

            $tableKey = $tableAlias ?: $tableName;

            $tableName = $this->quoteTableSql($tableName);

            if ($tableAlias) {
                $tableAlias = $this->quoteName($tableAlias);
            }

            $this->tables[] = [
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
        $this->setLogic($this->quoteNameSql($column) . ' = ?', [$value]);

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
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function setRaw(string $sql, string ...$params)
    {
        $this->setLogic($this->quoteSql($sql), $params);

        return $this;
    }

    /**
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function increment(string $column, int $value = 1)
    {
        $column = $this->quoteNameSql($column);

        $this->setLogic($column . ' = ' . $column . ' + ?', [$value]);

        return $this;
    }

    /**
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function decrement(string $column, int $value = 1)
    {
        $column = $this->quoteNameSql($column);

        $this->setLogic($column . ' = ' . $column . ' - ?', [$value]);

        return $this;
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

    /**
     * @throws QueryException
     *
     * @return array
     */
    protected function updateClauseToSql()
    {
        if (!$this->tables) {
            throw new QueryException('Undefined tables in UPDATE statement.');
        }

        $sql = $params = [];

        foreach ($this->tables as $table) {
            $sqlPart = $table['table'];

            if ($table['alias']) {
                $sqlPart .= ' AS ' . $table['alias'];
            }

            list($joinsSql, $joinsParams) = $this->joinToSql($table['tableKey']);

            if ($joinsSql) {
                $sqlPart .= ' ' . $joinsSql;

                $params = array_merge($params, $joinsParams);
            }

            $sql[] = $sqlPart;
        }

        $sql = 'UPDATE ' . implode(', ', $sql);

        return [$sql, $params];
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    protected function addLimitToSql(string $sql): string
    {
        if ($limit = $this->getLimit()) {
            $sql .= ' LIMIT ' . $limit;
        }

        return $sql;
    }

    /**
     * @throws QueryException
     *
     * @return array
     */
    protected function setClauseToSql()
    {
        if (!$this->set) {
            throw new QueryException('Undefined SET statement in UPDATE statement.');
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

    /**
     * @return array
     */
    protected function updateToSql(): array
    {
        list($sql, $params) = $this->updateClauseToSql();

        $sql = [$sql];

        list($joinsSql, $joinsParams) = $this->joinToSql();

        if ($joinsSql) {
            $sql[] = $joinsSql;

            $params = array_merge($params, $joinsParams);
        }

        list($setSql, $setParams) = $this->setClauseToSql();

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

        $sql = $this->addLimitToSql(implode(' ', $sql));

        return [$sql, $params];
    }

    /**
     * @return string
     */
    protected function updateToString(): string
    {
        return $this->updateToSql()[0];
    }

    /**
     * @return array
     */
    public function toSql(): array
    {
        return $this->updateToSql();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->updateToString();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    abstract protected function quoteSql(string $sql): string;

    /**
     * @param string $name
     *
     * @return string
     */
    abstract protected function quoteNameSql(string $name): string;

    /**
     * @param string $sql
     *
     * @return string
     */
    abstract protected function quoteTableSql(string $sql): string;
}
