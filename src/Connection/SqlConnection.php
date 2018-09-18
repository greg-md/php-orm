<?php

namespace Greg\Orm\Connection;

interface SqlConnection
{
    /**
     * @param string $sql
     * @param array  $params
     *
     * @return int
     */
    public function sqlExecute(string $sql, array $params = []): int;

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[]
     */
    public function sqlFetch(string $sql, array $params = []): ?array;

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]
     */
    public function sqlFetchAll(string $sql, array $params = []): array;

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]|\Generator
     */
    public function sqlGenerate(string $sql, array $params = []): \Generator;

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return string
     */
    public function sqlFetchColumn(string $sql, array $params = [], string $column = '0');

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return string[]
     */
    public function sqlFetchAllColumn(string $sql, array $params = [], string $column = '0'): array;

    /**
     * @param string $sql
     * @param array  $params
     * @param string $key
     * @param string $value
     *
     * @return string[]
     */
    public function sqlFetchPairs(string $sql, array $params = [], string $key = '0', string $value = '1'): array;
}
