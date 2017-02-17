<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

trait LimitTableClauseTrait
{
    use TableClauseTrait;

    private $limitAppliers = [];

    public function assignLimitAppliers(LimitClauseStrategy $strategy)
    {
        if ($this->limitAppliers and !$strategy->hasLimit()) {
            foreach ($this->limitAppliers as $applier) {
                $clause = $this->driver()->limit();

                call_user_func_array($applier, [$clause]);

                $strategy->limit($clause->getLimit());
            }
        }

        return $this;
    }

    public function setLimitApplier(callable $callable)
    {
        $this->limitAppliers[] = $callable;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLimitAppliers(): bool
    {
        return (bool) $this->limitAppliers;
    }

    /**
     * @return callable[]
     */
    public function getLimitAppliers(): array
    {
        return $this->limitAppliers;
    }

    public function clearLimitAppliers()
    {
        $this->limitAppliers = [];

        return $this;
    }

    public function limit(int $number)
    {
        $instance = $this->limitStrategyInstance();

        $instance->limitStrategy()->limit($number);

        return $instance;
    }

    public function hasLimit(): bool
    {
        if ($clause = $this->getLimitStrategy()) {
            return $clause->hasLimit();
        }

        return false;
    }

    public function getLimit(): ?int
    {
        if ($clause = $this->getLimitStrategy()) {
            return $clause->getLimit();
        }

        return null;
    }

    public function clearLimit()
    {
        if ($clause = $this->getLimitStrategy()) {
            $clause->clearLimit();
        }

        return $this;
    }

    public function getLimitStrategy(): ?LimitClauseStrategy
    {
        /** @var QueryStrategy|LimitClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needLimitStrategyInQuery($query);

            return $query;
        }

        /** @var LimitClause $clause */
        $clause = $this->getClause('LIMIT');

        return $clause;
    }

    public function limitStrategy(): LimitClauseStrategy
    {
        /** @var QueryStrategy|LimitClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needLimitStrategyInQuery($query);

            return $query;
        }

        if (!$clause = $this->getClause('LIMIT')) {
            $this->setClause('LIMIT', $clause = $this->driver()->limit());
        }

        return $clause;
    }

    protected function limitStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needLimitStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this;
        }

        return $this->sqlClone();
    }

    protected function needLimitStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof LimitClauseStrategy)) {
            throw new QueryException('Current query does not have a LIMIT clause.');
        }

        return $this;
    }

    protected function getLimitClause(): ?LimitClause
    {
        /** @var LimitClause $clause */
        $clause = $this->getClause('LIMIT');

        return $clause;
    }
}
