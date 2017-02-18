<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlAbstract;

class FromClause extends SqlAbstract implements ClauseStrategy, FromClauseStrategy, JoinClauseStrategy
{
    use FromClauseTrait, JoinClauseTrait;

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
