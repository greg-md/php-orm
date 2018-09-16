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

    /**
     * @param LimitClauseStrategy $strategy
     *
     * @return $this
     */
    public function assignLimitAppliers(LimitClauseStrategy $strategy)
    {
        if ($this->limitAppliers and !$strategy->hasLimit()) {
            foreach ($this->limitAppliers as $applier) {
                $clause = $this->connection()->limit();

                call_user_func_array($applier, [$clause]);

                $strategy->limit($clause->getLimit());
            }
        }

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
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

    /**
     * @param array $appliers
     *
     * @return $this
     */
    public function setLimitAppliers(array $appliers)
    {
        $this->limitAppliers = $appliers;

        return $this;
    }

    /**
     * @return $this
     */
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

    /**
     * @return $this
     */
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

    /**
     * @return $this
     */
    public function intoLimitStrategy()
    {
        if (!$this->hasClause('LIMIT')) {
            $this->setClause('LIMIT', $this->connection()->limit());
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

        return $this->cleanClone()->setClause('LIMIT', $this->connection()->limit());
    }

    protected function needLimitStrategyInQuery(QueryStrategy $query)
    {
        //        if (!($query instanceof LimitClauseStrategy)) {
        //            throw new SqlException('Current query does not have a LIMIT clause.');
        //        }

        return $this;
    }
}
