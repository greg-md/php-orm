<?php

namespace Greg\Orm\Dialect;

interface SqlDialectStrategy
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function quoteTable(string $name): string;

    /**
     * @param string $name
     *
     * @return string
     */
    public function quoteName(string $name): string;

    /**
     * @param string $sql
     *
     * @return string
     */
    public function quote(string $sql): string;

    /**
     * @param $name
     *
     * @return array
     */
    public function parseTable($name): array;

    /**
     * @param string $name
     *
     * @return array
     */
    public function parseName(string $name): array;

    /**
     * @param array  $values
     * @param string $delimiter
     *
     * @return string
     */
    public function concat(array $values, string $delimiter = ''): string;

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public function limit(string $sql, int $limit): string;

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public function offset(string $sql, int $limit): string;

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockForUpdate(string $sql): string;

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockForShare(string $sql): string;

    public function dateString(string $time): string;

    public function timeString(string $time): string;

    public function dateTimeString(string $time): string;

    public function count(string $column = '*', string $alias = null): string;

    public function selectCount(string $column = '*', string $alias = null): string;

    public function max(string $column, string $alias = null): string;

    public function selectMax(string $column, string $alias = null): string;

    public function min(string $column, string $alias = null): string;

    public function selectMin(string $column, string $alias = null): string;

    public function avg(string $column, string $alias = null): string;

    public function selectAvg(string $column, string $alias = null): string;

    public function sum(string $column, string $alias = null): string;

    public function selectSum(string $column, string $alias = null): string;

    public function selectAll(string $from): string;
}
