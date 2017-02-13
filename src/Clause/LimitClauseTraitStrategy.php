<?php

namespace Greg\Orm\Clause;

interface LimitClauseTraitStrategy
{
    /**
     * @param int $number
     *
     * @return $this
     */
    public function limit(int $number);

    /**
     * @return bool
     */
    public function hasLimit(): bool;

    /**
     * @return int|null
     */
    public function getLimit(): ?int;

    /**
     * @return $this
     */
    public function clearLimit();
}
