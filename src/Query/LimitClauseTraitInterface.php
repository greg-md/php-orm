<?php

namespace Greg\Orm\Query;

interface LimitClauseTraitInterface
{
    /**
     * @param $number
     *
     * @return $this
     */
    public function limit($number);

    public function hasLimit();

    public function getLimit();

    public function setLimit($number);

    public function clearLimit();
}
