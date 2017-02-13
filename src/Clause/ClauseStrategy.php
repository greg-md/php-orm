<?php

namespace Greg\Orm\Clause;

interface ClauseStrategy
{
    /**
     * @param bool $condition
     * @param callable $callable
     * @return $this
     */
    public function when(bool $condition, callable $callable);
}
