<?php

namespace Greg\Orm\Storage\Mysql\Query;

use Greg\Support\Arr;

trait MysqlQueryTrait
{
    static public function concat(array $values, $delimiter = '')
    {
        return sizeof($values) > 1 ? 'concat_ws("' . $delimiter . '", ' . implode(', ', $values) . ')' : Arr::first($values);
    }
}