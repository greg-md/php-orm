<?php

namespace Greg\Orm\Query;

interface UpdateQueryInterface extends QueryTraitInterface, WhereQueryTraitInterface
{
    public function table($table, $_ = null);

    public function set($key, $value = null);

    public function exec();
}
