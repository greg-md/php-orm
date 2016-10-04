<?php

namespace Greg\Orm\Query;

interface OffsetClauseTraitInterface
{
    /**
     * @param $number
     *
     * @return $this
     */
    public function offset($number);

    public function hasOffset();

    public function getOffset();

    public function setOffset($number);

    public function clearOffset();
}
