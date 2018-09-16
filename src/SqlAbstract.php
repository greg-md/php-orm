<?php

namespace Greg\Orm;

use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Dialect\SqlDialectAbstract;

abstract class SqlAbstract implements SqlStrategy
{
    /**
     * @var SqlDialect
     */
    private $dialect;

    /**
     * SqlAbstract constructor.
     *
     * @param SqlDialect $dialect
     */
    public function __construct(SqlDialect $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialectAbstract();
        }

        $this->dialect = $dialect;
    }

    /**
     * @return SqlDialect
     */
    public function dialect(): SqlDialect
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
