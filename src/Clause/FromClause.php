<?php

namespace Greg\Orm\Clause;

use Greg\Orm\WhenTrait;

abstract class FromClause implements FromClauseStrategy
{
    use FromClauseTrait, WhenTrait;

    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function toSql(bool $useClause = true): array
    {
        return $this->fromToSql($useClause);
    }

    /**
     * @param bool $useClause
     *
     * @return string
     */
    public function toString(bool $useClause = true): string
    {
        return $this->fromToString($useClause);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
