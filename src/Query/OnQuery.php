<?php

namespace Greg\Orm\Query;

use Greg\Support\Debug;

class OnQuery implements OnQueryInterface
{
    use QueryTrait, OnQueryTrait;

    public function toString()
    {
        return $this->onToString(false);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function __debugInfo()
    {
        return Debug::fixInfo($this, get_object_vars($this), false);
    }
}