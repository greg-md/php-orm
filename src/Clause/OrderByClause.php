<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlAbstract;

class OrderByClause extends SqlAbstract implements OrderByClauseStrategy
{
    use OrderByClauseTrait;

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
