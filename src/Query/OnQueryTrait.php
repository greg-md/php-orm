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

    public function onRaw($expr, $value = null, $_ = null)
    {
        return $this->condition(...func_get_args());
    }

    public function orOnRaw($expr, $value = null, $_ = null)
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

    protected function newConditions()
    {
        return new OnQuery($this->getStorage());
    }

    public function onToSql()
    {
        list($sql, $params) = $this->conditionsToSql();

        if ($sql) {
            $sql = 'ON ' . $sql;
        }

        return [$sql, $params];
    }

    public function onToString()
    {
        return $this->onToSql()[0];
    }
}