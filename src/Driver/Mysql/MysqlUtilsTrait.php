<?php

namespace Greg\Orm\Driver\Mysql;

trait MysqlUtilsTrait
{
    //    protected function quoteLike(string $value, string $escape = '\\'): string
//    {
//        return MysqlDriverUtils::quoteLike($value, $escape);
//    }
//
//    protected function concat(array $values, string $delimiter = ''): string
//    {
//        return MysqlDriverUtils::concat($values, $delimiter);
//    }

    protected function parseAlias($name): array
    {
        return MysqlDriverUtils::parseAlias($name);
    }

    protected function quoteTableSql(string $sql): string
    {
        return MysqlDriverUtils::quoteTableSql($sql);
    }

    protected function quoteSql(string $sql): string
    {
        return MysqlDriverUtils::quoteSql($sql);
    }

    protected function quoteNameSql(string $name): string
    {
        return MysqlDriverUtils::quoteNameSql($name);
    }

    protected function quoteName(string $name): string
    {
        return MysqlDriverUtils::quoteName($name);
    }

    protected function prepareForBind($value, int $rowLength = null): string
    {
        return MysqlDriverUtils::prepareForBind($value, $rowLength);
    }

//    protected function ifNullSql(string $sql, string $else = '""'): string
//    {
//        return MysqlDriverUtils::ifNullSql($sql, $else);
//    }
}
