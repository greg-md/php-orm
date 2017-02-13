<?php

namespace Greg\Orm\Clause;

interface LimitClauseStrategy extends ClauseStrategy, LimitClauseTraitStrategy
{
    /**
     * @param string $sql
     * @return string
     */
    public function addLimitToSql(string $sql): string;
}
