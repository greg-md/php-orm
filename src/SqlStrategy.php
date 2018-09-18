<?php

namespace Greg\Orm;

use Greg\Orm\Dialect\SqlDialectStrategy;

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
     * @return SqlDialectStrategy
     */
    public function dialect(): SqlDialectStrategy;
}
