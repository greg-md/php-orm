<?php

namespace Greg\Orm\Clause;

use Greg\Orm\WhenTrait;

abstract class WhereClause implements WhereClauseStrategy
{
    use WhereClauseTrait, WhenTrait;

    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function toSql(bool $useClause = true): array
    {
        return $this->whereToSql($useClause);
    }

    /**
     * @param bool $useClause
     *
     * @return string
     */
    public function toString(bool $useClause = true): string
    {
        return $this->whereToString($useClause);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
