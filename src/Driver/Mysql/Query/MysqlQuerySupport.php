<?php

namespace Greg\Orm\Driver\Mysql\Query;

use Greg\Orm\Query\QuerySupport;
use Greg\Support\Arr;

class MysqlQuerySupport extends QuerySupport
{
    public static function concat(array $values, $delimiter = '')
    {
        return count($values) > 1 ? 'concat_ws("' . $delimiter . '", ' . implode(', ', $values) . ')' : Arr::first($values);
    }
}
