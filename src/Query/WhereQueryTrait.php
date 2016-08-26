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

    public function whereRaw($expr, $value = null, $_ = null)
    {
        return $this->conditionRaw(...func_get_args());
    }

    public function orWhereRaw($expr, $value = null, $_ = null)
    {
        return $this->orConditionRaw(...func_get_args());
    }

    public function whereRel($column1, $operator, $column2 = null)
    {
        return $this->conditionRel(...func_get_args());
    }

    public function orWhereRel($column1, $operator, $column2 = null)
    {
        return $this->orConditionRel(...func_get_args());
    }

    public function whereAre(array $columns)
    {
        return $this->conditions(...func_get_args());
    }

    public function where($column, $operator, $value = null)
    {
        return $this->condition(...func_get_args());
    }

    public function orWhereAre(array $columns)
    {
        return $this->orConditions(...func_get_args());
    }

    public function orWhere($column, $operator, $value = null)
    {
        return $this->orCondition(...func_get_args());
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