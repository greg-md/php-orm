<?php

namespace Greg\Orm\Query;

class GroupByClause implements GroupByClauseInterface
{
    use ClauseTrait, GroupByClauseTrait;

    public function toSql()
    {
        return $this->groupByToSql();
    }

    public function toString()
    {
        return $this->groupByToString();
    }

    public function __toString()
    {
        return (string)$this->toString();
    }
}