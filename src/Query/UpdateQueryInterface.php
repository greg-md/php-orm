<?php

namespace Greg\Orm\Query;

interface UpdateQueryInterface
{
    public function table($table, $_ = null);

    public function set(array $values);

    public function exec();
}
