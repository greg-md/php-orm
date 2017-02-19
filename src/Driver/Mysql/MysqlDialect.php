<?php

namespace Greg\Orm\Driver\Mysql;

use Greg\Orm\DialectAbstract;

class MysqlDialect extends DialectAbstract
{
    /**
     * @param string $sql
     *
     * @return string
     */
    public static function lockForUpdateSql(string $sql): string
    {
        return $sql . ' FOR UPDATE';
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public static function lockInShareMode(string $sql): string
    {
        return $sql . ' LOCK IN SHARE MODE';
    }

    public static function concat(array $values, string $delimiter = ''): string
    {
        return count($values) > 1 ? 'concat_ws("' . $delimiter . '", ' . implode(', ', $values) . ')' : array_shift($values);
    }
}
