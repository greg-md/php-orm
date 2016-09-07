<?php

namespace Greg\Orm\Query;

interface UpdateQueryInterface extends QueryTraitInterface, WhereQueryTraitInterface
{
    public function table($table, $_ = null);

    public function set($key, $value = null);

    public function setRaw($raw, $param = null, $_ = null);

    public function increment($column, $value = 1);

    public function decrement($column, $value = 1);
}
