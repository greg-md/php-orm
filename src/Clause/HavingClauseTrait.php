<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\DialectStrategy;

trait HavingClauseTrait
{
    /**
     * @var Conditions|null
     */
    private $hConditions;

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function having($column, $operator, $value = null)
    {
        $this->hConditions()->column(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHaving($column, $operator, $value = null)
    {
        $this->hConditions()->orColumn(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function havingMultiple(array $columns)
    {
        $this->hConditions()->columns(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orHavingMultiple(array $columns)
    {
        $this->hConditions()->orColumns(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingDate($column, $operator, $value = null)
    {
        $this->hConditions()->date(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingDate($column, $operator, $value = null)
    {
        $this->hConditions()->orDate(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingTime($column, $operator, $value = null)
    {
        $this->hConditions()->time(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingTime($column, $operator, $value = null)
    {
        $this->hConditions()->orTime(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingYear($column, $operator, $value = null)
    {
        $this->hConditions()->year(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingYear($column, $operator, $value = null)
    {
        $this->hConditions()->orYear(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingMonth($column, $operator, $value = null)
    {
        $this->hConditions()->month(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingMonth($column, $operator, $value = null)
    {
        $this->hConditions()->orMonth(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingDay($column, $operator, $value = null)
    {
        $this->hConditions()->day(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingDay($column, $operator, $value = null)
    {
        $this->hConditions()->orDay(...func_get_args());

        return $this;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function havingRelation($column1, $operator, $column2 = null)
    {
        $this->hConditions()->relation(...func_get_args());

        return $this;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function orHavingRelation($column1, $operator, $column2 = null)
    {
        $this->hConditions()->orRelation(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function havingRelations(array $relations)
    {
        $this->hConditions()->relations(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orHavingRelations(array $relations)
    {
        $this->hConditions()->orRelations(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNull(string $column)
    {
        $this->hConditions()->isNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNull(string $column)
    {
        $this->hConditions()->orIsNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNotNull(string $column)
    {
        $this->hConditions()->isNotNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNotNull(string $column)
    {
        $this->hConditions()->orIsNotNull(...func_get_args());

        return $this;
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
        $this->hConditions()->between(...func_get_args());

        return $this;
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
        $this->hConditions()->orBetween(...func_get_args());

        return $this;
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
        $this->hConditions()->notBetween(...func_get_args());

        return $this;
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
        $this->hConditions()->orNotBetween(...func_get_args());

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function havingGroup(callable $callable)
    {
        $this->hConditions()->group($callable);

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function orHavingGroup(callable $callable)
    {
        $this->hConditions()->orGroup($callable);

        return $this;
    }

    /**
     * @param Conditions $strategy
     *
     * @return $this
     */
    public function havingConditions(Conditions $strategy)
    {
        $this->hConditions()->conditions($strategy);

        return $this;
    }

    /**
     * @param Conditions $strategy
     *
     * @return $this
     */
    public function orHavingConditions(Conditions $strategy)
    {
        $this->hConditions()->orConditions($strategy);

        return $this;
    }

    /**
     * @param HavingClauseStrategy $strategy
     *
     * @return $this
     */
    public function havingStrategy(HavingClauseStrategy $strategy)
    {
        $this->hConditions()->logic('AND', $strategy);

        return $this;
    }

    /**
     * @param HavingClauseStrategy $strategy
     *
     * @return $this
     */
    public function orHavingStrategy(HavingClauseStrategy $strategy)
    {
        $this->hConditions()->logic('OR', $strategy);

        return $this;
    }

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function havingRaw(string $sql, string ...$params)
    {
        $this->hConditions()->raw($sql, ...$params);

        return $this;
    }

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function orHavingRaw(string $sql, string ...$params)
    {
        $this->hConditions()->orRaw($sql, ...$params);

        return $this;
    }

    /**
     * @param string $logic
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    public function havingLogic(string $logic, $sql, array $params = [])
    {
        $this->hConditions()->logic($logic, $sql, $params);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasHaving(): bool
    {
        return $this->hConditions()->has();
    }

    /**
     * @return array
     */
    public function getHaving(): array
    {
        return $this->hConditions()->get();
    }

    /**
     * @return $this
     */
    public function clearHaving()
    {
        $this->hConditions()->clear();

        return $this;
    }

    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function havingToSql($useClause = true): array
    {
        [$sql, $params] = $this->hConditions()->toSql();

        if ($sql and $useClause) {
            $sql = 'HAVING ' . $sql;
        }

        return [$sql, $params];
    }

    /**
     * @param bool $useClause
     *
     * @return string
     */
    public function havingToString($useClause = true): string
    {
        return $this->havingToSql($useClause)[0];
    }

    /**
     * @return Conditions
     */
    protected function hConditions(): Conditions
    {
        if (!$this->hConditions) {
            $this->hConditions = new Conditions($this->dialect());
        }

        return $this->hConditions;
    }

    protected function havingClone()
    {
        if ($this->hConditions) {
            $this->hConditions = clone $this->hConditions;
        }

        return $this;
    }

    /**
     * @return DialectStrategy
     */
    abstract public function dialect(): DialectStrategy;
}