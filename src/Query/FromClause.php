<?php

namespace Greg\Orm\Query;

class FromClause implements FromClauseInterface
{
    use ClauseTrait, FromClauseTrait;

    public function toSql($useClause = true)
    {
        return $this->fromToSql($useClause);
    }

    public function toString($useClause = true)
    {
        return $this->fromToString($useClause);
    }

    public function __toString()
    {
        return (string)$this->toString();
    }
}