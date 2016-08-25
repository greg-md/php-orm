<?php

namespace Greg\Orm\Query;

use Greg\Support\Debug;

class WhereQuery
{
    use QueryTrait, WhereQueryTrait;

    public function toString()
    {
        return $this->whereToString(false);
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