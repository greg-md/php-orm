<?php

namespace Greg\Orm;

interface DialectStrategy
{
    /**
     * @param string $name
     *
     * @return string
     */
    public static function quoteTable(string $name): string;

    /**
     * @param string $name
     *
     * @return string
     */
    public static function quoteName(string $name): string;

    /**
     * @param string $sql
     *
     * @return string
     */
    public static function quoteSql(string $sql): string;

    /**
     * @param $value
     * @param int|null $rowLength
     *
     * @return string
     */
    public static function prepareBindKeys($value, int $rowLength = null): string;

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public static function addLimitToSql(string $sql, int $limit): string;

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public static function addOffsetToSql(string $sql, int $limit): string;

    /**
     * @param string $sql
     *
     * @return string
     */
    public static function lockForUpdateSql(string $sql): string;

    /**
     * @param string $sql
     *
     * @return string
     */
    public static function lockInShareMode(string $sql): string;

    /**
     * @param $name
     *
     * @return array
     */
    public static function parseTable($name): array;

    public static function parseName(string $name): array;

    public static function concat(array $values, string $delimiter = ''): string;

//    public static function quoteLike(string $value, string $escape = '\\'): string;
//
//    public static function ifNullSql(string $sql, string $else = '""'): string;
}
