<?php

namespace Greg\Orm\Query;

use Greg\Support\Debug;

class JoinsQuery implements JoinsQueryInterface
{
    use QueryTrait, JoinsQueryTrait;

    public function toString($source)
    {
        return $this->joinsToString($source);
    }

    public function __toString()
    {
        return $this->toString(null);
    }

    public function __debugInfo()
    {
        return Debug::fixInfo($this, get_object_vars($this), false);
    }
}