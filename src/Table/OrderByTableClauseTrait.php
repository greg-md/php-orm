<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

trait OrderByTableClauseTrait
{
    use TableClauseTrait;

    private $orderByAppliers = [];

    public function assignOrderByAppliers(OrderByClauseStrategy $strategy)
    {
        if ($items = $strategy->getOrderBy()) {
            $strategy->clearOrderBy();

            foreach ($this->orderByAppliers as $applier) {
                $clause = $this->driver()->orderBy();

                call_user_func_array($applier, [$clause]);

                foreach ($clause->getOrderBy() as $orderBy) {
                    $strategy->orderByLogic($orderBy['sql'], $orderBy['type'], $orderBy['params']);
                }
            }

            foreach ($items as $orderBy) {
                $strategy->orderByLogic($orderBy['sql'], $orderBy['type'], $orderBy['params']);
            }
        }

        return $this;
    }

    public function setOrderByApplier(callable $callable)
    {
        $this->orderByAppliers[] = $callable;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOrderByAppliers(): bool
    {
        return (bool) $this->orderByAppliers;
    }

    /**
     * @return callable[]
     */
    public function getOrderByAppliers(): array
    {
        return $this->orderByAppliers;
    }

    public function clearOrderByAppliers()
    {
        $this->orderByAppliers = [];

        return $this;
    }

    public function orderBy(string $column, string $type = null)
    {
        $instance = $this->orderByStrategyInstance();

        $instance->orderByStrategy()->orderBy($column, $type);

        return $instance;
    }

    public function orderAsc(string $column)
    {
        $instance = $this->orderByStrategyInstance();

        $instance->orderByStrategy()->orderAsc($column);

        return $instance;
    }

    public function orderDesc(string $column)
    {
        $instance = $this->orderByStrategyInstance();

        $instance->orderByStrategy()->orderDesc($column);

        return $instance;
    }

    public function orderByRaw(string $sql, string ...$params)
    {
        $instance = $this->orderByStrategyInstance();

        $instance->orderByStrategy()->orderByRaw($sql, ...$params);

        return $instance;
    }

    public function hasOrderBy(): bool
    {
        if ($clause = $this->getOrderByStrategy()) {
            return $clause->hasOrderBy();
        }

        return false;
    }

    public function getOrderBy(): array
    {
        if ($clause = $this->getOrderByStrategy()) {
            return $clause->getOrderBy();
        }

        return [];
    }

    public function clearOrderBy()
    {
        if ($clause = $this->getOrderByStrategy()) {
            $clause->clearOrderBy();
        }

        return $this;
    }

    public function getOrderByStrategy(): ?OrderByClauseStrategy
    {
        /** @var QueryStrategy|OrderByClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needOrderByStrategyInQuery($query);

            return $query;
        }

        /** @var OrderByClause $clause */
        $clause = $this->getClause('ORDER_BY');

        return $clause;
    }

    public function orderByStrategy(): OrderByClauseStrategy
    {
        /** @var QueryStrategy|OrderByClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needOrderByStrategyInQuery($query);

            return $query;
        }

        if (!$clause = $this->getClause('ORDER_BY')) {
            $this->setClause('ORDER_BY', $clause = $this->driver()->orderBy());
        }

        return $clause;
    }

    protected function orderByStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needOrderByStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this;
        }

        return $this->sqlClone();
    }

    protected function needOrderByStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof OrderByClauseStrategy)) {
            throw new QueryException('Current query does not have an ORDER BY clause.');
        }

        return $this;
    }
}