<?php

namespace Greg\Orm\Clause;

interface OffsetClauseTraitStrategy
{
    /**
     * @param int $number
     *
     * @return $this
     */
    public function offset(int $number);

    /**
     * @return bool
     */
    public function hasOffset(): bool;

    /**
     * @return int|null
     */
    public function getOffset(): ?int;

    /**
     * @return $this
     */
    public function clearOffset();
}
