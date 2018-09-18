<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

trait OrderByTableClauseTrait
{
    use TableClauseTrait;

    private $orderByAppliers = [];

    /**
     * @param OrderByClauseStrategy $strategy
     *
     * @return $this
     */
    public function assignOrderByAppliers(OrderByClauseStrategy $strategy)
    {
        if ($this->orderByAppliers) {
            $items = $strategy->getOrderBy();

            $strategy->clearOrderBy();

            foreach ($this->orderByAppliers as $applier) {
                $clause = $this->connection()->orderBy();

                call_user_func_array($applier, [$clause]);

                foreach ($clause->getOrderBy() as $orderBy) {
                    $strategy->addOrderBy($orderBy['sql'], $orderBy['type'], $orderBy['params']);
                }
            }

            foreach ($items as $orderBy) {
                $strategy->addOrderBy($orderBy['sql'], $orderBy['type'], $orderBy['params']);
            }
        }

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
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

    /**
     * @param array $appliers
     *
     * @return $this
     */
    public function setOrderByAppliers(array $appliers)
    {
        $this->orderByAppliers = $appliers;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearOrderByAppliers()
    {
        $this->orderByAppliers = [];

        return $this;
    }

    /**
     * @param string      $column
     * @param string|null $type
     *
     * @return $this
     */
    public function orderBy(string $column, string $type = null)
    {
        $instance = $this->orderByStrategyInstance();

        $instance->orderByStrategy()->orderBy($column, $type);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orderAsc(string $column)
    {
        $instance = $this->orderByStrategyInstance();

        $instance->orderByStrategy()->orderAsc($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orderDesc(string $column)
    {
        $instance = $this->orderByStrategyInstance();

        $instance->orderByStrategy()->orderDesc($column);

        return $instance;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function orderByRaw(string $sql, string ...$params)
    {
        $instance = $this->orderByStrategyInstance();

        $instance->orderByStrategy()->orderByRaw($sql, ...$params);

        return $instance;
    }

    /**
     * @return bool
     */
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

    /**
     * @return $this
     */
    public function clearOrderBy()
    {
        if ($clause = $this->getOrderByStrategy()) {
            $clause->clearOrderBy();
        }

        return $this;
    }

    public function orderByToSql(bool $useClause = true): array
    {
        if ($clause = $this->getOrderByStrategy()) {
            return $clause->orderByToSql($useClause);
        }

        return ['', []];
    }

    public function orderByToString(bool $useClause = true): string
    {
        return $this->orderByToSql($useClause)[0];
    }

    public function orderByClause(): OrderByClause
    {
        /** @var OrderByClause $clause */
        $clause = $this->clause('ORDER_BY');

        return $clause;
    }

    public function hasOrderByClause(): bool
    {
        return $this->hasClause('ORDER_BY');
    }

    public function getOrderByClause(): ?OrderByClause
    {
        /** @var OrderByClause $clause */
        $clause = $this->getClause('ORDER_BY');

        return $clause;
    }

    public function getOrderByStrategy(): ?OrderByClauseStrategy
    {
        /** @var QueryStrategy|OrderByClauseStrategy $query */
        if ($query = $this->getQuery()) {
            //$this->validateOrderByStrategyInQuery($query);

            return $query;
        }

        return $this->getOrderByClause();
    }

    public function orderByStrategy(): OrderByClauseStrategy
    {
        /** @var QueryStrategy|OrderByClauseStrategy $query */
        if ($query = $this->getQuery()) {
            //$this->validateOrderByStrategyInQuery($query);

            return $query;
        }

        return $this->orderByClause();
    }

    /**
     * @return $this
     */
    public function intoOrderByStrategy()
    {
        if (!$this->hasClause('ORDER_BY')) {
            $this->setClause('ORDER_BY', $this->connection()->orderBy());
        }

        return $this;
    }

    private function orderByStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            //$this->validateOrderByStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this->intoOrderByStrategy();
        }

        return $this->cleanClone()->setClause('ORDER_BY', $this->connection()->orderBy());
    }

//    private function validateOrderByStrategyInQuery(QueryStrategy $query)
//    {
//        if (!($query instanceof OrderByClauseStrategy)) {
//            throw new SqlException('Current query does not have an ORDER BY clause.');
//        }
//
//        return $this;
//    }

    private function getPreparedOrderByClause()
    {
        if ($this->orderByAppliers) {
            $clause = clone $this->intoOrderByStrategy()->orderByClause();

            $this->assignOrderByAppliers($clause);
        } else {
            $clause = $this->getOrderByClause();
        }

        return $clause;
    }
}
