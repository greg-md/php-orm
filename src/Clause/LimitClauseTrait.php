<?php

namespace Greg\Orm\Clause;

trait LimitClauseTrait
{
    /**
     * @var
     */
    private $limit;

    /**
     * @param int $number
     *
     * @return $this
     */
    public function limit(int $number)
    {
        $this->limit = $number;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLimit(): bool
    {
        return (bool) $this->limit;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return $this
     */
    public function clearLimit()
    {
        $this->limit = null;

        return $this;
    }
}
