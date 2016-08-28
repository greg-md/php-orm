<?php

namespace Greg\Orm\Query;

interface ConditionsQueryTraitInterface
{
    public function hasConditions();

    public function clearConditions();

    public function isNull($expr);

    public function orIsNull($expr);

    public function isNotNull($expr);

    public function orIsNotNull($expr);

    public function conditionRaw($expr, $value = null, $_ = null);

    public function orConditionRaw($expr, $value = null, $_ = null);

    public function conditionRel($column1, $operator, $column2 = null);

    public function orConditionRel($column1, $operator, $column2 = null);

    public function conditions(array $columns);

    public function condition($column, $operator, $value = null);

    public function orConditions(array $columns);

    public function orCondition($column, $operator, $value = null);

    public function conditionsToSql();

    public function conditionsToString();
}
