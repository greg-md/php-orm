<?php

namespace Greg\Orm\TableQuery;

interface TableUpdateQueryTraitInterface
{
    public function intoUpdate(array $values = []);

    public function getUpdateQuery();

    public function table($table, $_ = null);

    public function setForUpdate($key, $value = null);

    public function setRawForUpdate($raw, $param = null, $_ = null);

    public function increment($column, $value = 1);

    public function decrement($column, $value = 1);

    public function update(array $set = []);
}