<?php

namespace Greg\Orm\Query;

class WhereClause implements WhereClauseInterface
{
    use ClauseTrait, WhereClauseTrait;

    public function toSql($useClause = true)
    {
        return $this->whereToSql($useClause);
    }

    public function toString($useClause = true)
    {
        return $this->whereToString($useClause);
    }

    public function __toString()
    {
        return (string)$this->toString();
    }
}