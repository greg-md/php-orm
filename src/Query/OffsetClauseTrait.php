<?php

namespace Greg\Orm\Query;

trait OffsetClauseTrait
{
    protected $offset = null;

    public function offset($number)
    {
        $this->offset = (int) $number;

        return $this;
    }

    protected function addOffsetToSql(&$sql)
    {
        if ($this->offset) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $this;
    }

    public function hasOffset()
    {
        return (bool) $this->offset;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setOffset($number)
    {
        return $this->offset($number);
    }

    public function clearOffset()
    {
        $this->offset = null;

        return $this;
    }
}
