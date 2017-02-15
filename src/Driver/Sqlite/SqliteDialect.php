<?php

namespace Greg\Orm\Driver\Sqlite;

use Greg\Orm\DialectAbstract;

class SqliteDialect extends DialectAbstract
{
    public static function lockForUpdateSql(string $sql): string
    {
        return $sql;
    }

    public static function lockInShareMode(string $sql): string
    {
        return $sql;
    }
}