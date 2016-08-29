<?php

namespace Greg\Orm\Query;

interface SelectQueryInterface extends QueryTraitInterface, FromQueryTraitInterface, WhereQueryTraitInterface
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
    public function from($table, $column = null, $_ = null);

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

    /**
     * @param $expr
     * @return $this
     */
    public function group($expr);

    /**
     * @param $expr
     * @param null $param
     * @param null $_
     * @return $this
     */
    public function groupRaw($expr, $param = null, $_ = null);

    /**
     * @return bool
     */
    public function hasGroup();

    /**
     * @return $this
     */
    public function clearGroup();

    public function groupToSql();

    public function groupToString();

    /**
     * @param $column
     * @param null $type
     * @return $this
     */
    public function order($column, $type = null);

    /**
     * @param $expr
     * @param null $param
     * @param null $_
     * @return $this
     */
    public function orderRaw($expr, $param = null, $_ = null);

    /**
     * @return bool
     */
    public function hasOrder();

    /**
     * @return $this
     */
    public function clearOrder();

    public function orderToSql();

    public function orderToString();

    /**
     * @param $number
     * @return $this
     */
    public function limit($number);

    /**
     * @param $number
     * @return $this
     */
    public function offset($number);

    public function selectStmtToSql();

    public function selectStmtToString();

    public function selectToSql();

    public function selectToString();

    /**
     * @return array|null
     */
    public function assoc();

    /**
     * @return array
     */
    public function assocAll();

    /**
     * @return array
     */
    public function assocAllGenerator();

    /**
     * @param int $column
     * @return array
     */
    public function col($column = 0);

    public function one($column = 0);

    /**
     * @param int $key
     * @param int $value
     * @return array
     */
    public function pairs($key = 0, $value = 1);

    /**
     * @return bool
     */
    public function exists();
}
