<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OffsetClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

trait OffsetTableClauseTrait
{
    use TableClauseTrait;

    private $offsetAppliers = [];

    public function assignOffsetAppliers(OffsetClauseStrategy $strategy)
    {
        if ($this->offsetAppliers and !$strategy->hasOffset()) {
            foreach ($this->offsetAppliers as $applier) {
                $clause = $this->driver()->offset();

                call_user_func_array($applier, [$clause]);

                $strategy->offset($clause->getOffset());
            }
        }

        return $this;
    }

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

    public function clearOffsetAppliers()
    {
        $this->offsetAppliers = [];

        return $this;
    }

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

    public function clearOffset()
    {
        if ($clause = $this->getOffsetStrategy()) {
            $clause->clearOffset();
        }

        return $this;
    }

    public function getOffsetStrategy(): ?OffsetClauseStrategy
    {
        /** @var QueryStrategy|OffsetClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needOffsetStrategyInQuery($query);

            return $query;
        }

        return $this->getOffsetClause();
    }

    public function offsetStrategy(): OffsetClauseStrategy
    {
        /** @var QueryStrategy|OffsetClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needOffsetStrategyInQuery($query);

            return $query;
        }

        return $this->offsetClause();
    }

    public function getOffsetClause(): ?OffsetClause
    {
        /** @var OffsetClause $clause */
        $clause = $this->getClause('OFFSET');

        return $clause;
    }

    public function offsetClause(): OffsetClause
    {
        if (!$clause = $this->getClause('OFFSET')) {
            $this->setClause('OFFSET', $clause = $this->driver()->offset());
        }

        return $clause;
    }

    protected function offsetStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needOffsetStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this;
        }

        return $this->cleanClone();
    }

    protected function needOffsetStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof OffsetClauseStrategy)) {
            throw new QueryException('Current query does not have an OFFSET clause.');
        }

        return $this;
    }
}
