<?php

namespace Greg\Orm\TableQuery;

interface TableInsertQueryTraitInterface
{
    public function insert(array $data);

    public function insertAndGetId(array $data);

    public function insertSelect($sql);
}