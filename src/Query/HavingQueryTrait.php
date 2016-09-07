<?php

namespace Greg\Orm\Query;

trait HavingQueryTrait
{
    use ConditionsQueryTrait;

    public function havingAre(array $columns)
    {
        return $this->conditions(...func_get_args());
    }

    public function having($column, $operator, $value = null)
    {
        return $this->condition(...func_get_args());
    }

    public function orHavingAre(array $columns)
    {
        return $this->orConditions(...func_get_args());
    }

    public function orHaving($column, $operator, $value = null)
    {
        return $this->orCondition(...func_get_args());
    }

    public function havingRel($column1, $operator, $column2 = null)
    {
        return $this->conditionRel(...func_get_args());
    }

    public function orHavingRel($column1, $operator, $column2 = null)
    {
        return $this->orConditionRel(...func_get_args());
    }

    public function havingIsNull($column)
    {
        return $this->conditionIsNull($column);
    }

    public function orHavingIsNull($column)
    {
        return $this->orConditionIsNull($column);
    }

    public function havingIsNotNull($column)
    {
        return $this->conditionIsNotNull($column);
    }

    public function orHavingIsNotNull($column)
    {
        return $this->orConditionIsNotNull($column);
    }

    public function havingBetween($column, $min, $max)
    {
        return $this->conditionBetween($column, $min, $max);
    }

    public function orHavingBetween($column, $min, $max)
    {
        return $this->orConditionBetween($column, $min, $max);
    }

    public function havingNotBetween($column, $min, $max)
    {
        return $this->conditionNotBetween($column, $min, $max);
    }

    public function orHavingNotBetween($column, $min, $max)
    {
        return $this->orConditionNotBetween($column, $min, $max);
    }

    public function havingDate($column, $date)
    {
        return $this->conditionDate($column, $date);
    }

    public function orHavingDate($column, $date)
    {
        return $this->orConditionDate($column, $date);
    }

    public function havingTime($column, $date)
    {
        return $this->conditionTime($column, $date);
    }

    public function orHavingTime($column, $date)
    {
        return $this->orConditionTime($column, $date);
    }

    public function havingYear($column, $year)
    {
        return $this->conditionYear($column, $year);
    }

    public function orHavingYear($column, $year)
    {
        return $this->orConditionYear($column, $year);
    }

    public function havingMonth($column, $month)
    {
        return $this->conditionMonth($column, $month);
    }

    public function orHavingMonth($column, $month)
    {
        return $this->orConditionMonth($column, $month);
    }

    public function havingDay($column, $day)
    {
        return $this->conditionDay($column, $day);
    }

    public function orHavingDay($column, $day)
    {
        return $this->orConditionDay($column, $day);
    }

    public function havingRaw($expr, $value = null, $_ = null)
    {
        return $this->conditionRaw(...func_get_args());
    }

    public function orHavingRaw($expr, $value = null, $_ = null)
    {
        return $this->orConditionRaw(...func_get_args());
    }

    public function hasHaving()
    {
        return $this->hasConditions();
    }

    public function getHaving()
    {
        return $this->hasConditions();
    }

    public function addHaving(array $conditions)
    {
        return $this->addConditions($conditions);
    }

    public function setHaving(array $conditions)
    {
        return $this->setConditions($conditions);
    }

    public function clearHaving()
    {
        return $this->clearConditions();
    }

    protected function newConditions()
    {
        return new HavingQuery();
    }

    protected function havingToSql($useClause = true)
    {
        list($sql, $params) = $this->conditionsToSql();

        if ($sql and $useClause) {
            $sql = 'WHERE ' . $sql;
        }

        return [$sql, $params];
    }

    protected function havingToString($useClause = true)
    {
        return $this->havingToSql($useClause)[0];
    }
}