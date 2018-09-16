<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

trait FromTableClauseTrait
{
    use TableClauseTrait;

    private $fromAppliers = [];

    /**
     * @param FromClauseStrategy $strategy
     *
     * @return $this
     */
    public function assignFromAppliers(FromClauseStrategy $strategy)
    {
        if ($this->fromAppliers) {
            $items = $strategy->getFrom();

            $strategy->clearFrom();

            foreach ($this->fromAppliers as $applier) {
                $clause = $this->connection()->from();

                call_user_func_array($applier, [$clause]);

                foreach ($clause->getFrom() as $from) {
                    $strategy->fromLogic($from['tableKey'], $from['table'], $from['alias'], $from['params']);
                }
            }

            foreach ($items as $from) {
                $strategy->fromLogic($from['tableKey'], $from['table'], $from['alias'], $from['params']);
            }
        }

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function setFromApplier(callable $callable)
    {
        $this->fromAppliers[] = $callable;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasFromAppliers(): bool
    {
        return (bool) $this->fromAppliers;
    }

    /**
     * @return callable[]
     */
    public function getFromAppliers(): array
    {
        return $this->fromAppliers;
    }

    /**
     * @param array $appliers
     *
     * @return $this
     */
    public function setFromAppliers(array $appliers)
    {
        $this->fromAppliers = $appliers;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearFromAppliers()
    {
        $this->fromAppliers = [];

        return $this;
    }

    /**
     * @param $table
     * @param array ...$tables
     *
     * @return $this
     */
    public function from($table, ...$tables)
    {
        $instance = $this->fromStrategyInstance();

        $instance->fromStrategy()->from($table, ...$tables);

        return $instance;
    }

    /**
     * @param null|string $alias
     * @param string      $sql
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function fromRaw(?string $alias, string $sql, string ...$params)
    {
        $instance = $this->fromStrategyInstance();

        $instance->fromStrategy()->fromRaw($alias, $sql, ...$params);

        return $instance;
    }

    /**
     * @return bool
     */
    public function hasFrom(): bool
    {
        if ($clause = $this->getFromStrategy()) {
            return $clause->hasFrom();
        }

        return false;
    }

    /**
     * @return array
     */
    public function getFrom(): array
    {
        if ($clause = $this->getFromStrategy()) {
            return $clause->getFrom();
        }

        return [];
    }

    /**
     * @return $this
     */
    public function clearFrom()
    {
        if ($clause = $this->getFromStrategy()) {
            return $clause->clearFrom();
        }

        return $this;
    }

    public function fromToSql(?JoinClauseStrategy $join = null, bool $useClause = true): array
    {
        if ($clause = $this->getFromStrategy()) {
            return $clause->fromToSql($join, $useClause);
        }

        return ['', []];
    }

    public function fromToString(?JoinClauseStrategy $join = null, bool $useClause = true): string
    {
        return $this->fromToSql($join, $useClause)[0];
    }

    public function fromClause(): FromClause
    {
        /** @var FromClause $clause */
        $clause = $this->clause('FROM');

        return $clause;
    }

    public function hasFromClause(): bool
    {
        return $this->hasClause('FROM');
    }

    public function getFromClause(): ?FromClause
    {
        /** @var FromClause $clause */
        $clause = $this->getClause('FROM');

        return $clause;
    }

    public function fromStrategy(): FromClauseStrategy
    {
        /** @var QueryStrategy|FromClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needFromStrategyInQuery($query);

            return $query;
        }

        return $this->fromClause();
    }

    public function getFromStrategy(): ?FromClauseStrategy
    {
        /** @var QueryStrategy|FromClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needFromStrategyInQuery($query);

            return $query;
        }

        return $this->getFromClause();
    }

    /**
     * @return $this
     */
    public function intoFromStrategy()
    {
        if (!$this->hasClause('FROM')) {
            $this->setClause('FROM', $this->connection()->from());
        }

        return $this;
    }

    protected function fromStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needFromStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this->intoFromStrategy();
        }

        return $this->cleanClone()->setClause('FROM', $this->connection()->from());
    }

    protected function needFromStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof FromClauseStrategy)) {
            throw new SqlException('Current query does not have a FROM clause.');
        }

        return $this;
    }

    protected function getPreparedFromClause()
    {
        if ($this->hasFromAppliers()) {
            $clause = clone $this->intoFromStrategy()->fromClause();

            $this->assignFromAppliers($clause);
        } else {
            $clause = $this->getFromClause();
        }

        return $clause;
    }
}
