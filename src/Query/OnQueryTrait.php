<?php

namespace Greg\Orm\Query;

trait OnQueryTrait
{
    /**
     * @var OnConditionsQuery|null
     */
    protected $onConditions = null;

    protected function onConditions()
    {
        if (!$this->onConditions) {
            $this->onConditions = new HavingConditionsQuery();
        }

        return $this->onConditions;
    }

    public function onAre(array $columns)
    {
        return $this->onConditions()->conditions(...func_get_args());
    }

    public function on($column, $operator, $value = null)
    {
        return $this->onConditions()->condition(...func_get_args());
    }

    public function orOnAre(array $columns)
    {
        return $this->onConditions()->orConditions(...func_get_args());
    }

    public function orOn($column, $operator, $value = null)
    {
        return $this->onConditions()->orCondition(...func_get_args());
    }

    public function onRel($column1, $operator, $column2 = null)
    {
        return $this->onConditions()->conditionRel(...func_get_args());
    }

    public function orOnRel($column1, $operator, $column2 = null)
    {
        return $this->onConditions()->orConditionRel(...func_get_args());
    }

    public function onIsNull($column)
    {
        return $this->onConditions()->conditionIsNull($column);
    }

    public function orOnIsNull($column)
    {
        return $this->onConditions()->orConditionIsNull($column);
    }

    public function onIsNotNull($column)
    {
        return $this->onConditions()->conditionIsNotNull($column);
    }

    public function orOnIsNotNull($column)
    {
        return $this->onConditions()->orConditionIsNotNull($column);
    }

    public function onBetween($column, $min, $max)
    {
        return $this->onConditions()->conditionBetween($column, $min, $max);
    }

    public function orOnBetween($column, $min, $max)
    {
        return $this->onConditions()->orConditionBetween($column, $min, $max);
    }

    public function onNotBetween($column, $min, $max)
    {
        return $this->onConditions()->conditionNotBetween($column, $min, $max);
    }

    public function orOnNotBetween($column, $min, $max)
    {
        return $this->onConditions()->orConditionNotBetween($column, $min, $max);
    }

    public function onDate($column, $date)
    {
        return $this->onConditions()->conditionDate($column, $date);
    }

    public function orOnDate($column, $date)
    {
        return $this->onConditions()->orConditionDate($column, $date);
    }

    public function onTime($column, $date)
    {
        return $this->onConditions()->conditionTime($column, $date);
    }

    public function orOnTime($column, $date)
    {
        return $this->onConditions()->orConditionTime($column, $date);
    }

    public function onYear($column, $year)
    {
        return $this->onConditions()->conditionYear($column, $year);
    }

    public function orOnYear($column, $year)
    {
        return $this->onConditions()->orConditionYear($column, $year);
    }

    public function onMonth($column, $month)
    {
        return $this->onConditions()->conditionMonth($column, $month);
    }

    public function orOnMonth($column, $month)
    {
        return $this->onConditions()->orConditionMonth($column, $month);
    }

    public function onDay($column, $day)
    {
        return $this->onConditions()->conditionDay($column, $day);
    }

    public function orOnDay($column, $day)
    {
        return $this->onConditions()->orConditionDay($column, $day);
    }

    public function onRaw($expr, $value = null, $_ = null)
    {
        return $this->onConditions()->condition(...func_get_args());
    }

    public function orOnRaw($expr, $value = null, $_ = null)
    {
        return $this->onConditions()->orCondition(...func_get_args());
    }

    public function hasOn()
    {
        return $this->onConditions()->hasConditions();
    }

    public function getOn()
    {
        return $this->onConditions()->getConditions();
    }

    public function addOn(array $conditions)
    {
        $this->onConditions()->addConditions($conditions);

        return $this;
    }

    public function setOn(array $conditions)
    {
        $this->onConditions()->setConditions($conditions);

        return $this;
    }

    public function clearOn()
    {
        $this->onConditions()->clearConditions();

        return $this;
    }

    protected function onToSql($useClause = true)
    {
        list($sql, $params) = $this->onConditions()->toSql();

        if ($sql and $useClause) {
            $sql = 'ON ' . $sql;
        }

        return [$sql, $params];
    }

    protected function onToString($useClause = true)
    {
        return $this->onToSql($useClause)[0];
    }
}