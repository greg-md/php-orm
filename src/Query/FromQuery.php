<?php

namespace Greg\Orm\Query;

class FromQuery implements FromQueryInterface
{
    use QueryTrait, FromQueryTrait;

    public function toSql($useClause = true)
    {
        return $this->fromToSql($useClause);
    }

    public function toString($useClause = true)
    {
        return $this->fromToString($useClause);
    }
}