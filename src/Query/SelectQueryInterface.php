<?php

namespace Greg\Orm\Query;

interface SelectQueryInterface
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

    /**
     * @param $column
     * @param null $_
     * @return $this
     */
    public function columns($column, $_ = null);

    /**
     * @param $column
     * @param null $_
     * @return $this
     */
    public function columnsRaw($column, $_ = null);

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
     * @return bool
     */
    public function hasGroup();

    /**
     * @return $this
     */
    public function clearGroup();

    /**
     * @param $expr
     * @param null $type
     * @return $this
     */
    public function order($expr, $type = null);

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

    public function groupToString();

    public function orderToString();

    public function selectToString();

    /**
     * @return bool
     */
    public function hasOrder();

    /**
     * @return $this
     */
    public function clearOrder();

    /**
     * @return array
     */
    public function assocAll();

    public function assoc();

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

    public function one($column = 0);

    /**
     * @param int $column
     * @return array
     */
    public function col($column = 0);

    public function stmt($execute = true);
}
