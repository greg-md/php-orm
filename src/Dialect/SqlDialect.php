<?php

namespace Greg\Orm\Dialect;

use Greg\Orm\Model;
use Greg\Support\Str;

class SqlDialect implements DialectStrategy
{
    /**
     * @var string
     */
    protected $quoteNameWith = '`';

    /**
     * @var string
     */
    protected $nameRegex = '[a-z0-9_\.\*]+';

    /**
     * @param string $name
     *
     * @return string
     */
    public function quoteTable(string $name): string
    {
        if (preg_match('#^(' . $this->nameRegex . ')$#i', $name)) {
            return static::quoteName($name);
        }

        return static::quoteSql($name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function quoteName(string $name): string
    {
        $sql = explode('.', $name);

        $sql = array_map(function ($part) {
            return $part !== '*' ? Str::quote($part, $this->quoteNameWith) : $part;
        }, $sql);

        $sql = implode('.', $sql);

        return $sql;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function quoteSql(string $sql): string
    {
        return preg_replace_callback('#".*\!' . $this->nameRegex . '.*"|\!(' . $this->nameRegex . ')#i', function ($matches) {
            return isset($matches[1]) ? static::quoteName($matches[1]) : $matches[0];
        }, $sql);
    }

    /**
     * @param $name
     *
     * @return array
     */
    public function parseTable($name): array
    {
        if ($name instanceof Model) {
            return [$name->alias(), $name->fullName()];
        }

        if (is_array($name)) {
            return [key($name), current($name)];
        }

        if (is_scalar($name)) {
            return static::parseName($name);
        }

        return [null, $name];
    }

    public function parseName(string $name): array
    {
        preg_match('#^(.*?)(?:\s+(?:as\s+)?([a-z0-9_]+))?$#i', $name, $matches);

        return [$matches[2] ?? null, $matches[1]];
    }

    public function concat(array $values, string $delimiter = ''): string
    {
        if ($delimiter) {
            return implode(' + ' . $delimiter . ' + ', $values);
        }

        return implode(' + ', $values);
    }

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public function addLimitToSql(string $sql, int $limit): string
    {
        return ($sql ? $sql . ' ' : '') . 'LIMIT ' . $limit;
    }

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public function addOffsetToSql(string $sql, int $limit): string
    {
        return ($sql ? $sql . ' ' : '') . 'OFFSET ' . $limit;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockForUpdateSql(string $sql): string
    {
        return $sql;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockInShareMode(string $sql): string
    {
        return $sql;
    }

//    public function quoteLike(string $value, string $escape = '\\'): string
//    {
//        return strtr($value, [
//            '_' => $escape . '_',
//            '%' => $escape . '%',
//        ]);
//    }
//
//    public function ifNullSql(string $sql, string $else = '""'): string
//    {
//        return 'IFNULL(' . $sql . ', ' . $else . ')';
//    }
}
