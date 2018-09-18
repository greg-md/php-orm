<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;

interface JoinClauseStrategy
{
    /**
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function leftJoin($table, string $on = null, string ...$params);

    /**
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function leftJoinOn($table, $on);

    /**
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function rightJoin($table, string $on = null, string ...$params);

    /**
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function rightJoinOn($table, $on);

    /**
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function innerJoin($table, string $on = null, string ...$params);

    /**
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function innerJoinOn($table, $on);

    /**
     * @param $table
     *
     * @return $this
     */
    public function crossJoin($table);

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function leftJoinTo($source, $table, string $on = null, string ...$params);

    /**
     * @param $source
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function leftJoinOnTo($source, $table, $on);

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function rightJoinTo($source, $table, string $on = null, string ...$params);

    /**
     * @param $source
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function rightJoinOnTo($source, $table, $on);

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function innerJoinTo($source, $table, string $on = null, string ...$params);

    /**
     * @param $source
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function innerJoinOnTo($source, $table, $on);

    /**
     * @param $source
     * @param $table
     *
     * @return $this
     */
    public function crossJoinTo($source, $table);

    /**
     * @param string      $tableKey
     * @param string      $type
     * @param null|string $source
     * @param $table
     * @param null|string $alias
     * @param $on
     * @param array $params
     *
     * @return $this
     */
    public function addJoin(string $tableKey, string $type, ?string $source, $table, ?string $alias, $on = null, array $params = []);

    /**
     * @return string
     */
    public function hasJoin();

    /**
     * @return array
     */
    public function getJoin();

    /**
     * @return $this
     */
    public function clearJoin();

    public function joinToSql(string $source = null): array;

    public function joinToString(string $source = null): string;
}
