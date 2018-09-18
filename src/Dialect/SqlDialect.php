<?php

namespace Greg\Orm\Dialect;

use Greg\Orm\Model;

class SqlDialect
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

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return string
     */
    public function selectCount(string $column = '*', string $alias = null): string
    {
        return 'SELECT ' . $this->count($column, $alias);
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return string
     */
    public function max(string $column, string $alias = null): string
    {
        if ($alias) {
            $alias = $this->quoteName($alias);
        }

        return 'MAX(' . $this->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : '');
    }

    public function selectMax(string $column, string $alias = null): string
    {
        return 'SELECT ' . $this->max($column, $alias);
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return string
     */
    public function min(string $column, string $alias = null): string
    {
        if ($alias) {
            $alias = $this->quoteName($alias);
        }

        return 'MIN(' . $this->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : '');
    }

    public function selectMin(string $column, string $alias = null): string
    {
        return 'SELECT ' . $this->min($column, $alias);
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return string
     */
    public function avg(string $column, string $alias = null): string
    {
        if ($alias) {
            $alias = $this->quoteName($alias);
        }

        return 'AVG(' . $this->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : '');
    }

    public function selectAvg(string $column, string $alias = null): string
    {
        return 'SELECT ' . $this->avg($column, $alias);
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return string
     */
    public function sum(string $column, string $alias = null): string
    {
        if ($alias) {
            $alias = $this->quoteName($alias);
        }

        return 'SUM(' . $this->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : '');
    }

    public function selectSum(string $column, string $alias = null): string
    {
        return 'SELECT ' . $this->sum($column, $alias);
    }

    public function selectAll(string $from): string
    {
        return 'SELECT * FROM `' . $from . '`';
    }
}
