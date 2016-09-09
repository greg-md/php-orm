<?php

namespace Greg\Orm\Query;

trait LimitClauseTrait
{
    protected $limit = null;

    public function limit($number)
    {
        $this->limit = (int)$number;

        return $this;
    }

    public function addLimitToSql(&$sql)
    {
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        return $this;
    }
}