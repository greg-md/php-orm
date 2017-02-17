<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

trait GroupByTableClauseTrait
{
    use TableClauseTrait;

    private $groupByAppliers = [];

    public function assignGroupByAppliers(GroupByClauseStrategy $strategy)
    {
        if ($this->groupByAppliers and $items = $strategy->getGroupBy()) {
            $strategy->clearGroupBy();

            foreach ($this->groupByAppliers as $applier) {
                $clause = $this->driver()->groupBy();

                call_user_func_array($applier, [$clause]);

                foreach ($clause->getGroupBy() as $groupBy) {
                    $strategy->groupByLogic($groupBy['sql'], $groupBy['params']);
                }
            }

            foreach ($items as $groupBy) {
                $strategy->groupByLogic($groupBy['sql'], $groupBy['params']);
            }
        }

        return $this;
    }

    public function setGroupByApplier(callable $callable)
    {
        $this->groupByAppliers[] = $callable;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasGroupByAppliers(): bool
    {
        return (bool) $this->groupByAppliers;
    }

    /**
     * @return callable[]
     */
    public function getGroupByAppliers(): array
    {
        return $this->groupByAppliers;
    }

    public function clearGroupByAppliers()
    {
        $this->groupByAppliers = [];

        return $this;
    }

    public function groupBy(string $column)
    {
        $instance = $this->groupByStrategyInstance();

        $instance->groupByStrategy()->groupBy($column);

        return $instance;
    }

    public function groupByRaw(string $sql, string ...$params)
    {
        $instance = $this->groupByStrategyInstance();

        $instance->groupByStrategy()->groupByRaw($sql, ...$params);

        return $instance;
    }

    public function hasGroupBy(): bool
    {
        if ($clause = $this->getGroupByStrategy()) {
            return $clause->hasGroupBy();
        }

        return false;
    }

    public function getGroupBy(): array
    {
        if ($clause = $this->getGroupByStrategy()) {
            return $clause->getGroupBy();
        }

        return [];
    }

    public function clearGroupBy()
    {
        if ($clause = $this->getGroupByStrategy()) {
            $clause->clearGroupBy();
        }

        return $this;
    }

    public function getGroupByStrategy(): ?GroupByClauseStrategy
    {
        /** @var QueryStrategy|GroupByClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needGroupByStrategyInQuery($query);

            return $query;
        }

        /** @var GroupByClause $clause */
        $clause = $this->getClause('GROUP_BY');

        return $clause;
    }

    public function groupByStrategy(): GroupByClauseStrategy
    {
        /** @var QueryStrategy|GroupByClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needGroupByStrategyInQuery($query);

            return $query;
        }

        if (!$clause = $this->getClause('GROUP_BY')) {
            $this->setClause('GROUP_BY', $clause = $this->driver()->groupBy());
        }

        return $clause;
    }

    protected function groupByStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needGroupByStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this;
        }

        return $this->sqlClone();
    }

    protected function needGroupByStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof GroupByClauseStrategy)) {
            throw new QueryException('Current query does not have a GROUP BY clause.');
        }

        return $this;
    }

    protected function getGroupByClause(): ?GroupByClause
    {
        /** @var GroupByClause $clause */
        $clause = $this->getClause('GROUP_BY');

        return $clause;
    }
}
