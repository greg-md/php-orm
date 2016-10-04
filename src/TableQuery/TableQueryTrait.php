<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverInterface;
use Greg\Orm\Query\ClauseInterface;
use Greg\Orm\Query\QueryInterface;

trait TableQueryTrait
{
    /**
     * @var QueryInterface
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

    public function setQuery(QueryInterface $query)
    {
        $this->query = $query;

        return $this;
    }

    public function hasClauses()
    {
        return (bool) $this->clauses;
    }

    public function hasClause($clause)
    {
        return isset($this->clauses[$clause]);
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

    public function setClause($clause, ClauseInterface $query)
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
        return $this->getDriver()->concat($values, $delimiter);
    }

    public function quoteLike($value, $escape = '\\')
    {
        return $this->getDriver()->quoteLike($value, $escape);
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

    public function __toString()
    {
        return (string) $this->toString();
    }

    protected function prepareQuery(QueryInterface $query)
    {
        list($sql, $params) = $query->toSql();

        $stmt = $this->getDriver()->prepare($sql);

        if ($params) {
            $stmt->bindParams($params);
        }

        return $stmt;
    }

    protected function executeQuery(QueryInterface $query)
    {
        $stmt = $this->prepareQuery($query);

        $stmt->execute();

        return $stmt;
    }

    protected function execQuery(QueryInterface $query)
    {
        return $this->prepareQuery($query)->execute();
    }

    public function prepare()
    {
        return $this->prepareQuery($this->getQuery());
    }

    /**
     * @return DriverInterface
     */
    abstract public function getDriver();
}
