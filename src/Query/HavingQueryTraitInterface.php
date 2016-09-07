<?php

namespace Greg\Orm\Query;

interface HavingQueryTraitInterface extends ConditionsQueryTraitInterface
{
    /**
     * @param array $columns
     * @return $this
     */
    public function havingAre(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return $this
     */
    public function having($column, $operator, $value = null);

    /**
     * @param array $columns
     * @return $this
     */
    public function orHavingAre(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return $this
     */
    public function orHaving($column, $operator, $value = null);


    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     * @return $this
     */
    public function havingRel($column1, $operator, $column2 = null);

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     * @return $this
     */
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


    /**
     * @param $expr
     * @param null $value
     * @param null $_
     * @return $this
     */
    public function havingRaw($expr, $value = null, $_ = null);

    /**
     * @param $expr
     * @param null $value
     * @param null $_
     * @return $this
     */
    public function orHavingRaw($expr, $value = null, $_ = null);


    public function hasHaving();

    public function getHaving();

    public function addHaving(array $conditions);

    public function setHaving(array $conditions);

    public function clearHaving();
}
