<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\ConditionsStrategy;
use Greg\Orm\DialectStrategy;

trait HavingClauseTrait
{
    /**
     * @var ConditionsStrategy|null
     */
    private $havingStrategy;

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function having($column, $operator, $value = null)
    {
        $this->havingStrategy()->column(...func_get_args());

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
        $this->havingStrategy()->orColumn(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function havingMultiple(array $columns)
    {
        $this->havingStrategy()->columns(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orHavingMultiple(array $columns)
    {
        $this->havingStrategy()->orColumns(...func_get_args());

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
        $this->havingStrategy()->date(...func_get_args());

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
        $this->havingStrategy()->orDate(...func_get_args());

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
        $this->havingStrategy()->time(...func_get_args());

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
        $this->havingStrategy()->orTime(...func_get_args());

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
        $this->havingStrategy()->year(...func_get_args());

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
        $this->havingStrategy()->orYear(...func_get_args());

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
        $this->havingStrategy()->month(...func_get_args());

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
        $this->havingStrategy()->orMonth(...func_get_args());

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
        $this->havingStrategy()->day(...func_get_args());

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
        $this->havingStrategy()->orDay(...func_get_args());

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
        $this->havingStrategy()->relation(...func_get_args());

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
        $this->havingStrategy()->orRelation(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function havingRelations(array $relations)
    {
        $this->havingStrategy()->relations(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orHavingRelations(array $relations)
    {
        $this->havingStrategy()->orRelations(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNull(string $column)
    {
        $this->havingStrategy()->isNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNull(string $column)
    {
        $this->havingStrategy()->orIsNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNotNull(string $column)
    {
        $this->havingStrategy()->isNotNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNotNull(string $column)
    {
        $this->havingStrategy()->orIsNotNull(...func_get_args());

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
        $this->havingStrategy()->between(...func_get_args());

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
        $this->havingStrategy()->orBetween(...func_get_args());

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
        $this->havingStrategy()->notBetween(...func_get_args());

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
        $this->havingStrategy()->orNotBetween(...func_get_args());

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function havingGroup(callable $callable)
    {
        $this->havingStrategy()->group($callable);

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function orHavingGroup(callable $callable)
    {
        $this->havingStrategy()->orGroup($callable);

        return $this;
    }

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function havingConditions(ConditionsStrategy $strategy)
    {
        $this->havingStrategy()->conditions($strategy);

        return $this;
    }

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function orHavingConditions(ConditionsStrategy $strategy)
    {
        $this->havingStrategy()->orConditions($strategy);

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
        $this->havingStrategy()->raw($sql, ...$params);

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
        $this->havingStrategy()->orRaw($sql, ...$params);

        return $this;
    }

    /**
     * @param string $type
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    public function havingLogic(string $type, $sql, array $params = [])
    {
        $this->havingStrategy()->logic($type, $sql, $params);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasHaving(): bool
    {
        return $this->havingStrategy()->has();
    }

    /**
     * @return array
     */
    public function getHaving(): array
    {
        return $this->havingStrategy()->get();
    }

    /**
     * @return $this
     */
    public function clearHaving()
    {
        $this->havingStrategy()->clear();

        return $this;
    }

    /**
     * @return ConditionsStrategy
     */
    protected function havingStrategy(): ConditionsStrategy
    {
        if (!$this->havingStrategy) {
            $this->havingStrategy = new Conditions($this->dialect());
        }

        return $this->havingStrategy;
    }

    /**
     * @param bool $useClause
     *
     * @return array
     */
    protected function havingToSql($useClause = true): array
    {
        [$sql, $params] = $this->havingStrategy()->toSql();

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
    protected function havingToString($useClause = true): string
    {
        return $this->havingToSql($useClause)[0];
    }

    /**
     * @return DialectStrategy
     */
    abstract public function dialect(): DialectStrategy;
}
