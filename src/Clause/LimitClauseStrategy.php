<?php

namespace Greg\Orm\Clause;

interface LimitClauseStrategy
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
