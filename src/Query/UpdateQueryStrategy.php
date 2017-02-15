<?php

namespace Greg\Orm\Query;

use Greg\Orm\Clause\JoinClauseTraitStrategy;
use Greg\Orm\Clause\LimitClauseTraitStrategy;
use Greg\Orm\Clause\OrderByClauseTraitStrategy;
use Greg\Orm\Clause\WhereClauseTraitStrategy;

interface UpdateQueryStrategy extends
    QueryStrategy,
    JoinClauseTraitStrategy,
    WhereClauseTraitStrategy,
    OrderByClauseTraitStrategy,
    LimitClauseTraitStrategy
{
    /**
     * @param $table
     * @param array ...$tables
     *
     * @return $this
     */
    public function table($table, ...$tables);

    /**
     * @return bool
     */
    public function hasTables(): bool;

    /**
     * @return array
     */
    public function getTables(): array;

    /**
     * @return $this
     */
    public function clearTables();

    /**
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function set(string $column, string $value);

    /**
     * @param array $columns
     * @return $this
     */
    public function setMultiple(array $columns);

    /**
     * @param string    $raw
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function setRaw(string $raw, string ...$params);

    /**
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function increment(string $column, int $value = 1);

    /**
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function decrement(string $column, int $value = 1);

    /**
     * @return bool
     */
    public function hasSet(): bool;

    /**
     * @return array
     */
    public function getSet(): array;

    /**
     * @return $this
     */
    public function clearSet();
}
