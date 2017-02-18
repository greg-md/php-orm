<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlAbstract;

class OrderByClause extends SqlAbstract implements ClauseStrategy, OrderByClauseStrategy
{
    use OrderByClauseTrait;

    /**
     * @param bool $useClause
     * @return array
     */
    public function toSql(bool $useClause = true): array
    {
        return $this->orderByToSql($useClause);
    }

    /**
     * @param bool $useClause
     * @return string
     */
    public function toString(bool $useClause = true): string
    {
        return $this->orderByToString($useClause);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
