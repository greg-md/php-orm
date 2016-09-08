<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Query\QueryTraitInterface;
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

    public function getClause($clause)
    {
        if (!isset($this->clauses[$clause])) {
            throw new \Exception('Clause ' . $clause . ' was not defined.');
        }

        return $this->clauses[$clause];
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

    public function setClause($clause, QueryTraitInterface $query)
    {
        $this->clauses[$clause] = $query;

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
        $stmt = $this->prepareQuery($query);

        $stmt->execute();

        return $stmt;
    }

    /**
     * @param QueryTraitInterface $query
     * @return StmtInterface
     */
    protected function execQuery(QueryTraitInterface $query)
    {
        return $this->prepareQuery($query)->execute();
    }

    public function prepare()
    {
        return $this->prepareQuery($this->getQuery());
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}