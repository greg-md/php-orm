<?php

namespace Greg\Orm;

use Greg\Orm\Dialect\DialectStrategy;
use Greg\Orm\Dialect\SqlDialect;

abstract class SqlAbstract implements SqlStrategy
{
    /**
     * @var DialectStrategy
     */
    private $dialect;

    /**
     * SqlAbstract constructor.
     *
     * @param DialectStrategy $dialect
     */
    public function __construct(DialectStrategy $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

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
     * @param bool     $condition
     * @param callable $callable
     *
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
