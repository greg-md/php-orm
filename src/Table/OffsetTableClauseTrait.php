<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OffsetClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

trait OffsetTableClauseTrait
{
    use TableClauseTrait;

    private $offsetAppliers = [];

    /**
     * @param OffsetClauseStrategy $strategy
     *
     * @return $this
     */
    public function assignOffsetAppliers(OffsetClauseStrategy $strategy)
    {
        if ($this->offsetAppliers and !$strategy->hasOffset()) {
            foreach ($this->offsetAppliers as $applier) {
                $clause = $this->connection()->offset();

                call_user_func_array($applier, [$clause]);

                $strategy->offset($clause->getOffset());
            }
        }

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function setOffsetApplier(callable $callable)
    {
        $this->offsetAppliers[] = $callable;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOffsetAppliers(): bool
    {
        return (bool) $this->offsetAppliers;
    }

    /**
     * @return callable[]
     */
    public function getOffsetAppliers(): array
    {
        return $this->offsetAppliers;
    }

    /**
     * @param array $appliers
     *
     * @return $this
     */
    public function setOffsetAppliers(array $appliers)
    {
        $this->offsetAppliers = $appliers;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearOffsetAppliers()
    {
        $this->offsetAppliers = [];

        return $this;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function offset(int $number)
    {
        $instance = $this->offsetStrategyInstance();

        $instance->offsetStrategy()->offset($number);

        return $instance;
    }

    public function hasOffset(): bool
    {
        if ($clause = $this->getOffsetStrategy()) {
            return $clause->hasOffset();
        }

        return false;
    }

    public function getOffset(): ?int
    {
        if ($clause = $this->getOffsetStrategy()) {
            return $clause->getOffset();
        }

        return null;
    }

    /**
     * @return $this
     */
    public function clearOffset()
    {
        if ($clause = $this->getOffsetStrategy()) {
            $clause->clearOffset();
        }

        return $this;
    }

    public function offsetClause(): OffsetClause
    {
        /** @var OffsetClause $clause */
        $clause = $this->clause('OFFSET');

        return $clause;
    }

    public function hasOffsetClause(): bool
    {
        return $this->hasClause('OFFSET');
    }

    public function getOffsetClause(): ?OffsetClause
    {
        /** @var OffsetClause $clause */
        $clause = $this->getClause('OFFSET');

        return $clause;
    }

    public function offsetStrategy(): OffsetClauseStrategy
    {
        /** @var QueryStrategy|OffsetClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->validateOffsetStrategyInQuery($query);

            return $query;
        }

        return $this->offsetClause();
    }

    public function getOffsetStrategy(): ?OffsetClauseStrategy
    {
        /** @var QueryStrategy|OffsetClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->validateOffsetStrategyInQuery($query);

            return $query;
        }

        return $this->getOffsetClause();
    }

    /**
     * @return $this
     */
    public function intoOffsetStrategy()
    {
        if (!$this->hasClause('OFFSET')) {
            $this->setClause('OFFSET', $this->connection()->offset());
        }

        return $this;
    }

    protected function offsetStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->validateOffsetStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this->intoOffsetStrategy();
        }

        return $this->cleanClone()->setClause('OFFSET', $this->connection()->offset());
    }

    protected function validateOffsetStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof OffsetClauseStrategy)) {
            throw new SqlException('Current query does not have an OFFSET clause.');
        }

        return $this;
    }

    protected function getPreparedOffsetClause()
    {
        if ($this->offsetAppliers) {
            $clause = clone $this->intoOffsetStrategy()->offsetClause();

            $this->assignOffsetAppliers($clause);
        } else {
            $clause = $this->getOffsetClause();
        }

        return $clause;
    }
}
