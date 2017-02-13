<?php

namespace Greg\Orm\Driver\Mysql;

use Greg\Orm\Driver\DriverUtils;
use Greg\Support\Arr;

class MysqlDriverUtils extends DriverUtils
{
    public static function concat(array $values, string $delimiter = ''): string
    {
        return count($values) > 1 ? 'concat_ws("' . $delimiter . '", ' . implode(', ', $values) . ')' : (string) Arr::first($values);
    }
}
