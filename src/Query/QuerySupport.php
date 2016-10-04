<?php

namespace Greg\Orm\Query;

use Greg\Orm\TableTraitInterface;
use Greg\Support\Str;

class QuerySupport
{
    const QUOTE_NAME_WITH = '`';

    const NAME_REGEX = '[a-z0-9_\.\*]+';

    public static function quoteLike($value, $escape = '\\')
    {
        return strtr($value, [
            '_' => $escape . '_',
            '%' => $escape . '%',
        ]);
    }

    public static function concat(array $values, $delimiter = '')
    {
        return implode(' + ' . $delimiter . ' + ', $values);
    }

    public static function parseAlias($name)
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

    public static function quoteTableExpr($expr)
    {
        if (preg_match('#^(' . static::NAME_REGEX . ')$#i', $expr)) {
            return static::quoteNameExpr($expr);
        }

        return static::quoteExpr($expr);
    }

    public static function quoteExpr($expr)
    {
        $regex = static::NAME_REGEX;

        $expr = preg_replace_callback('#".*\!' . $regex . '.*"|\!(' . $regex . ')#i', function ($matches) {
            return isset($matches[1]) ? static::quoteNameExpr($matches[1]) : $matches[0];
        }, $expr);

        return $expr;
    }

    public static function quoteNameExpr($name)
    {
        $expr = explode('.', $name);

        $expr = array_map(function ($part) {
            return $part !== '*' ? static::quoteName($part) : $part;
        }, $expr);

        $expr = implode('.', $expr);

        return $expr;
    }

    public static function quoteName($name)
    {
        return Str::quote($name, static::QUOTE_NAME_WITH);
    }

    public static function prepareForBind($value)
    {
        return is_array($value) ? static::prepareInForBind(count($value)) : '?';
    }

    public static function prepareInForBind($length, $rowLength = null)
    {
        $result = '(' . implode(', ', array_fill(0, $length, '?')) . ')';

        if ($rowLength !== null) {
            $result = '(' . implode(', ', array_fill(0, $rowLength, $result)) . ')';
        }

        return $result;
    }
}
