<?php

namespace Greg\Orm\Query;

class OnQuery implements OnQueryInterface
{
    use QueryTrait, OnQueryTrait;

    public function toSql()
    {
        return $this->onToSql();
    }

    public function toString()
    {
        return $this->onToString();
    }
}