<?php

namespace Greg\Orm\Query;

trait OnClauseTrait
{
    /**
     * @var OnConditionsExpr|null
     */
    protected $onConditions = null;

    protected function onConditions()
    {
        if (!$this->onConditions) {
            $this->onConditions = new HavingConditionsExpr();
        }

        return $this->onConditions;
    }

    public function onAre(array $columns)
    {
        $this->onConditions()->conditions(...func_get_args());

        return $this;
    }

    public function on($column, $operator, $value = null)
    {
        $this->onConditions()->condition(...func_get_args());

        return $this;
    }

    public function orOnAre(array $columns)
    {
        $this->onConditions()->orConditions(...func_get_args());

        return $this;
    }

    public function orOn($column, $operator, $value = null)
    {
        $this->onConditions()->orCondition(...func_get_args());

        return $this;
    }

    public function onRel($column1, $operator, $column2 = null)
    {
        $this->onConditions()->conditionRel(...func_get_args());

        return $this;
    }

    public function orOnRel($column1, $operator, $column2 = null)
    {
        $this->onConditions()->orConditionRel(...func_get_args());

        return $this;
    }

    public function onIsNull($column)
    {
        $this->onConditions()->conditionIsNull($column);

        return $this;
    }

    public function orOnIsNull($column)
    {
        $this->onConditions()->orConditionIsNull($column);

        return $this;
    }

    public function onIsNotNull($column)
    {
        $this->onConditions()->conditionIsNotNull($column);

        return $this;
    }

    public function orOnIsNotNull($column)
    {
        $this->onConditions()->orConditionIsNotNull($column);

        return $this;
    }

    public function onBetween($column, $min, $max)
    {
        $this->onConditions()->conditionBetween($column, $min, $max);

        return $this;
    }

    public function orOnBetween($column, $min, $max)
    {
        $this->onConditions()->orConditionBetween($column, $min, $max);

        return $this;
    }

    public function onNotBetween($column, $min, $max)
    {
        $this->onConditions()->conditionNotBetween($column, $min, $max);

        return $this;
    }

    public function orOnNotBetween($column, $min, $max)
    {
        $this->onConditions()->orConditionNotBetween($column, $min, $max);

        return $this;
    }

    public function onDate($column, $date)
    {
        $this->onConditions()->conditionDate($column, $date);

        return $this;
    }

    public function orOnDate($column, $date)
    {
        $this->onConditions()->orConditionDate($column, $date);

        return $this;
    }

    public function onTime($column, $date)
    {
        $this->onConditions()->conditionTime($column, $date);

        return $this;
    }

    public function orOnTime($column, $date)
    {
        $this->onConditions()->orConditionTime($column, $date);

        return $this;
    }

    public function onYear($column, $year)
    {
        $this->onConditions()->conditionYear($column, $year);

        return $this;
    }

    public function orOnYear($column, $year)
    {
        $this->onConditions()->orConditionYear($column, $year);

        return $this;
    }

    public function onMonth($column, $month)
    {
        $this->onConditions()->conditionMonth($column, $month);

        return $this;
    }

    public function orOnMonth($column, $month)
    {
        $this->onConditions()->orConditionMonth($column, $month);

        return $this;
    }

    public function onDay($column, $day)
    {
        $this->onConditions()->conditionDay($column, $day);

        return $this;
    }

    public function orOnDay($column, $day)
    {
        $this->onConditions()->orConditionDay($column, $day);

        return $this;
    }

    public function onRaw($expr, $value = null, $_ = null)
    {
        $this->onConditions()->condition(...func_get_args());

        return $this;
    }

    public function orOnRaw($expr, $value = null, $_ = null)
    {
        $this->onConditions()->orCondition(...func_get_args());

        return $this;
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
