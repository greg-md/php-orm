<?php

namespace Greg\Orm\Query;

class WhereQuery implements WhereQueryInterface
{
    use QueryTrait, WhereQueryTrait;

    public function toSql()
    {
        return $this->whereToSql();
    }

    public function toString()
    {
        return $this->whereToString();
    }
}