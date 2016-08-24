<?php

namespace Greg\Orm\Query;

trait OnQueryTrait
{
    use ConditionsQueryTrait;

    public function hasOn()
    {
        return $this->hasConditions();
    }

    public function clearOn()
    {
        return $this->clearConditions();
    }

    public function on($expr = null, $value = null, $_ = null)
    {
        return $this->condition(...func_get_args());
    }

    public function orOn($expr, $value = null, $_ = null)
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

    public function onCols(array $columns)
    {
        return $this->conditionCols(...func_get_args());
    }

    public function onCol($column, $operator, $value = null)
    {
        return $this->conditionCol(...func_get_args());
    }

    public function orOnCols(array $columns)
    {
        return $this->orConditionCols(...func_get_args());
    }

    public function orOnCol($column, $operator, $value = null)
    {
        return $this->orConditionCol(...func_get_args());
    }

    protected function newConditions()
    {
        return new OnQuery($this->getStorage());
    }

    public function onToString($useTag = true)
    {
        $condition = $this->conditionsToString();

        return $condition ? ($useTag ? 'ON ' : '') . $condition : '';
    }
}