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
    public function bind(string $key, string $value);

    /**
     * @param array $params
     *
     * @return $this
     */
    public function bindMultiple(array $params);

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
     * @param string $column
     *
     * @return string
     */
    public function column(string $column = '0');

    /**
     * @param string $column
     *
     * @return string[]
     */
    public function columnAll(string $column = '0');

    public function columnYield(string $column = '0');

    /**
     * @param string $key
     * @param string $value
     *
     * @return string[]
     */
    public function pairs(string $key = '0', string $value = '1');

    public function pairsYield(string $key = '0', string $value = '1');

    public function affectedRows(): int;
}
