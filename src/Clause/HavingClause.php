<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlAbstract;

class HavingClause extends SqlAbstract implements ClauseStrategy, HavingClauseStrategy
{
    use HavingClauseTrait;

    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function toSql(bool $useClause = true): array
    {
        return $this->havingToSql($useClause);
    }

    /**
     * @param bool $useClause
     *
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

    public function __clone()
    {
        $this->havingClone();
    }
}
