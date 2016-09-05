<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\InsertQueryInterface;
use Greg\Orm\Query\QueryTraitInterface;
use Greg\Orm\Query\UpdateQueryInterface;

trait TableQueryTrait
{
    protected $query = null;

    protected $clauses = [];

    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery(QueryTraitInterface $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return QueryTraitInterface
     * @throws \Exception
     */
    public function needQuery()
    {
        if (!($this->query instanceof QueryTraitInterface)) {
            throw new \Exception('Current query is not a query.');
        }

        return $this->query;
    }

    public function concat($array, $delimiter = '')
    {
        return $this->needQuery()->concat($array, $delimiter);
    }

    public function quoteLike($string, $escape = '\\')
    {
        return $this->needQuery()->quoteLike($string, $escape);
    }

    public function when($condition, callable $callable)
    {
        return $this->needQuery()->when($condition, $callable);
    }

    public function exec()
    {
        $query = $this->needQuery();

        if ($query instanceof InsertQueryInterface) {
            return $this->execInsert();
        }

        if ($query instanceof UpdateQueryInterface) {
            return $this->execUpdate();
        }

        if ($query instanceof DeleteQueryInterface) {
            return $this->execDelete();
        }

        throw new \Exception('Current query does not support exec method.');
    }

    public function stmt()
    {
        return $this->needQuery()->stmt();
    }

    public function toSql()
    {
        return $this->needQuery()->toSql();
    }

    public function toString()
    {
        return $this->needQuery()->toString();
    }
}