<?php

namespace Greg\Orm\Query;

trait OffsetClauseTrait
{
    protected $offset = null;

    public function offset($number)
    {
        $this->offset = (int)$number;

        return $this;
    }

    public function addOffsetToSql(&$sql)
    {
        if ($this->offset) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $this;
    }
}