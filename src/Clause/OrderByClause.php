<?php

namespace Greg\Orm\Clause;

use Greg\Orm\WhenTrait;

abstract class OrderByClause implements OrderByClauseStrategy
{
    use OrderByClauseTrait, WhenTrait;

    /**
     * @return array
     */
    public function toSql(): array
    {
        return $this->orderByToSql();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->orderByToString();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
