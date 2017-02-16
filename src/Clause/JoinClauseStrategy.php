<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;

interface JoinClauseStrategy
{
    /**
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function left($table, string $on = null, string ...$params);

    /**
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function leftOn($table, $on);

    /**
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function right($table, string $on = null, string ...$params);

    /**
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function rightOn($table, $on);

    /**
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function inner($table, string $on = null, string ...$params);

    /**
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function innerOn($table, $on);

    /**
     * @param $table
     *
     * @return $this
     */
    public function cross($table);

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function leftTo($source, $table, string $on = null, string ...$params);

    /**
     * @param $source
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function leftToOn($source, $table, $on);

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function rightTo($source, $table, string $on = null, string ...$params);

    /**
     * @param $source
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function rightToOn($source, $table, $on);

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function innerTo($source, $table, string $on = null, string ...$params);

    /**
     * @param $source
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function innerToOn($source, $table, $on);

    /**
     * @param $source
     * @param $table
     *
     * @return $this
     */
    public function crossTo($source, $table);

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
    public function joinLogic(string $tableKey, string $type, ?string $source, $table, ?string $alias, $on = null, array $params = []);

    /**
     * @return string
     */
    public function hasJoins();

    /**
     * @return array
     */
    public function getJoins();

    /**
     * @return $this
     */
    public function clearJoins();
}
