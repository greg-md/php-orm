<?php

namespace Greg\Orm\Query;

trait HavingQueryTrait
{
    /**
     * @var HavingConditionsQuery|null
     */
    protected $havingConditions = null;

    protected function havingConditions()
    {
        if (!$this->havingConditions) {
            $this->havingConditions = new HavingConditionsQuery();
        }

        return $this->havingConditions;
    }

    public function havingAre(array $columns)
    {
        return $this->havingConditions()->conditions(...func_get_args());
    }

    public function having($column, $operator, $value = null)
    {
        return $this->havingConditions()->condition(...func_get_args());
    }

    public function orHavingAre(array $columns)
    {
        return $this->havingConditions()->orConditions(...func_get_args());
    }

    public function orHaving($column, $operator, $value = null)
    {
        return $this->havingConditions()->orCondition(...func_get_args());
    }

    public function havingRel($column1, $operator, $column2 = null)
    {
        return $this->havingConditions()->conditionRel(...func_get_args());
    }

    public function orHavingRel($column1, $operator, $column2 = null)
    {
        return $this->havingConditions()->orConditionRel(...func_get_args());
    }

    public function havingIsNull($column)
    {
        return $this->havingConditions()->conditionIsNull($column);
    }

    public function orHavingIsNull($column)
    {
        return $this->havingConditions()->orConditionIsNull($column);
    }

    public function havingIsNotNull($column)
    {
        return $this->havingConditions()->conditionIsNotNull($column);
    }

    public function orHavingIsNotNull($column)
    {
        return $this->havingConditions()->orConditionIsNotNull($column);
    }

    public function havingBetween($column, $min, $max)
    {
        return $this->havingConditions()->conditionBetween($column, $min, $max);
    }

    public function orHavingBetween($column, $min, $max)
    {
        return $this->havingConditions()->orConditionBetween($column, $min, $max);
    }

    public function havingNotBetween($column, $min, $max)
    {
        return $this->havingConditions()->conditionNotBetween($column, $min, $max);
    }

    public function orHavingNotBetween($column, $min, $max)
    {
        return $this->havingConditions()->orConditionNotBetween($column, $min, $max);
    }

    public function havingDate($column, $date)
    {
        return $this->havingConditions()->conditionDate($column, $date);
    }

    public function orHavingDate($column, $date)
    {
        return $this->havingConditions()->orConditionDate($column, $date);
    }

    public function havingTime($column, $date)
    {
        return $this->havingConditions()->conditionTime($column, $date);
    }

    public function orHavingTime($column, $date)
    {
        return $this->havingConditions()->orConditionTime($column, $date);
    }

    public function havingYear($column, $year)
    {
        return $this->havingConditions()->conditionYear($column, $year);
    }

    public function orHavingYear($column, $year)
    {
        return $this->havingConditions()->orConditionYear($column, $year);
    }

    public function havingMonth($column, $month)
    {
        return $this->havingConditions()->conditionMonth($column, $month);
    }

    public function orHavingMonth($column, $month)
    {
        return $this->havingConditions()->orConditionMonth($column, $month);
    }

    public function havingDay($column, $day)
    {
        return $this->havingConditions()->conditionDay($column, $day);
    }

    public function orHavingDay($column, $day)
    {
        return $this->havingConditions()->orConditionDay($column, $day);
    }

    public function havingRaw($expr, $value = null, $_ = null)
    {
        return $this->havingConditions()->conditionRaw(...func_get_args());
    }

    public function orHavingRaw($expr, $value = null, $_ = null)
    {
        return $this->havingConditions()->orConditionRaw(...func_get_args());
    }

    public function hasHaving()
    {
        return $this->havingConditions()->hasConditions();
    }

    public function getHaving()
    {
        return $this->havingConditions()->hasConditions();
    }

    public function addHaving(array $conditions)
    {
        $this->havingConditions()->addConditions($conditions);

        return $this;
    }

    public function setHaving(array $conditions)
    {
        $this->havingConditions()->setConditions($conditions);

        return $this;
    }

    public function clearHaving()
    {
        $this->havingConditions()->clearConditions();

        return $this;
    }

    protected function havingToSql($useClause = true)
    {
        list($sql, $params) = $this->havingConditions()->toSql();

        if ($sql and $useClause) {
            $sql = 'HAVING ' . $sql;
        }

        return [$sql, $params];
    }

    protected function havingToString($useClause = true)
    {
        return $this->havingToSql($useClause)[0];
    }
}