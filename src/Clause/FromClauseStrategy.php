<?php

namespace Greg\Orm\Clause;

interface FromClauseStrategy extends JoinClauseStrategy
{
    /**
     * @param $table
     * @param array ...$tables
     *
     * @return $this
     */
    public function from($table, ...$tables);

    /**
     * @param null|string $alias
     * @param string      $sql
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function fromRaw(?string $alias, string $sql, string ...$params);

    public function fromLogic(?string $tableKey, $table, ?string $alias, array $params = []);

    /**
     * @return bool
     */
    public function hasFrom(): bool;

    /**
     * @return array
     */
    public function getFrom(): array;

    /**
     * @return $this
     */
    public function clearFrom();
}
