<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\InsertQueryInterface;
use Greg\Orm\Query\QueryTraitInterface;
use Greg\Orm\Query\UpdateQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableQueryTrait
{
    /**
     * @var QueryTraitInterface
     */
    protected $query = null;

    protected $clauses = [];

    public function getQuery()
    {
        if (!$this->query) {
            throw new \Exception('Query was not defined.');
        }

        return $this->query;
    }

    public function setQuery(QueryTraitInterface $query)
    {
        $this->query = $query;

        return $this;
    }

    public function hasClauses()
    {
        return (bool)$this->clauses;
    }

    public function getClauses()
    {
        return $this->clauses;
    }

    public function addClauses(array $clauses)
    {
        $this->clauses = array_merge($this->clauses, $clauses);

        return $this;
    }

    public function setClauses(array $clauses)
    {
        $this->clauses = $clauses;

        return $this;
    }

    public function clearClauses()
    {
        $this->clauses = [];

        return $this;
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
        return $this->getQuery()->when($condition, $callable);
    }

    public function toSql()
    {
        return $this->getQuery()->toSql();
    }

    public function toString()
    {
        return $this->getQuery()->toString();
    }

    protected function prepareQuery(QueryTraitInterface $query)
    {
        list($sql, $params) = $query->toSql();

        $stmt = $this->getStorage()->prepare($sql);

        if ($params) {
            $stmt->bindParams($params);
        }

        return $stmt;
    }

    /**
     * @param QueryTraitInterface $query
     * @return StmtInterface
     */
    protected function executeQuery(QueryTraitInterface $query)
    {
        return $this->prepareQuery($query)->execute();
    }

    public function prepare()
    {
        return $this->prepareQuery($this->getQuery());
    }

    public function execute()
    {
        return $this->executeQuery($this->getQuery());
    }

    public function exec()
    {
        $query = $this->getQuery();

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