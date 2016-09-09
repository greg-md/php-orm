<?php

namespace Greg\Orm\Query;

interface SelectQueryInterface extends
    QueryInterface,
    FromClauseTraitInterface,
    WhereClauseTraitInterface,
    HavingClauseTraitInterface,
    OrderByClauseTraitInterface,
    GroupByClauseTraitInterface,
    LimitClauseTraitInterface,
    OffsetClauseTraitInterface
{
    /**
     * @param bool $value
     * @return $this
     */
    public function distinct($value = true);


    /**
     * @param $table
     * @param null $column
     * @param null $_
     * @return $this
     */
    public function selectFrom($table, $column = null, $_ = null);


    /**
     * @param $table
     * @param $column
     * @param null $_
     * @return $this
     */
    public function columnsFrom($table, $column, $_ = null);

    /**
     * @param $column
     * @param null $_
     * @return $this
     */
    public function columns($column, $_ = null);

    /**
     * @param $column
     * @param null $alias
     * @return $this
     */
    public function column($column, $alias = null);

    /**
     * @param $column
     * @param null $alias
     * @return $this
     */
    public function columnRaw($column, $alias = null);

    public function hasColumns();
    /**
     * @return $this
     */
    public function clearColumns();


    public function count($column = '*', $alias = null);

    public function max($column, $alias = null);

    public function min($column, $alias = null);

    public function avg($column, $alias = null);

    public function sum($column, $alias = null);


    public function union($expr, $param = null, $_ = null);

    public function unionAll($expr, $param = null, $_ = null);

    public function unionDistinct($expr, $param = null, $_ = null);
}
