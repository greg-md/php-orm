<?php

namespace Greg\Orm\Clause;

trait OffsetClauseTrait
{
    /**
     * @var int
     */
    private $offset;

    /**
     * @param int $number
     * @return $this
     */
    public function offset(int $number)
    {
        $this->offset = $number;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOffset(): bool
    {
        return (bool) $this->offset;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return $this
     */
    public function clearOffset()
    {
        $this->offset = null;

        return $this;
    }
}
