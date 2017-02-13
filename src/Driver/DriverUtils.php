<?php

namespace Greg\Orm\Driver;

use Greg\Orm\TableTraitInterface;
use Greg\Support\Str;

class DriverUtils
{
    protected static $quoteNameWith = '`';

    protected static $nameRegex = '[a-z0-9_\.\*]+';

    public static function quoteLike(string $value, string $escape = '\\'): string
    {
        return strtr($value, [
            '_' => $escape . '_',
            '%' => $escape . '%',
        ]);
    }

    public static function concat(array $values, string $delimiter = ''): string
    {
        return implode(' + ' . $delimiter . ' + ', $values);
    }

    public static function parseAlias($name): array
    {
        if ($name instanceof TableTraitInterface) {
            return [$name->getAlias(), $name->fullName()];
        }

        if (is_array($name)) {
            return [key($name), current($name)];
        }

        if (is_scalar($name) and preg_match('#^(.+?)(?:\s+(?:as\s+)?([a-z0-9_]+))?$#i', $name, $matches)) {
            return [isset($matches[2]) ? $matches[2] : null, $matches[1]];
        }

        return [null, $name];
    }

    public static function quoteTableSql(string $sql): string
    {
        if (preg_match('#^(' . static::$nameRegex . ')$#i', $sql)) {
            return static::quoteNameSql($sql);
        }

        return static::quoteSql($sql);
    }

    public static function quoteSql(string $sql): string
    {
        $sql = preg_replace_callback('#".*\!' . static::$nameRegex . '.*"|\!(' . static::$nameRegex . ')#i', function ($matches) {
            return isset($matches[1]) ? static::quoteNameSql($matches[1]) : $matches[0];
        }, $sql);

        return $sql;
    }

    public static function quoteNameSql(string $name): string
    {
        $sql = explode('.', $name);

        $sql = array_map(function ($part) {
            return $part !== '*' ? static::quoteName($part) : $part;
        }, $sql);

        $sql = implode('.', $sql);

        return $sql;
    }

    public static function quoteName(string $name): string
    {
        return Str::quote($name, static::$quoteNameWith);
    }

    public static function prepareForBind($value, int $rowLength = null): string
    {
        if (is_array($value)) {
            $result = '(' . implode(', ', array_fill(0, count($value), '?')) . ')';

            if ($rowLength !== null) {
                $result = '(' . implode(', ', array_fill(0, $rowLength, $result)) . ')';
            }

            return $result;
        }

        return '?';
    }

    public static function ifNullSql(string $sql, string $else = '""'): string
    {
        return 'IFNULL(' . $sql . ', ' . $else . ')';
    }
}
