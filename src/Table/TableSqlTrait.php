<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\ClauseStrategy;
use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OffsetClauseStrategy;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Driver\StatementStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

trait TableSqlTrait
{
    /**
     * @var QueryStrategy|null
     */
    private $query;

    private $clauses = [];

    public function query(): QueryStrategy
    {
        if (!$this->query) {
            throw new QueryException('Query was not defined.');
        }

        return $this->query;
    }

    public function setQuery(QueryStrategy $query)
    {
        $this->query = $query;

        $this->clearClauses();

        return $this;
    }

    public function hasQuery(): bool
    {
        return (bool) $this->query;
    }

    public function getQuery(): ?QueryStrategy
    {
        return $this->query;
    }

    public function clearQuery()
    {
        $this->query = null;

        return $this;
    }

    public function clause(string $name): ClauseStrategy
    {
        if (!isset($this->clauses[$name])) {
            throw new QueryException('Clause ' . $name . ' was not defined.');
        }

        return $this->clauses[$name];
    }

    public function setClause(string $name, ClauseStrategy $query)
    {
        $this->clauses[$name] = $query;

        return $this;
    }

    public function hasClauses(): bool
    {
        return (bool) $this->clauses;
    }

    public function hasClause(string $name): bool
    {
        return isset($this->clauses[$name]);
    }

    public function getClauses(): array
    {
        return $this->clauses;
    }

    public function getClause(string $name): ?ClauseStrategy
    {
        return $this->clauses[$name] ?? null;
    }

    public function clearClauses()
    {
        $this->clauses = [];

        return $this;
    }

    public function clearClause(string $name)
    {
        unset($this->clauses[$name]);

        return $this;
    }

    public function when(bool $condition, callable $callable)
    {
        $this->query()->when($condition, $callable);

        return $this;
    }

    public function prepare(): StatementStrategy
    {
        return $this->prepareQuery($this->query());
    }

    public function execute(): StatementStrategy
    {
        return $this->executeQuery($this->query());
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

    protected function clausesToSql()
    {
        $sql = $params = [];

        if ($clause = $this->getFromClause()) {
            $clause = clone $clause;

            $this->assignFromAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->getJoinClause()) {
            $clause = clone $clause;

            $this->assignJoinAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->getWhereClause()) {
            $clause = clone $clause;

            $this->assignWhereAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->getHavingClause()) {
            $clause = clone $clause;

            $this->assignHavingAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->getOrderByClause()) {
            $clause = clone $clause;

            $this->assignOrderByAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->getGroupByClause()) {
            $clause = clone $clause;

            $this->assignGroupByAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        $sql = implode(' ', $sql);

        if ($clause = $this->getLimitClause()) {
            $clause = clone $clause;

            $this->assignLimitAppliers($clause);

            $sql = $this->driver()->dialect()->addLimitToSql($sql, $clause->getLimit());
        }

        if ($clause = $this->getOffsetClause()) {
            $clause = clone $clause;

            $this->assignOffsetAppliers($clause);

            $sql = $this->driver()->dialect()->addOffsetToSql($sql, $clause->getOffset());
        }

        return [$sql, $params];
    }

    /**
     * @todo Need to reset rows.
     *
     * @return $this
     */
    protected function sqlClone()
    {
        return clone $this;
    }

    protected function prepareQuery(QueryStrategy $query): StatementStrategy
    {
        list($sql, $params) = $query->toSql();

        $stmt = $this->driver()->prepare($sql);

        if ($params) {
            $stmt->bindParams($params);
        }

        return $stmt;
    }

    protected function executeQuery(QueryStrategy $query): StatementStrategy
    {
        $stmt = $this->prepareQuery($query);

        $stmt->execute();

        return $stmt;
    }

    protected function getFromClause(): ?FromClause
    {
        /** @var FromClause $clause */
        $clause = $this->getClause('FROM');

        return $clause;
    }

    protected function getJoinClause(): ?JoinClause
    {
        /** @var JoinClause $clause */
        $clause = $this->getClause('JOIN');

        return $clause;
    }

    protected function getWhereClause(): ?WhereClause
    {
        /** @var WhereClause $clause */
        $clause = $this->getClause('WHERE');

        return $clause;
    }

    protected function getHavingClause(): ?HavingClause
    {
        /** @var HavingClause $clause */
        $clause = $this->getClause('HAVING');

        return $clause;
    }

    protected function getOrderByClause(): ?OrderByClause
    {
        /** @var OrderByClause $clause */
        $clause = $this->getClause('ORDER_BY');

        return $clause;
    }

    protected function getGroupByClause(): ?GroupByClause
    {
        /** @var GroupByClause $clause */
        $clause = $this->getClause('GROUP_BY');

        return $clause;
    }

    protected function getLimitClause(): ?LimitClause
    {
        /** @var LimitClause $clause */
        $clause = $this->getClause('LIMIT');

        return $clause;
    }

    protected function getOffsetClause(): ?OffsetClause
    {
        /** @var OffsetClause $clause */
        $clause = $this->getClause('OFFSET');

        return $clause;
    }

    abstract public function assignFromAppliers(FromClauseStrategy $strategy);

    abstract public function assignJoinAppliers(JoinClauseStrategy $strategy);

    abstract public function assignWhereAppliers(WhereClauseStrategy $strategy);

    abstract public function assignHavingAppliers(HavingClauseStrategy $strategy);

    abstract public function assignOrderByAppliers(OrderByClauseStrategy $strategy);

    abstract public function assignGroupByAppliers(GroupByClauseStrategy $strategy);

    abstract public function assignLimitAppliers(LimitClauseStrategy $strategy);

    abstract public function assignOffsetAppliers(OffsetClauseStrategy $strategy);

    abstract public function driver(): DriverStrategy;
}
