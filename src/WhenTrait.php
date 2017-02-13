<?php

namespace Greg\Orm;

trait WhenTrait
{
    public function when(bool $condition, callable $callable)
    {
        if ($condition) {
            call_user_func_array($callable, [$this]);
        }

        return $this;
    }
}
