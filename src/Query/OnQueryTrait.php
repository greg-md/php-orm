<?php

namespace Greg\Orm\Query;

trait OnQueryTrait
{
    use ConditionsQueryTrait;

    public function onAre(array $columns)
    {
        return $this->conditions(...func_get_args());
    }

    public function on($column, $operator, $value = null)
    {
        return $this->condition(...func_get_args());
    }

    public function orOnAre(array $columns)
    {
        return $this->orConditions(...func_get_args());
    }

    public function orOn($column, $operator, $value = null)
    {
        return $this->orCondition(...func_get_args());
    }

    public function onRel($column1, $operator, $column2 = null)
    {
        return $this->conditionRel(...func_get_args());
    }

    public function orOnRel($column1, $operator, $column2 = null)
    {
        return $this->orConditionRel(...func_get_args());
    }

    public function onIsNull($column)
    {
        return $this->conditionIsNull($column);
    }

    public function orOnIsNull($column)
    {
        return $this->orConditionIsNull($column);
    }

    public function onIsNotNull($column)
    {
        return $this->conditionIsNotNull($column);
    }

    public function orOnIsNotNull($column)
    {
        return $this->orConditionIsNotNull($column);
    }

    public function onBetween($column, $min, $max)
    {
        return $this->conditionBetween($column, $min, $max);
    }

    public function orOnBetween($column, $min, $max)
    {
        return $this->orConditionBetween($column, $min, $max);
    }

    public function onNotBetween($column, $min, $max)
    {
        return $this->conditionNotBetween($column, $min, $max);
    }

    public function orOnNotBetween($column, $min, $max)
    {
        return $this->orConditionNotBetween($column, $min, $max);
    }

    public function onDate($column, $date)
    {
        return $this->conditionDate($column, $date);
    }

    public function orOnDate($column, $date)
    {
        return $this->orConditionDate($column, $date);
    }

    public function onTime($column, $date)
    {
        return $this->conditionTime($column, $date);
    }

    public function orOnTime($column, $date)
    {
        return $this->orConditionTime($column, $date);
    }

    public function onYear($column, $year)
    {
        return $this->conditionYear($column, $year);
    }

    public function orOnYear($column, $year)
    {
        return $this->orConditionYear($column, $year);
    }

    public function onMonth($column, $month)
    {
        return $this->conditionMonth($column, $month);
    }

    public function orOnMonth($column, $month)
    {
        return $this->orConditionMonth($column, $month);
    }

    public function onDay($column, $day)
    {
        return $this->conditionDay($column, $day);
    }

    public function orOnDay($column, $day)
    {
        return $this->orConditionDay($column, $day);
    }

    public function onRaw($expr, $value = null, $_ = null)
    {
        return $this->condition(...func_get_args());
    }

    public function orOnRaw($expr, $value = null, $_ = null)
    {
        return $this->orCondition(...func_get_args());
    }

    public function hasOn()
    {
        return $this->hasConditions();
    }

    public function getOn()
    {
        return $this->getConditions();
    }

    public function addOn(array $conditions)
    {
        return $this->addConditions($conditions);
    }

    public function setOn(array $conditions)
    {
        return $this->setConditions($conditions);
    }

    public function clearOn()
    {
        return $this->clearConditions();
    }

    protected function newConditions()
    {
        return new OnQuery($this->getStorage());
    }

    protected function subQueryToSql(OnQueryInterface $query)
    {
        return $query->onToSql(false);
    }

    public function onToSql($useClause = true)
    {
        list($sql, $params) = $this->conditionsToSql();

        if ($sql and $useClause) {
            $sql = 'ON ' . $sql;
        }

        return [$sql, $params];
    }

    public function onToString($useClause = true)
    {
        return $this->onToSql($useClause)[0];
    }
}