<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlAbstract;

class GroupByClause extends SqlAbstract implements GroupByClauseStrategy
{
    use GroupByClauseTrait;

    /**
     * @return array
     */
    public function toSql(): array
    {
        return $this->groupByToSql();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->groupByToString();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
