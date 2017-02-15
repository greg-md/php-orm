<?php

namespace Greg\Orm;

interface DialectStrategy
{
    public static function quoteTable(string $name): string;

    public static function quoteName(string $name): string;

    public static function quoteSql(string $sql): string;

    public static function prepareBindKeys($value, int $rowLength = null): string;

    public static function addLimitToSql(string $sql, int $limit): string;

    public static function addOffsetToSql(string $sql, int $limit): string;

    public static function lockForUpdateSql(string $sql): string;

    public static function lockInShareMode(string $sql): string;

    public static function parseTable($name): array;

//    public static function quoteLike(string $value, string $escape = '\\'): string;
//
//    public static function concat(array $values, string $delimiter = ''): string;
//
//    public static function ifNullSql(string $sql, string $else = '""'): string;
}
