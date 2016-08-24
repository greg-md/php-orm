<?php

namespace Greg\Orm\Query;

use Greg\Support\Debug;

class ConditionsQuery extends QueryAbstract
{
    use ConditionsQueryTrait;

    public function toString()
    {
        return $this->conditionsToString();
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