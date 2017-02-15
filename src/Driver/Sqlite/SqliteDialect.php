<?php

namespace Greg\Orm\Driver\Sqlite;

use Greg\Orm\DialectAbstract;

class SqliteDialect extends DialectAbstract
{
    /**
     * @param string $sql
     * @return string
     */
    public static function lockForUpdateSql(string $sql): string
    {
        return $sql;
    }

    /**
     * @param string $sql
     * @return string
     */
    public static function lockInShareMode(string $sql): string
    {
        return $sql;
    }
}
