<?php

namespace Greg\Orm\Query;

class OnQuery implements OnQueryInterface
{
    use QueryTrait, OnQueryTrait;

    public function toSql($useClause = true)
    {
        return $this->onToSql($useClause);
    }

    public function toString($useClause = true)
    {
        return $this->onToString($useClause);
    }
}