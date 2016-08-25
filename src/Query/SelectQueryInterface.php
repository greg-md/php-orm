<?php

namespace Greg\Orm\Query;

interface SelectQueryInterface
{
    public function distinct($value = true);

    public function from($table, $column = null, $_ = null);

    public function columnsFrom($table, $column, $_ = null);

    public function column($column, $alias = null);

    public function columns($column, $_ = null);

    public function clearColumns();

    public function group($expr);

    public function hasGroup();

    public function clearGroup();

    public function order($expr, $type = null);

    public function limit($number);

    public function offset($number);

    public function groupToString();

    public function orderToString();

    public function selectToString();

    public function hasOrder();

    public function clearOrder();

    public function assocAll();

    public function assoc();

    public function pairs($key = 0, $value = 1);

    public function exists();

    public function one($column = 0);

    public function col($column = 0);

    public function stmt($execute = true);
}
