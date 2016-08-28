<?php

namespace Greg\Orm\Query;

class FromQuery implements FromQueryInterface
{
    use QueryTrait, FromQueryTrait;

    public function toSql()
    {
        return $this->fromToSql();
    }

    public function toString()
    {
        return $this->fromToString();
    }
}