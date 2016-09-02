<?php

namespace Greg\Orm\Query;

interface WhereQueryTraitInterface extends ConditionsQueryTraitInterface
{
    /**
     * @param array $columns
     * @return $this
     */
    public function whereAre(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return $this
     */
    public function where($column, $operator, $value = null);

    /**
     * @param array $columns
     * @return $this
     */
    public function orWhereAre(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return $this
     */
    public function orWhere($column, $operator, $value = null);

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     * @return $this
     */
    public function whereRel($column1, $operator, $column2 = null);

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     * @return $this
     */
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

    /**
     * @param $expr
     * @param null $value
     * @param null $_
     * @return $this
     */
    public function whereRaw($expr, $value = null, $_ = null);

    /**
     * @param $expr
     * @param null $value
     * @param null $_
     * @return $this
     */
    public function orWhereRaw($expr, $value = null, $_ = null);

    public function hasWhere();

    /**
     * @return $this
     */
    public function clearWhere();

    public function whereExists($expr, $param = null, $_ = null);

    public function whereNotExists($expr, $param = null, $_ = null);

    public function whereToSql($useClause = true);

    public function whereToString($useClause = true);
}
