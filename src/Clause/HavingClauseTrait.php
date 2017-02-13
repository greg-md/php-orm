<?php

namespace Greg\Orm\Clause;

trait HavingClauseTrait
{
    /**
     * @var ConditionsStrategy|null
     */
    private $havingConditions;

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function having($column, $operator, $value = null)
    {
        $this->havingConditions()->column(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orHaving($column, $operator, $value = null)
    {
        $this->havingConditions()->orColumn(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function havingMultiple(array $columns)
    {
        $this->havingConditions()->columns(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function orHavingMultiple(array $columns)
    {
        $this->havingConditions()->orColumns(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function havingDate($column, $operator, $value = null)
    {
        $this->havingConditions()->date(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orHavingDate($column, $operator, $value = null)
    {
        $this->havingConditions()->orDate(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function havingTime($column, $operator, $value = null)
    {
        $this->havingConditions()->time(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orHavingTime($column, $operator, $value = null)
    {
        $this->havingConditions()->orTime(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function havingYear($column, $operator, $value = null)
    {
        $this->havingConditions()->year(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orHavingYear($column, $operator, $value = null)
    {
        $this->havingConditions()->orYear(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function havingMonth($column, $operator, $value = null)
    {
        $this->havingConditions()->month(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orHavingMonth($column, $operator, $value = null)
    {
        $this->havingConditions()->orMonth(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function havingDay($column, $operator, $value = null)
    {
        $this->havingConditions()->day(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function orHavingDay($column, $operator, $value = null)
    {
        $this->havingConditions()->orDay(...func_get_args());

        return $this;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     * @return $this
     */
    public function havingRelation($column1, $operator, $column2 = null)
    {
        $this->havingConditions()->relation(...func_get_args());

        return $this;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     * @return $this
     */
    public function orHavingRelation($column1, $operator, $column2 = null)
    {
        $this->havingConditions()->orRelation(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function havingRelations(array $relations)
    {
        $this->havingConditions()->relations(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function orHavingRelations(array $relations)
    {
        $this->havingConditions()->orRelations(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function havingIsNull(string $column)
    {
        $this->havingConditions()->isNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function orHavingIsNull(string $column)
    {
        $this->havingConditions()->orIsNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function havingIsNotNull(string $column)
    {
        $this->havingConditions()->isNotNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function orHavingIsNotNull(string $column)
    {
        $this->havingConditions()->orIsNotNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function havingBetween(string $column, int $min, int $max)
    {
        $this->havingConditions()->between(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function orHavingBetween(string $column, int $min, int $max)
    {
        $this->havingConditions()->orBetween(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function havingNotBetween(string $column, int $min, int $max)
    {
        $this->havingConditions()->notBetween(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function orHavingNotBetween(string $column, int $min, int $max)
    {
        $this->havingConditions()->orNotBetween(...func_get_args());

        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function havingGroup(callable $callable)
    {
        $this->havingConditions()->group($callable);

        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function orHavingGroup(callable $callable)
    {
        $this->havingConditions()->orGroup($callable);

        return $this;
    }

    /**
     * @param ConditionsStrategy $strategy
     * @return $this
     */
    public function havingCondition(ConditionsStrategy $strategy)
    {
        $this->havingConditions()->condition($strategy);

        return $this;
    }

    /**
     * @param ConditionsStrategy $strategy
     * @return $this
     */
    public function orHavingCondition(ConditionsStrategy $strategy)
    {
        $this->havingConditions()->orCondition($strategy);

        return $this;
    }

    /**
     * @param string $sql
     * @param \string[] ...$params
     * @return $this
     */
    public function havingRaw(string $sql, string ...$params)
    {
        $this->havingConditions()->raw($sql, ...$params);

        return $this;
    }

    /**
     * @param string $sql
     * @param \string[] ...$params
     * @return $this
     */
    public function orHavingRaw(string $sql, string ...$params)
    {
        $this->havingConditions()->orRaw($sql, ...$params);

        return $this;
    }

    /**
     * @param string $type
     * @param $sql
     * @param array $params
     * @return $this
     */
    public function havingLogic(string $type, $sql, array $params = [])
    {
        $this->havingConditions()->logic($type, $sql, $params);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasHaving(): bool
    {
        return $this->havingConditions()->has();
    }

    /**
     * @return array
     */
    public function getHaving(): array
    {
        return $this->havingConditions()->get();
    }

    /**
     * @return $this
     */
    public function clearHaving()
    {
        $this->havingConditions()->clear();

        return $this;
    }

    /**
     * @return ConditionsStrategy
     */
    protected function havingConditions(): ConditionsStrategy
    {
        if (!$this->havingConditions) {
            $this->havingConditions = $this->newHavingConditions();
        }

        return $this->havingConditions;
    }

    /**
     * @param bool $useClause
     * @return array
     */
    protected function havingToSql($useClause = true): array
    {
        [$sql, $params] = $this->havingConditions()->toSql();

        if ($sql and $useClause) {
            $sql = 'HAVING ' . $sql;
        }

        return [$sql, $params];
    }

    /**
     * @param bool $useClause
     * @return string
     */
    protected function havingToString($useClause = true): string
    {
        return $this->havingToSql($useClause)[0];
    }

    /**
     * @return ConditionsStrategy
     */
    abstract protected function newHavingConditions(): ConditionsStrategy;
}
