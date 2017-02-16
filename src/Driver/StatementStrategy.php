<?php

namespace Greg\Orm\Driver;

interface StatementStrategy
{
    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function bindParam(string $key, string $value);

    /**
     * @param array $params
     *
     * @return $this
     */
    public function bindParams(array $params);

    /**
     * @param array $params
     *
     * @return bool
     */
    public function execute(array $params = []): bool;

    /**
     * @return string[]
     */
    public function fetch();

    /**
     * @return string[][]
     */
    public function fetchAll();

    /**
     * @return \Generator
     */
    public function fetchYield();

    /**
     * @return string[]
     */
    public function fetchAssoc();

    /**
     * @return string[][]
     */
    public function fetchAssocAll();

    /**
     * @return \Generator
     */
    public function fetchAssocYield();

    /**
     * @param string $column
     *
     * @return string
     */
    public function fetchColumn(string $column = '0');

    /**
     * @param string $column
     *
     * @return string[]
     */
    public function fetchAllColumn(string $column = '0');

    /**
     * @param string $key
     * @param string $value
     *
     * @return string[]
     */
    public function fetchPairs(string $key = '0', string $value = '1');

    public function rowCount(): int;
}
