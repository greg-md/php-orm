<?php

namespace Greg\Orm\Query;

class JoinsQuery implements JoinsQueryInterface
{
    use QueryTrait, JoinsQueryTrait;

    public function toSql($source = null)
    {
        return $this->joinsToSql($source);
    }

    public function toString($source = null)
    {
        return $this->joinsToString($source);
    }
}