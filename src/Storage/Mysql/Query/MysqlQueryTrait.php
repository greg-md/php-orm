<?php

namespace Greg\Orm\Storage\Mysql\Query;

use Greg\Support\Arr;

trait MysqlQueryTrait
{
    public function concat($array, $delimiter = '')
    {
        return sizeof($array) > 1 ? 'concat_ws("' . $delimiter . '", ' . implode(', ', $array) . ')' : Arr::first($array);
    }
}