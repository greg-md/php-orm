<?php

namespace Greg\Orm\TableQuery;

interface TableSelectQueryTraitInterface
{
    public function getSelectQuery();

    public function intoSelect($column = null, $_ = null);

    public function select($column = null, $_ = null);

    public function distinct($value = true);

    public function only($column, $_ = null);

    public function selectFrom($table, $column = null, $_ = null);

    public function columnsFrom($table, $column, $_ = null);
}