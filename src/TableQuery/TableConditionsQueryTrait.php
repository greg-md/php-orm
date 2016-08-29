<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\ConditionsQueryTraitInterface;

trait TableConditionsQueryTrait
{
    /**
     * @return ConditionsQueryTraitInterface
     * @throws \Exception
     */
    public function needConditionsQuery()
    {
        if (!(($query = $this->getQuery()) instanceof ConditionsQueryTraitInterface)) {
            throw new \Exception('Current query is not a condition statement.');
        }

        return $query;
    }

    public function conditions(array $columns)
    {
        $this->needConditionsQuery()->conditions($columns);

        return $this;
    }

    public function condition($column, $operator, $value = null)
    {
        $this->needConditionsQuery()->condition(...func_get_args());

        return $this;
    }

    public function orConditions(array $columns)
    {
        $this->needConditionsQuery()->orConditions($columns);

        return $this;
    }

    public function orCondition($column, $operator, $value = null)
    {
        $this->needConditionsQuery()->orCondition(...func_get_args());

        return $this;
    }

    public function conditionRel($column1, $operator, $column2 = null)
    {
        $this->needConditionsQuery()->conditionRel(...func_get_args());

        return $this;
    }

    public function orConditionRel($column1, $operator, $column2 = null)
    {
        $this->needConditionsQuery()->orConditionRel(...func_get_args());

        return $this;
    }

    public function conditionIsNull($column)
    {
        $this->needConditionsQuery()->conditionIsNull($column);

        return $this;
    }

    public function orConditionIsNull($column)
    {
        $this->needConditionsQuery()->orConditionIsNull($column);

        return $this;
    }

    public function conditionIsNotNull($column)
    {
        $this->needConditionsQuery()->conditionIsNotNull($column);

        return $this;
    }

    public function orConditionIsNotNull($column)
    {
        $this->needConditionsQuery()->orConditionIsNotNull($column);

        return $this;
    }

    public function conditionBetween($column, $min, $max)
    {
        $this->needConditionsQuery()->conditionBetween($column, $min, $max);

        return $this;
    }

    public function orConditionBetween($column, $min, $max)
    {
        $this->needConditionsQuery()->orConditionBetween($column, $min, $max);

        return $this;
    }

    public function conditionNotBetween($column, $min, $max)
    {
        $this->needConditionsQuery()->conditionNotBetween($column, $min, $max);

        return $this;
    }

    public function orConditionNotBetween($column, $min, $max)
    {
        $this->needConditionsQuery()->orConditionNotBetween($column, $min, $max);

        return $this;
    }

    public function conditionDate($column, $date)
    {
        $this->needConditionsQuery()->conditionDate($column, $date);

        return $this;
    }

    public function orConditionDate($column, $date)
    {
        $this->needConditionsQuery()->orConditionDate($column, $date);

        return $this;
    }

    public function conditionTime($column, $date)
    {
        $this->needConditionsQuery()->conditionTime($column, $date);

        return $this;
    }

    public function orConditionTime($column, $date)
    {
        $this->needConditionsQuery()->orConditionTime($column, $date);

        return $this;
    }

    public function conditionYear($column, $year)
    {
        $this->needConditionsQuery()->conditionYear($column, $year);

        return $this;
    }

    public function orConditionYear($column, $year)
    {
        $this->needConditionsQuery()->orConditionYear($column, $year);

        return $this;
    }

    public function conditionMonth($column, $month)
    {
        $this->needConditionsQuery()->conditionMonth($column, $month);

        return $this;
    }

    public function orConditionMonth($column, $month)
    {
        $this->needConditionsQuery()->orConditionMonth($column, $month);

        return $this;
    }

    public function conditionDay($column, $day)
    {
        $this->needConditionsQuery()->conditionDay($column, $day);

        return $this;
    }

    public function orConditionDay($column, $day)
    {
        $this->needConditionsQuery()->orConditionDay($column, $day);

        return $this;
    }

    public function conditionRaw($expr, $value = null, $_ = null)
    {
        $this->needConditionsQuery()->conditionRaw(...func_get_args());

        return $this;
    }

    public function orConditionRaw($expr, $value = null, $_ = null)
    {
        $this->needConditionsQuery()->orConditionRaw(...func_get_args());

        return $this;
    }

    public function hasConditions()
    {
        return $this->needConditionsQuery()->hasConditions();
    }

    public function clearConditions()
    {
        return $this->needConditionsQuery()->clearConditions();
    }

    public function conditionsToSql()
    {
        return $this->needConditionsQuery()->conditionsToSql();
    }

    public function conditionsToString()
    {
        return $this->needConditionsQuery()->conditionsToString();
    }

    abstract public function getQuery();
}