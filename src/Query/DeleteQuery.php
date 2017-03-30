<?php

namespace Greg\Orm\Query;

use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\FromClauseTrait;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\JoinClauseTrait;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\LimitClauseTrait;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\OrderByClauseTrait;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Clause\WhereClauseTrait;
use Greg\Orm\SqlAbstract;
use Greg\Orm\SqlException;

class DeleteQuery extends SqlAbstract implements
    QueryStrategy,
    FromClauseStrategy,
    JoinClauseStrategy,
    WhereClauseStrategy,
    OrderByClauseStrategy,
    LimitClauseStrategy
{
    use FromClauseTrait,
        JoinClauseTrait,
        WhereClauseTrait,
        OrderByClauseTrait,
        LimitClauseTrait;

    /**
     * @var array
     */
    private $rowsFrom = [];

    /**
     * @param string    $table
     * @param string[] ...$tables
     *
     * @return $this
     */
    public function rowsFrom(string $table, string ...$tables)
    {
        array_unshift($tables, $table);

        foreach ($tables as $table) {
            $this->rowsFrom[] = $this->dialect()->quoteName($table);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRowsFrom(): bool
    {
        return (bool) $this->rowsFrom;
    }

    /**
     * @return array
     */
    public function getRowsFrom(): array
    {
        return $this->rowsFrom;
    }

    /**
     * @return $this
     */
    public function clearRowsFrom()
    {
        $this->rowsFrom = [];

        return $this;
    }

    /**
     * @return array
     */
    public function deleteToSql(): array
    {
        $params = [];

        $sql = ['DELETE'];

        if ($this->rowsFrom) {
            $sql[] = implode(', ', $this->rowsFrom);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    public function deleteToString(): string
    {
        return $this->deleteToSql()[0];
    }

    public function toSql(): array
    {
        list($sql, $params) = $this->deleteToSql();

        $sql = [$sql];

        list($fromSql, $fromParams) = $this->fromToSql($this);

        if (!$fromSql) {
            throw new SqlException('Undefined DELETE FROM clause.');
        }

        $sql[] = $fromSql;

        $params = array_merge($params, $fromParams);

        list($joinSql, $joinParams) = $this->joinToSql();

        if ($joinSql) {
            $sql[] = $joinSql;

            $params = array_merge($params, $joinParams);
        }

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
            $sql = $this->dialect()->addLimitToSql($sql, $limit);
        }

        return [$sql, $params];
    }

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
}
