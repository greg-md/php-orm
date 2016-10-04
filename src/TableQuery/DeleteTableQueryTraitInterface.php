<?php

namespace Greg\Orm\TableQuery;

interface DeleteTableQueryTraitInterface
{
    public function intoDelete();

    public function getDeleteQuery();

    public function fromTable($table);

    public function delete();

    public function truncate();

    public function erase($key);
}
