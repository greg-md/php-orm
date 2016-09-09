<?php

namespace Greg\Orm\Query;

class HavingClause implements HavingClauseInterface
{
    use ClauseTrait, HavingClauseTrait;

    public function toSql($useClause = true)
    {
        return $this->havingToSql($useClause);
    }

    public function toString($useClause = true)
    {
        return $this->havingToString($useClause);
    }

    public function __toString()
    {
        return (string)$this->toString();
    }
}