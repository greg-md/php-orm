<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\InsertQueryInterface;
use Greg\Orm\Query\QueryTraitInterface;
use Greg\Orm\Query\UpdateQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableQueryTrait
{
    protected $query = null;

    protected $clauses = [];

    protected function cleanClauses()
    {
        $this->clauses = [];

        return $this;
    }

    /**
     * @return QueryTraitInterface
     * @throws \Exception
     */
    protected function needQuery()
    {
        if (!($this->query instanceof QueryTraitInterface)) {
            throw new \Exception('Current query is not a query.');
        }

        return $this->query;
    }

    public function concat(array $values, $delimiter = '')
    {
        return $this->getStorage()->concat($values, $delimiter);
    }

    public function quoteLike($value, $escape = '\\')
    {
        return $this->getStorage()->quoteLike($value, $escape);
    }

    public function when($condition, callable $callable)
    {
        return $this->needQuery()->when($condition, $callable);
    }

    public function toSql()
    {
        return $this->needQuery()->toSql();
    }

    public function toString()
    {
        return $this->needQuery()->toString();
    }

    public function prepare()
    {
        list($sql, $params) = $this->toSql();

        $stmt = $this->getStorage()->prepare($sql);

        if ($params) {
            $stmt->bindParams($params);
        }

        return $stmt;
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

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}