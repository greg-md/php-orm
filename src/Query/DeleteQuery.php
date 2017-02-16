<?php

namespace Greg\Orm\Query;

use Greg\Orm\Clause\FromClauseTrait;
use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\LimitClauseTrait;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\OrderByClauseTrait;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\WhereClauseTrait;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\QueryException;
use Greg\Orm\SqlAbstract;

class DeleteQuery extends SqlAbstract implements
    QueryStrategy,
    FromClauseStrategy,
    WhereClauseStrategy,
    OrderByClauseStrategy,
    LimitClauseStrategy
{
    use FromClauseTrait,
        WhereClauseTrait,
        OrderByClauseTrait,
        LimitClauseTrait;

    /**
     * @var array
     */
    private $rowsFrom = [];

    /**
     * @param string    $table
     * @param \string[] ...$tables
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
    public function toSql(): array
    {
        return $this->deleteToSql();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->deleteToString();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    public function __clone()
    {
        $this->whereClone();
    }

    /**
     * @return array
     */
    protected function deleteClauseToSql()
    {
        $params = [];

        $sql = ['DELETE'];

        if ($this->rowsFrom) {
            $sql[] = implode(', ', $this->rowsFrom);
        }

        $sql = implode(' ', $sql);

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
    protected function deleteToSql()
    {
        list($sql, $params) = $this->deleteClauseToSql();

        $sql = [$sql];

        list($fromSql, $fromParams) = $this->fromToSql();

        if (!$fromSql) {
            throw new QueryException('Undefined DELETE FROM clause.');
        }

        $sql[] = $fromSql;

        $params = array_merge($params, $fromParams);

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
    protected function deleteToString()
    {
        return $this->deleteToSql()[0];
    }
}
