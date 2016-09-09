<?php

namespace Greg\Orm\Query;

class OrderByClause implements OrderByClauseInterface
{
    use ClauseTrait, OrderByClauseTrait;

    public function toSql()
    {
        return $this->orderByToSql();
    }

    public function toString()
    {
        return $this->orderByToString();
    }

    public function __toString()
    {
        return (string)$this->toString();
    }
}