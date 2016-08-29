<?php

namespace Greg\Orm\Query;

class HavingQuery implements HavingQueryInterface
{
    use QueryTrait, HavingQueryTrait;

    public function toSql()
    {
        return $this->havingToSql();
    }

    public function toString()
    {
        return $this->havingToString();
    }
}