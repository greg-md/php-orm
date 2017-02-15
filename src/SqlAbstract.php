<?php

namespace Greg\Orm;

abstract class SqlAbstract implements SqlStrategy
{
    private $dialect;

    public function __construct(DialectStrategy $dialect)
    {
        $this->dialect = $dialect;
    }

    public function dialect(): DialectStrategy
    {
        return $this->dialect;
    }

    public function when(bool $condition, callable $callable)
    {
        if ($condition) {
            call_user_func_array($callable, [$this]);
        }

        return $this;
    }
}
