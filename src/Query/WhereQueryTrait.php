<?php

namespace Greg\Orm\Query;

trait WhereQueryTrait
{
    use ConditionsQueryTrait;

    public function hasWhere()
    {
        return $this->hasConditions();
    }

    public function clearWhere()
    {
        return $this->clearConditions();
    }

    public function where($expr = null, $value = null, $_ = null)
    {
        return $this->condition(...func_get_args());
    }

    public function orWhere($expr, $value = null, $_ = null)
    {
        return $this->orCondition(...func_get_args());
    }

    public function whereRel($column1, $operator, $column2 = null)
    {
        return $this->conditionRel(...func_get_args());
    }

    public function orWhereRel($column1, $operator, $column2 = null)
    {
        return $this->orConditionRel(...func_get_args());
    }

    public function whereCols(array $columns)
    {
        return $this->conditionCols(...func_get_args());
    }

    public function whereCol($column, $operator, $value = null)
    {
        return $this->conditionCol(...func_get_args());
    }

    public function orWhereCols(array $columns)
    {
        return $this->orConditionCols(...func_get_args());
    }

    public function orWhereCol($column, $operator, $value = null)
    {
        return $this->orConditionCol(...func_get_args());
    }

    protected function newConditions()
    {
        return new WhereQuery($this->getStorage());
    }

    public function whereToString($useTag = true)
    {
        $condition = $this->conditionsToString();

        return $condition ? ($useTag ? 'WHERE ' : '') . $condition : '';
    }
}