<?php

namespace Greg\Orm\Query;

use Greg\Orm\Clause\FromClauseTraitStrategy;
use Greg\Orm\Clause\LimitClauseTraitStrategy;
use Greg\Orm\Clause\OrderByClauseTraitStrategy;
use Greg\Orm\Clause\WhereClauseTraitStrategy;

interface DeleteQueryStrategy extends
    QueryStrategy,
    FromClauseTraitStrategy,
    WhereClauseTraitStrategy,
    OrderByClauseTraitStrategy,
    LimitClauseTraitStrategy
{
    /**
     * @param string    $table
     * @param \string[] ...$tables
     *
     * @return $this
     */
    public function rowsFrom(string $table, string ...$tables);

    /**
     * @return bool
     */
    public function hasRowsFrom(): bool;

    /**
     * @return array
     */
    public function getRowsFrom(): array;

    /**
     * @return $this
     */
    public function clearRowsFrom();
}
