<?php

namespace Greg\Orm\TableQuery;

interface TableDeleteQueryTraitInterface
{
    public function intoDelete($column = null, $_ = null);

    public function getDeleteQuery();


    public function fromTable($table);


    public function delete();

    public function truncate();

    public function erase($key);
}