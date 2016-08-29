<?php

namespace Greg\Orm\Query;

interface OnQueryTraitInterface extends ConditionsQueryTraitInterface
{
    /**
     * @param array $columns
     * @return $this
     */
    public function onAre(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return $this
     */
    public function on($column, $operator, $value = null);

    /**
     * @param array $columns
     * @return $this
     */
    public function orOnAre(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return $this
     */
    public function orOn($column, $operator, $value = null);

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     * @return $this
     */
    public function onRel($column1, $operator, $column2 = null);

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     * @return $this
     */
    public function orOnRel($column1, $operator, $column2 = null);

    public function onIsNull($column);

    public function orOnIsNull($column);

    public function onIsNotNull($column);

    public function orOnIsNotNull($column);

    public function onBetween($column, $min, $max);

    public function orOnBetween($column, $min, $max);

    public function onNotBetween($column, $min, $max);

    public function orOnNotBetween($column, $min, $max);

    public function onDate($column, $date);

    public function orOnDate($column, $date);

    public function onTime($column, $date);

    public function orOnTime($column, $date);

    public function onYear($column, $year);

    public function orOnYear($column, $year);

    public function onMonth($column, $month);

    public function orOnMonth($column, $month);

    public function onDay($column, $day);

    public function orOnDay($column, $day);

    /**
     * @param $expr
     * @param null $value
     * @param null $_
     * @return $this
     */
    public function onRaw($expr, $value = null, $_ = null);

    /**
     * @param $expr
     * @param null $value
     * @param null $_
     * @return $this
     */
    public function orOnRaw($expr, $value = null, $_ = null);

    public function hasOn();

    public function clearOn();

    public function onExists($expr, $param = null, $_ = null);

    public function onNotExists($expr, $param = null, $_ = null);

    public function onToSql();

    public function onToString();
}
