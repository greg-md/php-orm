<?php

namespace Greg\Orm\Driver\Mysql;

use Greg\Orm\DialectAbstract;

class MysqlDialect extends DialectAbstract
{
    public static function lockForUpdateSql(string $sql): string
    {
        return $sql . ' FOR UPDATE';
    }

    public static function lockInShareMode(string $sql): string
    {
        return $sql . ' LOCK IN SHARE MODE';
    }

//    public static function concat(array $values, string $delimiter = ''): string
//    {
//        return count($values) > 1 ? 'concat_ws("' . $delimiter . '", ' . implode(', ', $values) . ')' : (string) Arr::first($values);
//    }
}