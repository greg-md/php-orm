<?php

namespace Greg\Orm\Clause;

interface OrderByClauseStrategy extends OrderByClauseTraitStrategy
{
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
