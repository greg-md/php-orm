<?php

namespace Greg\Orm\Driver\Mysql\Query;

use Greg\Orm\Query\QuerySupport;
use Greg\Support\Arr;

class MysqlQuerySupport extends QuerySupport
{
    static public function concat(array $values, $delimiter = '')
    {
        return sizeof($values) > 1 ? 'concat_ws("' . $delimiter . '", ' . implode(', ', $values) . ')' : Arr::first($values);
    }
}