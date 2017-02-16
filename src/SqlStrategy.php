<?php

namespace Greg\Orm;

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
     * @return DialectStrategy
     */
    public function dialect(): DialectStrategy;
}
