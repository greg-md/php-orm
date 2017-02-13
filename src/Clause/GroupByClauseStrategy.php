<?php

namespace Greg\Orm\Clause;

interface GroupByClauseStrategy extends ClauseStrategy, GroupByClauseTraitStrategy
{
    /**
     * @return array
     */
    public function toSql(): array;

    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @return string
     */
    public function __toString(): string;
}
