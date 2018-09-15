<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

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

    /**
     * @param int $number
     *
     * @return $this
     */
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

    public function limitClause(): LimitClause
    {
        /** @var LimitClause $clause */
        $clause = $this->clause('LIMIT');

        return $clause;
    }

    public function hasLimitClause(): bool
    {
        return $this->hasClause('LIMIT');
    }

    public function getLimitClause(): ?LimitClause
    {
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

        return $this->limitClause();
    }

    public function getLimitStrategy(): ?LimitClauseStrategy
    {
        /** @var QueryStrategy|LimitClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needLimitStrategyInQuery($query);

            return $query;
        }

        return $this->getLimitClause();
    }

    public function intoLimitStrategy()
    {
        if (!$this->hasClause('LIMIT')) {
            $this->setClause('LIMIT', $this->driver()->limit());
        }

        return $this;
    }

    protected function limitStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needLimitStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this->intoLimitStrategy();
        }

        return $this->cleanClone()->setClause('LIMIT', $this->driver()->limit());
    }

    protected function needLimitStrategyInQuery(QueryStrategy $query)
    {
        //        if (!($query instanceof LimitClauseStrategy)) {
        //            throw new SqlException('Current query does not have a LIMIT clause.');
        //        }

        return $this;
    }
}
