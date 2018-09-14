<?php

namespace Greg\Orm\Dialect;

use Greg\Orm\Model;

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
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    /**
     * @var string
     */
    protected $timeFormat = 'H:i:s';

    /**
     * @var string
     */
    protected $dateTimeFormat = 'Y-m-d H:i:s';

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

        return static::quote($name);
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
            return $part !== '*' ? $this->quoteNameWith . $part . $this->quoteNameWith : $part;
        }, $sql);

        $sql = implode('.', $sql);

        return $sql;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function quote(string $sql): string
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

    /**
     * @param string $name
     *
     * @return array
     */
    public function parseName(string $name): array
    {
        preg_match('#^(.*?)(?:\s+(?:as\s+)?([a-z0-9_]+))?$#i', $name, $matches);

        return [$matches[2] ?? null, $matches[1]];
    }

    /**
     * @param array  $values
     * @param string $delimiter
     *
     * @return string
     */
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
    public function limit(string $sql, int $limit): string
    {
        return ($sql ? $sql . ' ' : '') . 'LIMIT ' . $limit;
    }

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public function offset(string $sql, int $limit): string
    {
        return ($sql ? $sql . ' ' : '') . 'OFFSET ' . $limit;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockForUpdate(string $sql): string
    {
        return $sql;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockForShare(string $sql): string
    {
        return $sql;
    }

    /**
     * @param string $time
     *
     * @return string
     */
    public function dateString(string $time): string
    {
        return date($this->dateFormat, ctype_digit($time) ? $time : strtotime($time));
    }

    /**
     * @param string $time
     *
     * @return string
     */
    public function timeString(string $time): string
    {
        return date($this->timeFormat, ctype_digit($time) ? $time : strtotime($time));
    }

    /**
     * @param string $time
     *
     * @return string
     */
    public function dateTimeString(string $time): string
    {
        return date($this->dateTimeFormat, ctype_digit($time) ? $time : strtotime($time));
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return string
     */
    public function count(string $column = '*', string $alias = null): string
    {
        if ($alias) {
            $alias = $this->quoteName($alias);
        }

        return 'COUNT(' . $this->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : '');
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
