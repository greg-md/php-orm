<?php

namespace Greg\Orm\Clause;

interface OffsetClauseStrategy extends ClauseStrategy, OffsetClauseTraitStrategy
{
    /**
     * @param string $sql
     *
     * @return string
     */
    public function addOffsetToSql(string $sql): string;
}
