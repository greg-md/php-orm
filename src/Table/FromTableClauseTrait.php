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

    public function assignFromAppliers(FromClauseStrategy $strategy)
    {
        if ($this->fromAppliers) {
            $items = $strategy->getFrom();

            $strategy->clearFrom();

            foreach ($this->fromAppliers as $applier) {
                $clause = $this->driver()->from();

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

    public function fromRaw(?string $alias, string $sql, string ...$params)
    {
        $instance = $this->fromStrategyInstance();

        $instance->fromStrategy()->fromRaw($alias, $sql, ...$params);

        return $instance;
    }

    public function hasFrom(): bool
    {
        if ($clause = $this->getFromStrategy()) {
            return $clause->hasFrom();
        }

        return false;
    }

    public function getFrom(): array
    {
        if ($clause = $this->getFromStrategy()) {
            return $clause->getFrom();
        }

        return [];
    }

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

    public function intoFromStrategy()
    {
        if (!$this->hasClause('FROM')) {
            $this->setClause('FROM', $this->driver()->from());
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

        return $this->cleanClone()->setClause('FROM', $this->driver()->from());
    }

    protected function needFromStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof FromClauseStrategy)) {
            throw new SqlException('Current query does not have a FROM clause.');
        }

        return $this;
    }
}
