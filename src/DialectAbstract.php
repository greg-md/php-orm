<?php

namespace Greg\Orm;

use Greg\Support\Str;

abstract class DialectAbstract implements DialectStrategy
{
    /**
     * @var string
     */
    protected static $quoteNameWith = '`';

    /**
     * @var string
     */
    protected static $nameRegex = '[a-z0-9_\.\*]+';

    /**
     * @param string $name
     *
     * @return string
     */
    public static function quoteTable(string $name): string
    {
        if (preg_match('#^(' . static::$nameRegex . ')$#i', $name)) {
            return static::quoteName($name);
        }

        return static::quoteSql($name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function quoteName(string $name): string
    {
        $sql = explode('.', $name);

        $sql = array_map(function ($part) {
            return $part !== '*' ? Str::quote($part, static::$quoteNameWith) : $part;
        }, $sql);

        $sql = implode('.', $sql);

        return $sql;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public static function quoteSql(string $sql): string
    {
        $sql = preg_replace_callback('#".*\!' . static::$nameRegex . '.*"|\!(' . static::$nameRegex . ')#i', function ($matches) {
            return isset($matches[1]) ? static::quoteName($matches[1]) : $matches[0];
        }, $sql);

        return $sql;
    }

    /**
     * @param $value
     * @param int|null $rowLength
     *
     * @return string
     */
    public static function prepareBindKeys($value, int $rowLength = null): string
    {
        if (is_array($value)) {
            $result = '(' . implode(', ', array_fill(0, count($value), '?')) . ')';

            if ($rowLength) {
                $result = '(' . implode(', ', array_fill(0, $rowLength, $result)) . ')';
            }

            return $result;
        }

        return '?';
    }

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public static function addLimitToSql(string $sql, int $limit): string
    {
        return $sql . ' LIMIT ' . $limit;
    }

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public static function addOffsetToSql(string $sql, int $limit): string
    {
        return $sql . ' OFFSET ' . $limit;
    }

    /**
     * @param $table
     *
     * @return array
     */
    public static function parseTable($table): array
    {
        if ($table instanceof Model) {
            return [$table->alias(), $table->fullName()];
        }

        if (is_array($table)) {
            return [key($table), current($table)];
        }

        if (is_scalar($table) and preg_match('#^(.+?)(?:\s+(?:as\s+)?([a-z0-9_]+))?$#i', $table, $matches)) {
            return [isset($matches[2]) ? $matches[2] : null, $matches[1]];
        }

        return [null, $table];
    }

//    public static function quoteLike(string $value, string $escape = '\\'): string
//    {
//        return strtr($value, [
//            '_' => $escape . '_',
//            '%' => $escape . '%',
//        ]);
//    }
//
//    public static function concat(array $values, string $delimiter = ''): string
//    {
//        return implode(' + ' . $delimiter . ' + ', $values);
//    }
//
//    public static function ifNullSql(string $sql, string $else = '""'): string
//    {
//        return 'IFNULL(' . $sql . ', ' . $else . ')';
//    }
}
