<?php

namespace Greg\Orm\Clause;

interface JoinClauseStrategy extends JoinClauseTraitStrategy
{
    /**
     * @param string|null $source
     *
     * @return array
     */
    public function toSql(string $source = null): array;

    /**
     * @param string|null $source
     *
     * @return string
     */
    public function toString(string $source = null): string;

    /**
     * @return string
     */
    public function __toString(): string;
}
