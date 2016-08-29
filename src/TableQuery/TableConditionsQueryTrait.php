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

    public function hasConditions()
    {
        return $this->needConditionsQuery()->hasConditions();
    }

    public function clearConditions()
    {
        return $this->needConditionsQuery()->clearConditions();
    }

    public function isNull($column)
    {
        $this->needConditionsQuery()->isNull($column);

        return $this;
    }

    public function orIsNull($column)
    {
        $this->needConditionsQuery()->orIsNull($column);

        return $this;
    }

    public function isNotNull($column)
    {
        $this->needConditionsQuery()->isNotNull($column);

        return $this;
    }

    public function orIsNotNull($column)
    {
        $this->needConditionsQuery()->orIsNotNull($column);

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