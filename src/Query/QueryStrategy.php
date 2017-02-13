<?php

namespace Greg\Orm\Query;

interface QueryStrategy
{
    /**
     * @param bool $condition
     * @param callable $callable
     * @return $this
     */
    public function when(bool $condition, callable $callable);

    /**
     * @return array
     */
    public function toSql(): array;

    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @return string
     */
    public function __toString(): string;
}
