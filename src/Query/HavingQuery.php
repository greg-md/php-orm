<?php

namespace Greg\Orm\Query;

class HavingQuery implements HavingQueryInterface
{
    use QueryTrait, HavingQueryTrait;

    public function toSql($useClause = true)
    {
        return $this->havingToSql($useClause);
    }

    public function toString($useClause = true)
    {
        return $this->havingToString($useClause);
    }
}