<?php

namespace Greg\Orm\Query;

trait HavingClauseTrait
{
    /**
     * @var HavingConditionsExpr|null
     */
    protected $havingConditions = null;

    protected function havingConditions()
    {
        if (!$this->havingConditions) {
            $this->havingConditions = new HavingConditionsExpr();
        }

        return $this->havingConditions;
    }

    public function havingAre(array $columns)
    {
        $this->havingConditions()->conditions(...func_get_args());

        return $this;
    }

    public function having($column, $operator, $value = null)
    {
        $this->havingConditions()->condition(...func_get_args());

        return $this;
    }

    public function orHavingAre(array $columns)
    {
        $this->havingConditions()->orConditions(...func_get_args());

        return $this;
    }

    public function orHaving($column, $operator, $value = null)
    {
        $this->havingConditions()->orCondition(...func_get_args());

        return $this;
    }

    public function havingRel($column1, $operator, $column2 = null)
    {
        $this->havingConditions()->conditionRel(...func_get_args());

        return $this;
    }

    public function orHavingRel($column1, $operator, $column2 = null)
    {
        $this->havingConditions()->orConditionRel(...func_get_args());

        return $this;
    }

    public function havingIsNull($column)
    {
        $this->havingConditions()->conditionIsNull($column);

        return $this;
    }

    public function orHavingIsNull($column)
    {
        $this->havingConditions()->orConditionIsNull($column);

        return $this;
    }

    public function havingIsNotNull($column)
    {
        $this->havingConditions()->conditionIsNotNull($column);

        return $this;
    }

    public function orHavingIsNotNull($column)
    {
        $this->havingConditions()->orConditionIsNotNull($column);

        return $this;
    }

    public function havingBetween($column, $min, $max)
    {
        $this->havingConditions()->conditionBetween($column, $min, $max);

        return $this;
    }

    public function orHavingBetween($column, $min, $max)
    {
        $this->havingConditions()->orConditionBetween($column, $min, $max);

        return $this;
    }

    public function havingNotBetween($column, $min, $max)
    {
        $this->havingConditions()->conditionNotBetween($column, $min, $max);

        return $this;
    }

    public function orHavingNotBetween($column, $min, $max)
    {
        $this->havingConditions()->orConditionNotBetween($column, $min, $max);

        return $this;
    }

    public function havingDate($column, $date)
    {
        $this->havingConditions()->conditionDate($column, $date);

        return $this;
    }

    public function orHavingDate($column, $date)
    {
        $this->havingConditions()->orConditionDate($column, $date);

        return $this;
    }

    public function havingTime($column, $date)
    {
        $this->havingConditions()->conditionTime($column, $date);

        return $this;
    }

    public function orHavingTime($column, $date)
    {
        $this->havingConditions()->orConditionTime($column, $date);

        return $this;
    }

    public function havingYear($column, $year)
    {
        $this->havingConditions()->conditionYear($column, $year);

        return $this;
    }

    public function orHavingYear($column, $year)
    {
        $this->havingConditions()->orConditionYear($column, $year);

        return $this;
    }

    public function havingMonth($column, $month)
    {
        $this->havingConditions()->conditionMonth($column, $month);

        return $this;
    }

    public function orHavingMonth($column, $month)
    {
        $this->havingConditions()->orConditionMonth($column, $month);

        return $this;
    }

    public function havingDay($column, $day)
    {
        $this->havingConditions()->conditionDay($column, $day);

        return $this;
    }

    public function orHavingDay($column, $day)
    {
        $this->havingConditions()->orConditionDay($column, $day);

        return $this;
    }

    public function havingRaw($expr, $value = null, $_ = null)
    {
        $this->havingConditions()->conditionRaw(...func_get_args());

        return $this;
    }

    public function orHavingRaw($expr, $value = null, $_ = null)
    {
        $this->havingConditions()->orConditionRaw(...func_get_args());

        return $this;
    }

    public function hasHaving()
    {
        return $this->havingConditions()->hasConditions();
    }

    public function getHaving()
    {
        return $this->havingConditions()->hasConditions();
    }

    public function addHaving(array $conditions)
    {
        $this->havingConditions()->addConditions($conditions);

        return $this;
    }

    public function setHaving(array $conditions)
    {
        $this->havingConditions()->setConditions($conditions);

        return $this;
    }

    public function clearHaving()
    {
        $this->havingConditions()->clearConditions();

        return $this;
    }

    protected function havingToSql($useClause = true)
    {
        list($sql, $params) = $this->havingConditions()->toSql();

        if ($sql and $useClause) {
            $sql = 'HAVING ' . $sql;
        }

        return [$sql, $params];
    }

    protected function havingToString($useClause = true)
    {
        return $this->havingToSql($useClause)[0];
    }
}
