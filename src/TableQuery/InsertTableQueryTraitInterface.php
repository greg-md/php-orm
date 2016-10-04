<?php

namespace Greg\Orm\TableQuery;

interface InsertTableQueryTraitInterface
{
    public function insert(array $data);

    public function insertAndGetId(array $data);

    public function insertSelect($sql);
}
