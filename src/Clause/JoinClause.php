<?php

namespace Greg\Orm\Clause;

use Greg\Orm\WhenTrait;

abstract class JoinClause implements JoinClauseStrategy
{
    use JoinClauseTrait, WhenTrait;

    /**
     * @param string|null $source
     * @return array
     */
    public function toSql(string $source = null): array
    {
        return $this->joinToSql($source);
    }

    /**
     * @param string|null $source
     * @return string
     */
    public function toString(string $source = null): string
    {
        return $this->joinToString($source);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
