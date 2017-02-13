<?php

namespace Greg\Orm\TableQuery;

interface WhereTableClauseTraitInterface
{
    public function intoWhere();

    public function getWhereClause();

    public function whereAre(array $columns);

    public function where($column, $operator, $value = null);

    public function orWhereAre(array $columns);

    public function orWhere($column, $operator, $value = null);

    public function whereRel($column1, $operator, $column2 = null);

    public function orWhereRel($column1, $operator, $column2 = null);

    public function whereIsNull($column);

    public function orWhereIsNull($column);

    public function whereIsNotNull($column);

    public function orWhereIsNotNull($column);

    public function whereBetween($column, $min, $max);

    public function orWhereBetween($column, $min, $max);

    public function whereNotBetween($column, $min, $max);

    public function orWhereNotBetween($column, $min, $max);

    public function whereDate($column, $date);

    public function orWhereDate($column, $date);

    public function whereTime($column, $date);

    public function orWhereTime($column, $date);

    public function whereYear($column, $year);

    public function orWhereYear($column, $year);

    public function whereMonth($column, $month);

    public function orWhereMonth($column, $month);

    public function whereDay($column, $day);

    public function orWhereDay($column, $day);

    public function whereRaw($sql, $value = null, $_ = null);

    public function orWhereRaw($sql, $value = null, $_ = null);

    public function hasWhere();

    public function clearWhere();
}
