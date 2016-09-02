<?php

namespace Greg\Orm\Query;

class WhereQuery implements WhereQueryInterface
{
    use QueryTrait, WhereQueryTrait;

    public function toSql($useClause = true)
    {
        return $this->whereToSql($useClause);
    }

    public function toString($useClause = true)
    {
        return $this->whereToString($useClause);
    }
}