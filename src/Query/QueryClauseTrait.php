<?php

namespace Greg\Orm\Query;

use Greg\Orm\Query;
use Greg\Support\Debug;

trait QueryClauseTrait
{
    use QueryClauseSupportTrait;

    public function when($condition, callable $callable)
    {
        if ($condition) {
            call_user_func_array($callable, [$this]);
        }

        return $this;
    }

    public function __debugInfo()
    {
        return Debug::fixInfo($this, get_object_vars($this), false);
    }
}