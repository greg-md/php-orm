<?php

namespace Greg\Orm\TableQuery;

interface HavingTableClauseTraitInterface
{
    public function intoHaving();

    public function getHavingClause();

    public function havingAre(array $columns);

    public function having($column, $operator, $value = null);

    public function orHavingAre(array $columns);

    public function orHaving($column, $operator, $value = null);

    public function havingRel($column1, $operator, $column2 = null);

    public function orHavingRel($column1, $operator, $column2 = null);

    public function havingIsNull($column);

    public function orHavingIsNull($column);

    public function havingIsNotNull($column);

    public function orHavingIsNotNull($column);

    public function havingBetween($column, $min, $max);

    public function orHavingBetween($column, $min, $max);

    public function havingNotBetween($column, $min, $max);

    public function orHavingNotBetween($column, $min, $max);

    public function havingDate($column, $date);

    public function orHavingDate($column, $date);

    public function havingTime($column, $date);

    public function orHavingTime($column, $date);

    public function havingYear($column, $year);

    public function orHavingYear($column, $year);

    public function havingMonth($column, $month);

    public function orHavingMonth($column, $month);

    public function havingDay($column, $day);

    public function orHavingDay($column, $day);

    public function havingRaw($expr, $value = null, $_ = null);

    public function orHavingRaw($expr, $value = null, $_ = null);

    public function hasHaving();

    public function clearHaving();
}
