<?php

namespace Greg\Orm\Clause;

interface HavingClauseStrategy extends HavingClauseTraitStrategy
{
    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function toSql(bool $useClause = true): array;

    /**
     * @param bool $useClause
     *
     * @return string
     */
    public function toString(bool $useClause = true): string;

    /**
     * @return string
     */
    public function __toString(): string;
}
