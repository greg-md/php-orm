<?php

namespace Greg\Orm\Query;

interface ConditionsQueryTraitInterface
{
    public function conditions(array $columns);

    public function condition($column, $operator, $value = null);

    public function orConditions(array $columns);

    public function orCondition($column, $operator, $value = null);

    public function conditionRel($column1, $operator, $column2 = null);

    public function orConditionRel($column1, $operator, $column2 = null);

    public function conditionIsNull($column);

    public function orConditionIsNull($column);

    public function conditionIsNotNull($column);

    public function orConditionIsNotNull($column);

    public function conditionBetween($column, $min, $max);

    public function orConditionBetween($column, $min, $max);

    public function conditionNotBetween($column, $min, $max);

    public function orConditionNotBetween($column, $min, $max);

    public function conditionDate($column, $date);

    public function orConditionDate($column, $date);

    public function conditionTime($column, $date);

    public function orConditionTime($column, $date);

    public function conditionYear($column, $year);

    public function orConditionYear($column, $year);

    public function conditionMonth($column, $month);

    public function orConditionMonth($column, $month);

    public function conditionDay($column, $day);

    public function orConditionDay($column, $day);

    public function conditionRaw($expr, $value = null, $_ = null);

    public function orConditionRaw($expr, $value = null, $_ = null);

    public function hasConditions();

    public function clearConditions();

    public function conditionsToSql();

    public function conditionsToString();
}
