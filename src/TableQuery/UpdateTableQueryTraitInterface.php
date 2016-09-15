<?php

namespace Greg\Orm\TableQuery;

interface UpdateTableQueryTraitInterface
{
    public function intoUpdate();

    public function getUpdateQuery();


    public function table($table, $_ = null);


    public function setForUpdate($key, $value = null);

    public function setRawForUpdate($raw, $param = null, $_ = null);


    public function increment($column, $value = 1);

    public function decrement($column, $value = 1);


    public function update(array $set = []);
}