<?php

namespace Greg\Orm;

abstract class SqlAbstract implements SqlStrategy
{
    /**
     * @var DialectStrategy
     */
    private $dialect;

    /**
     * SqlAbstract constructor.
     * @param DialectStrategy $dialect
     */
    public function __construct(DialectStrategy $dialect)
    {
        $this->dialect = $dialect;
    }

    /**
     * @return DialectStrategy
     */
    public function dialect(): DialectStrategy
    {
        return $this->dialect;
    }

    /**
     * @param bool $condition
     * @param callable $callable
     * @return $this
     */
    public function when(bool $condition, callable $callable)
    {
        if ($condition) {
            call_user_func_array($callable, [$this]);
        }

        return $this;
    }
}
