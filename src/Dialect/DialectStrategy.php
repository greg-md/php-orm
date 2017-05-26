<?php

namespace Greg\Orm\Dialect;

interface DialectStrategy
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
    public function quoteSql(string $sql): string;

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
    public function addLimitToSql(string $sql, int $limit): string;

    /**
     * @param string $sql
     * @param int    $limit
     *
     * @return string
     */
    public function addOffsetToSql(string $sql, int $limit): string;

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockForUpdateSql(string $sql): string;

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockInShareMode(string $sql): string;

    public function dateString(string $time): string;

    public function timeString(string $time): string;

    public function dateTimeString(string $time): string;

//    public function quoteLike(string $value, string $escape = '\\'): string;
//
//    public function ifNullSql(string $sql, string $else = '""'): string;
}
