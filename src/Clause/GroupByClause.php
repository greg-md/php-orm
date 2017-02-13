<?php

namespace Greg\Orm\Clause;

use Greg\Orm\WhenTrait;

abstract class GroupByClause implements GroupByClauseStrategy
{
    use GroupByClauseTrait, WhenTrait;

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
