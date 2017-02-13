<?php

namespace Greg\Orm\Driver\Sqlite;

trait SqliteUtilsTrait
{
//    protected function quoteLike(string $value, string $escape = '\\'): string
//    {
//        return SqliteDriverUtils::quoteLike($value, $escape);
//    }
//
//    protected function concat(array $values, string $delimiter = ''): string
//    {
//        return SqliteDriverUtils::concat($values, $delimiter);
//    }

    protected function parseAlias($name): array
    {
        return SqliteDriverUtils::parseAlias($name);
    }

    protected function quoteTableSql(string $sql): string
    {
        return SqliteDriverUtils::quoteTableSql($sql);
    }

    protected function quoteSql(string $sql): string
    {
        return SqliteDriverUtils::quoteSql($sql);
    }

    protected function quoteNameSql(string $name): string
    {
        return SqliteDriverUtils::quoteNameSql($name);
    }

    protected function quoteName(string $name): string
    {
        return SqliteDriverUtils::quoteName($name);
    }

    protected function prepareForBind($value, int $rowLength = null): string
    {
        return SqliteDriverUtils::prepareForBind($value, $rowLength);
    }

//    protected function ifNullSql(string $sql, string $else = '""'): string
//    {
//        return SqliteDriverUtils::ifNullSql($sql, $else);
//    }
}
