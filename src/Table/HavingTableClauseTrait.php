<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

trait HavingTableClauseTrait
{
    use TableClauseTrait;

    /**
     * @var callable[]
     */
    private $havingAppliers = [];

    /**
     * @param HavingClauseStrategy $strategy
     *
     * @return $this
     */
    public function assignHavingAppliers(HavingClauseStrategy $strategy)
    {
        if ($this->havingAppliers) {
            $items = $strategy->getHaving();

            $strategy->clearHaving();

            foreach ($this->havingAppliers as $applier) {
                $clause = $this->connection()->having();

                call_user_func_array($applier, [$clause]);

                $strategy->havingConditions($clause);
            }

            if ($items) {
                $clause = $this->connection()->having();

                foreach ($items as $where) {
                    $clause->havingLogic($where['logic'], $where['sql'], $where['params']);
                }

                $strategy->havingConditions($clause);
            }
        }

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
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

    /**
     * @param array $appliers
     *
     * @return $this
     */
    public function setHavingAppliers(array $appliers)
    {
        $this->havingAppliers = $appliers;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearHavingAppliers()
    {
        $this->havingAppliers = [];

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function having($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->having(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orHaving($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHaving(...func_get_args());

        return $instance;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function havingMultiple(array $columns)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingMultiple($columns);

        return $instance;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orHavingMultiple(array $columns)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingMultiple($columns);

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingDate($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingDate(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orHavingDate($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingDate(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingTime($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingTime(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orHavingTime($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingTime(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingYear($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingYear(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orHavingYear($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingYear(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingMonth($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingMonth(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orHavingMonth($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingMonth(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingDay($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingDay(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orHavingDay($column, $operator, $value = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingDay(...func_get_args());

        return $instance;
    }

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     *
     * @return $this
     */
    public function havingRelation($column1, $operator, $column2 = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingRelation(...func_get_args());

        return $instance;
    }

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     *
     * @return $this
     */
    public function orHavingRelation($column1, $operator, $column2 = null)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingRelation(...func_get_args());

        return $instance;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function havingRelations(array $relations)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingRelations($relations);

        return $instance;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orHavingRelations(array $relations)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingRelations($relations);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIs(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingIs($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIs(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingIs($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNot(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingIsNot($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNot(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingIsNot($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNull(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingIsNull($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNull(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingIsNull($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNotNull(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingIsNotNull($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNotNull(string $column)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingIsNotNull($column);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function havingBetween(string $column, int $min, int $max)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingBetween($column, $min, $max);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orHavingBetween(string $column, int $min, int $max)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingBetween($column, $min, $max);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function havingNotBetween(string $column, int $min, int $max)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingNotBetween($column, $min, $max);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orHavingNotBetween(string $column, int $min, int $max)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingNotBetween($column, $min, $max);

        return $instance;
    }

    /**
     * @param $strategy
     *
     * @return $this
     */
    public function havingConditions($strategy)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingConditions($strategy);

        return $instance;
    }

    /**
     * @param $strategy
     *
     * @return $this
     */
    public function orHavingConditions($strategy)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingConditions($strategy);

        return $instance;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function havingRaw(string $sql, string ...$params)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->havingRaw($sql, ...$params);

        return $instance;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function orHavingRaw(string $sql, string ...$params)
    {
        $instance = $this->havingStrategyInstance();

        $instance->havingStrategy()->orHavingRaw($sql, ...$params);

        return $instance;
    }

    /**
     * @return bool
     */
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

    /**
     * @return $this
     */
    public function clearHaving()
    {
        if ($clause = $this->getHavingStrategy()) {
            $clause->clearHaving();
        }

        return $this;
    }

    public function havingToSql(bool $useClause = true): array
    {
        if ($clause = $this->getHavingStrategy()) {
            return $clause->havingToSql($useClause);
        }

        return ['', []];
    }

    public function havingToString(bool $useClause = true): string
    {
        return $this->havingToSql($useClause)[0];
    }

    public function havingClause(): HavingClause
    {
        /** @var HavingClause $clause */
        $clause = $this->clause('HAVING');

        return $clause;
    }

    public function hasHavingClause(): bool
    {
        return $this->hasClause('HAVING');
    }

    public function getHavingClause(): ?HavingClause
    {
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

        return $this->havingClause();
    }

    public function getHavingStrategy(): ?HavingClauseStrategy
    {
        /** @var QueryStrategy|HavingClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needHavingStrategyInQuery($query);

            return $query;
        }

        return $this->getHavingClause();
    }

    /**
     * @return $this
     */
    public function intoHavingStrategy()
    {
        if (!$this->hasClause('HAVING')) {
            $this->setClause('HAVING', $this->connection()->having());
        }

        return $this;
    }

    protected function havingStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needHavingStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this->intoHavingStrategy();
        }

        return $this->cleanClone()->setClause('HAVING', $this->connection()->having());
    }

    protected function needHavingStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof HavingClauseStrategy)) {
            throw new SqlException('Current query does not have a HAVING clause.');
        }

        return $this;
    }

    protected function getPreparedHavingClause()
    {
        if ($this->hasHavingAppliers()) {
            $clause = clone $this->intoHavingStrategy()->havingClause();

            $this->assignHavingAppliers($clause);
        } else {
            $clause = $this->getHavingClause();
        }

        return $clause;
    }
}
