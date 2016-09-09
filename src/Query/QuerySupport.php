<?php

namespace Greg\Orm\Query;

use Greg\Orm\TableTraitInterface;
use Greg\Support\Str;

class QuerySupport
{
    const QUOTE_NAME_WITH = '`';

    const NAME_REGEX = '[a-z0-9_\.\*]+';

    static public function quoteLike($value, $escape = '\\')
    {
        return strtr($value, [
            '_' => $escape . '_',
            '%' => $escape . '%',
        ]);
    }

    static public function concat(array $values, $delimiter = '')
    {
        return implode(' + ' . $delimiter . ' + ', $values);
    }

    static public function parseAlias($name)
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

    static public function quoteTableExpr($expr)
    {
        if (preg_match('#^(' . static::NAME_REGEX . ')$#i', $expr)) {
            return static::quoteNameExpr($expr);
        }

        return static::quoteExpr($expr);
    }

    static public function quoteExpr($expr)
    {
        $regex = static::NAME_REGEX;

        $expr = preg_replace_callback('#".*\!' . $regex .'.*"|\!(' . $regex .')#i', function($matches) {
            return isset($matches[1]) ? static::quoteNameExpr($matches[1]) : $matches[0];
        }, $expr);

        return $expr;
    }

    static public function quoteNameExpr($name)
    {
        $expr = explode('.', $name);

        $expr = array_map(function($part) {
            return $part !== '*' ? static::quoteName($part) : $part;
        }, $expr);

        $expr = implode('.', $expr);

        return $expr;
    }

    static public function quoteName($name)
    {
        return Str::quote($name, static::QUOTE_NAME_WITH);
    }

    static public function prepareForBind($value)
    {
        return is_array($value) ? static::prepareInForBind(sizeof($value)) : '?';
    }

    static public function prepareInForBind($length, $rowLength = null)
    {
        $result = '(' . implode(', ', array_fill(0, $length, '?')) . ')';

        if ($rowLength !== null) {
            $result = '(' . implode(', ', array_fill(0, $rowLength, $result)) . ')';
        }

        return $result;
    }
}