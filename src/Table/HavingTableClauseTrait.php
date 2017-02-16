<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Conditions;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

trait HavingTableClauseTrait
{
    use TableClauseTrait;

    /**
     * @var callable[]
     */
    private $havingAppliers = [];

    public function assignHavingAppliers(HavingClauseStrategy $strategy)
    {
        if ($items = $strategy->getHaving()) {
            $strategy->clearHaving();

            foreach ($this->havingAppliers as $applier) {
                $clause = $this->driver()->having();

                call_user_func_array($applier, [$clause]);

                $strategy->havingStrategy($clause);
            }

            $clause = $this->driver()->having();

            foreach ($items as $where) {
                $clause->havingLogic($where['logic'], $where['sql'], $where['params']);
            }

            $strategy->havingStrategy($clause);
        }

        return $this;
    }

    public function setHavingApplier(callable $callable)
    {
        $this->havingAppliers[] = $callable;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasHavingAppliers(): bool
    {
        return (bool) $this->havingAppliers;
    }

    /**
     * @return callable[]
     */
    public function getHavingAppliers(): array
    {
        return $this->havingAppliers;
    }

    public function clearHavingAppliers()
    {
        $this->havingAppliers = [];

        return $this;
    }

    public function having($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->having(...func_get_args());

        return $instance;
    }

    public function orHaving($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHaving(...func_get_args());

        return $instance;
    }

    public function havingMultiple(array $columns)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingMultiple($columns);

        return $instance;
    }

    public function orHavingMultiple(array $columns)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingMultiple($columns);

        return $instance;
    }

    public function havingDate($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingDate(...func_get_args());

        return $instance;
    }

    public function orHavingDate($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingDate(...func_get_args());

        return $instance;
    }

    public function havingTime($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingTime(...func_get_args());

        return $instance;
    }

    public function orHavingTime($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingTime(...func_get_args());

        return $instance;
    }

    public function havingYear($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingYear(...func_get_args());

        return $instance;
    }

    public function orHavingYear($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingYear(...func_get_args());

        return $instance;
    }

    public function havingMonth($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingMonth(...func_get_args());

        return $instance;
    }

    public function orHavingMonth($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingMonth(...func_get_args());

        return $instance;
    }

    public function havingDay($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingDay(...func_get_args());

        return $instance;
    }

    public function orHavingDay($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingDay(...func_get_args());

        return $instance;
    }

    public function havingRelation($column1, $operator, $column2 = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingRelation(...func_get_args());

        return $instance;
    }

    public function orHavingRelation($column1, $operator, $column2 = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingRelation(...func_get_args());

        return $instance;
    }

    public function havingRelations(array $relations)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingRelations($relations);

        return $instance;
    }

    public function orHavingRelations(array $relations)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingRelations($relations);

        return $instance;
    }

    public function havingIsNull(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingIsNull($column);

        return $instance;
    }

    public function orHavingIsNull(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingIsNull($column);

        return $instance;
    }

    public function havingIsNotNull(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingIsNotNull($column);

        return $instance;
    }

    public function orHavingIsNotNull(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingIsNotNull($column);

        return $instance;
    }

    public function havingBetween(string $column, int $min, int $max)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingBetween($column, $min, $max);

        return $instance;
    }

    public function orHavingBetween(string $column, int $min, int $max)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingBetween($column, $min, $max);

        return $instance;
    }

    public function havingNotBetween(string $column, int $min, int $max)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingNotBetween($column, $min, $max);

        return $instance;
    }

    public function orHavingNotBetween(string $column, int $min, int $max)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingNotBetween($column, $min, $max);

        return $instance;
    }

    public function havingGroup(callable $callable)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingGroup($callable);

        return $instance;
    }

    public function orHavingGroup(callable $callable)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingGroup($callable);

        return $instance;
    }

    public function havingConditions(Conditions $strategy)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingConditions($strategy);

        return $instance;
    }

    public function orHavingConditions(Conditions $strategy)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingConditions($strategy);

        return $instance;
    }

    public function havingRaw(string $sql, string ...$params)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingRaw($sql, ...$params);

        return $instance;
    }

    public function orHavingRaw(string $sql, string ...$params)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingRaw($sql, ...$params);

        return $instance;
    }

    public function hasHaving(): bool
    {
        if ($clause = $this->getHavingStrategy()) {
            return $clause->hasHaving();
        }

        return false;
    }

    public function getHaving(): array
    {
        if ($clause = $this->getHavingStrategy()) {
            return $clause->getHaving();
        }

        return [];
    }

    public function clearHaving()
    {
        if ($clause = $this->getHavingStrategy()) {
            $clause->clearHaving();
        }

        return $this;
    }

    public function getHavingStrategy(): ?HavingClauseStrategy
    {
        /** @var QueryStrategy|HavingClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needHavingStrategyInQuery($query);

            return $query;
        }

        /** @var HavingClause $clause */
        $clause = $this->getClause('HAVING');

        return $clause;
    }

    public function havingStrategy(): HavingClauseStrategy
    {
        /** @var QueryStrategy|HavingClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needHavingStrategyInQuery($query);

            return $query;
        }

        if (!$clause = $this->getClause('HAVING')) {
            $this->setClause('HAVING', $clause = $this->driver()->having());
        }

        return $clause;
    }

    protected function havingStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needHavingStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this;
        }

        return $this->sqlClone();
    }

    protected function needHavingStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof HavingClauseStrategy)) {
            throw new QueryException('Current query does not have a HAVING clause.');
        }

        return $this;
    }

    protected function getHavingClause(): ?HavingClause
    {
        /** @var HavingClause $clause */
        $clause = $this->getClause('HAVING');

        return $clause;
    }
}
