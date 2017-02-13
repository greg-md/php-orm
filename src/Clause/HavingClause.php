<?php

namespace Greg\Orm\Clause;

use Greg\Orm\WhenTrait;

abstract class HavingClause implements HavingClauseStrategy
{
    use HavingClauseTrait, WhenTrait;

    /**
     * @param bool $useClause
     * @return array
     */
    public function toSql(bool $useClause = true): array
    {
        return $this->havingToSql($useClause);
    }

    /**
     * @param bool $useClause
     * @return string
     */
    public function toString(bool $useClause = true): string
    {
        return $this->havingToString($useClause);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
