<?php

namespace Greg\Orm\TableQuery;

interface TableFromQueryTraitInterface
{
    public function intoFrom();

    public function getFromClause();


    public function from($table, $_ = null);

    public function fromRaw($expr, $param = null, $_ = null);


    public function hasFrom();

    public function clearFrom();
}