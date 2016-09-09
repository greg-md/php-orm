<?php

namespace Greg\Orm\TableQuery;

interface TableDeleteQueryTraitInterface
{
    public function intoDelete(array $whereAre = []);

    public function getDeleteQuery();


    public function fromTable($table);


    public function delete();

    public function truncate();

    public function erase($key);
}