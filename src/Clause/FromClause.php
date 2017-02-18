<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlAbstract;

class FromClause extends SqlAbstract implements ClauseStrategy, FromClauseStrategy
{
    use FromClauseTrait;

    /**
     * @param JoinClauseStrategy|null $join
     * @param bool $useClause
     *
     * @return array
     */
    public function toSql(?JoinClauseStrategy $join = null, bool $useClause = true): array
    {
        return $this->fromToSql($join, $useClause);
    }

    /**
     * @param JoinClauseStrategy|null $join
     * @param bool $useClause
     *
     * @return string
     */
    public function toString(?JoinClauseStrategy $join = null, bool $useClause = true): string
    {
        return $this->fromToString($join, $useClause);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
