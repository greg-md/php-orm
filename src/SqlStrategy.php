<?php

namespace Greg\Orm;

use Greg\Orm\Dialect\SqlDialect;

interface SqlStrategy
{
    /**
     * @param bool     $condition
     * @param callable $callable
     *
     * @return $this
     */
    public function when(bool $condition, callable $callable);

    /**
     * @return SqlDialect
     */
    public function dialect(): SqlDialect;
}
