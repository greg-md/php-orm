<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

trait GroupByTableClauseTrait
{
    use TableClauseTrait;

    private $groupByAppliers = [];

    /**
     * @param GroupByClauseStrategy $strategy
     *
     * @return $this
     */
    public function assignGroupByAppliers(GroupByClauseStrategy $strategy)
    {
        if ($this->groupByAppliers) {
            $items = $strategy->getGroupBy();

            $strategy->clearGroupBy();

            foreach ($this->groupByAppliers as $applier) {
                $clause = $this->connection()->groupBy();

                call_user_func_array($applier, [$clause]);

                foreach ($clause->getGroupBy() as $groupBy) {
                    $strategy->addGroupBy($groupBy['sql'], $groupBy['params']);
                }
            }

            foreach ($items as $groupBy) {
                $strategy->addGroupBy($groupBy['sql'], $groupBy['params']);
            }
        }

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
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

    /**
     * @param array $appliers
     *
     * @return $this
     */
    public function setGroupByAppliers(array $appliers)
    {
        $this->groupByAppliers = $appliers;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearGroupByAppliers()
    {
        $this->groupByAppliers = [];

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function groupBy(string $column)
    {
        $instance = $this->groupByStrategyInstance();

        $instance->groupByStrategy()->groupBy($column);

        return $instance;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function groupByRaw(string $sql, string ...$params)
    {
        $instance = $this->groupByStrategyInstance();

        $instance->groupByStrategy()->groupByRaw($sql, ...$params);

        return $instance;
    }

    /**
     * @return bool
     */
    public function hasGroupBy(): bool
    {
        if ($clause = $this->getGroupByStrategy()) {
            return $clause->hasGroupBy();
        }

        return false;
    }

    /**
     * @return array
     */
    public function getGroupBy(): array
    {
        if ($clause = $this->getGroupByStrategy()) {
            return $clause->getGroupBy();
        }

        return [];
    }

    /**
     * @return $this
     */
    public function clearGroupBy()
    {
        if ($clause = $this->getGroupByStrategy()) {
            $clause->clearGroupBy();
        }

        return $this;
    }

    public function groupByToSql(bool $useClause = true): array
    {
        if ($clause = $this->getGroupByStrategy()) {
            return $clause->groupByToSql($useClause);
        }

        return ['', []];
    }

    public function groupByToString(bool $useClause = true): string
    {
        return $this->groupByToSql($useClause)[0];
    }

    public function groupByClause(): GroupByClause
    {
        /** @var GroupByClause $clause */
        $clause = $this->clause('GROUP_BY');

        return $clause;
    }

    public function hasGroupByClause(): bool
    {
        return $this->hasClause('GROUP_BY');
    }

    public function getGroupByClause(): ?GroupByClause
    {
        /** @var GroupByClause $clause */
        $clause = $this->getClause('GROUP_BY');

        return $clause;
    }

    public function groupByStrategy(): GroupByClauseStrategy
    {
        /** @var QueryStrategy|GroupByClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->validateGroupByStrategyInQuery($query);

            return $query;
        }

        return $this->groupByClause();
    }

    public function getGroupByStrategy(): ?GroupByClauseStrategy
    {
        /** @var QueryStrategy|GroupByClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->validateGroupByStrategyInQuery($query);

            return $query;
        }

        return $this->getGroupByClause();
    }

    /**
     * @return $this
     */
    public function intoGroupByStrategy()
    {
        if (!$this->hasClause('GROUP_BY')) {
            $this->setClause('GROUP_BY', $this->connection()->groupBy());
        }

        return $this;
    }

    private function groupByStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->validateGroupByStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this->intoGroupByStrategy();
        }

        return $this->cleanClone()->setClause('GROUP_BY', $this->connection()->groupBy());
    }

    private function validateGroupByStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof GroupByClauseStrategy)) {
            throw new SqlException('Current query does not have a GROUP BY clause.');
        }

        return $this;
    }

    private function getPreparedGroupByClause()
    {
        if ($this->groupByAppliers) {
            $clause = clone $this->intoGroupByStrategy()->groupByClause();

            $this->assignGroupByAppliers($clause);
        } else {
            $clause = $this->getGroupByClause();
        }

        return $clause;
    }
}
