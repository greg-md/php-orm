<?php

namespace Greg\Orm;

use Greg\Orm\Clause\ClauseStrategy;
use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\OffsetClauseStrategy;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Table\DeleteTableQueryTrait;
use Greg\Orm\Table\FromTableClauseTrait;
use Greg\Orm\Table\GroupByTableClauseTrait;
use Greg\Orm\Table\HavingTableClauseTrait;
use Greg\Orm\Table\InsertTableQueryTrait;
use Greg\Orm\Table\JoinTableClauseTrait;
use Greg\Orm\Table\LimitTableClauseTrait;
use Greg\Orm\Table\OffsetTableClauseTrait;
use Greg\Orm\Table\OrderByTableClauseTrait;
use Greg\Orm\Table\SelectTableQueryTrait;
use Greg\Orm\Table\UpdateTableQueryTrait;
use Greg\Orm\Table\WhereTableClauseTrait;

trait QueryBuilderTrait
{
    use DeleteTableQueryTrait,
        InsertTableQueryTrait,
        SelectTableQueryTrait,
        UpdateTableQueryTrait,

        FromTableClauseTrait,
        GroupByTableClauseTrait,
        HavingTableClauseTrait,
        JoinTableClauseTrait,
        LimitTableClauseTrait,
        OffsetTableClauseTrait,
        OrderByTableClauseTrait,
        WhereTableClauseTrait;

    /**
     * @var QueryStrategy|null
     */
    private $query;

    /**
     * @var ClauseStrategy[]
     */
    private $clauses = [];

    public function query(): QueryStrategy
    {
        if (!$this->query) {
            throw new SqlException('Query was not defined.');
        }

        return $this->query;
    }

    public function hasQuery(): bool
    {
        return (bool) $this->query;
    }

    /**
     * @param QueryStrategy $query
     * @return $this
     */
    public function setQuery(QueryStrategy $query)
    {
        $this->query = $query;

        $this->clearClauses();

        return $this;
    }

    public function getQuery(): ?QueryStrategy
    {
        return $this->query;
    }

    /**
     * @return $this
     */
    public function clearQuery()
    {
        $this->query = null;

        return $this;
    }

    public function clause(string $name): ClauseStrategy
    {
        if (!isset($this->clauses[$name])) {
            throw new SqlException('Clause ' . $name . ' was not defined.');
        }

        return $this->clauses[$name];
    }

    public function hasClause(string $name): bool
    {
        return isset($this->clauses[$name]);
    }

    /**
     * @param string $name
     * @param ClauseStrategy $query
     * @return $this
     */
    public function setClause(string $name, ClauseStrategy $query)
    {
        $this->clauses[$name] = $query;

        return $this;
    }

    public function getClause(string $name): ?ClauseStrategy
    {
        return $this->clauses[$name] ?? null;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function clearClause(string $name)
    {
        unset($this->clauses[$name]);

        return $this;
    }

    public function hasClauses(): bool
    {
        return (bool) $this->clauses;
    }

    public function getClauses(): array
    {
        return $this->clauses;
    }

    /**
     * @return $this
     */
    public function clearClauses()
    {
        $this->clauses = [];

        return $this;
    }

    /**
     * @param bool $condition
     * @param callable $callable
     * @return $this
     */
    public function when(bool $condition, callable $callable)
    {
        if ($condition) {
            call_user_func_array($callable, [$this]);
        }

        return $this;
    }

    public function toSql(): array
    {
        if ($this->clauses and !$this->query) {
            return $this->clausesToSql();
        }

        $query = clone $this->query();

        if ($query instanceof FromClauseStrategy) {
            $this->assignFromAppliers($query);
        }

        if ($query instanceof JoinClauseStrategy) {
            $this->assignJoinAppliers($query);
        }

        if ($query instanceof WhereClauseStrategy) {
            $this->assignWhereAppliers($query);
        }

        if ($query instanceof HavingClauseStrategy) {
            $this->assignHavingAppliers($query);
        }

        if ($query instanceof OrderByClauseStrategy) {
            $this->assignOrderByAppliers($query);
        }

        if ($query instanceof GroupByClauseStrategy) {
            $this->assignGroupByAppliers($query);
        }

        if ($query instanceof LimitClauseStrategy) {
            $this->assignLimitAppliers($query);
        }

        if ($query instanceof OffsetClauseStrategy) {
            $this->assignOffsetAppliers($query);
        }

        return $query->toSql();
    }

    public function toString(): string
    {
        return $this->toSql()[0];
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    protected function clausesToSql(): array
    {
        $sql = $params = [];

        if ($clause = $this->hasFromAppliers() ? $this->intoFromStrategy()->getFromClause() : $this->getFromClause()) {
            $clause = clone $clause;

            $this->assignFromAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasJoinAppliers() ? $this->intoJoinStrategy()->joinClause() : $this->getJoinClause()) {
            $clause = clone $clause;

            $this->assignJoinAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasWhereAppliers() ? $this->intoWhereStrategy()->whereClause() : $this->getWhereClause()) {
            $clause = clone $clause;

            $this->assignWhereAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasGroupByAppliers() ? $this->intoGroupByStrategy()->groupByClause() : $this->getGroupByClause()) {
            $clause = clone $clause;

            $this->assignGroupByAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasHavingAppliers() ? $this->intoHavingStrategy()->havingClause() : $this->getHavingClause()) {
            $clause = clone $clause;

            $this->assignHavingAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasOrderByAppliers() ? $this->intoOrderByStrategy()->orderByClause() : $this->getOrderByClause()) {
            $clause = clone $clause;

            $this->assignOrderByAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        $sql = implode(' ', $sql);

        if ($clause = $this->hasLimitAppliers() ? $this->intoLimitStrategy()->limitClause() : $this->getLimitClause()) {
            $clause = clone $clause;

            $this->assignLimitAppliers($clause);

            $sql = $this->driver()->dialect()->limit($sql, $clause->getLimit());
        }

        if ($clause = $this->hasOffsetAppliers() ? $this->intoOffsetStrategy()->offsetClause() : $this->getOffsetClause()) {
            $clause = clone $clause;

            $this->assignOffsetAppliers($clause);

            $sql = $this->driver()->dialect()->offset($sql, $clause->getOffset());
        }

        return [$sql, $params];
    }
}
