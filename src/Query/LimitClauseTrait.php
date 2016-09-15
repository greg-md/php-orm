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

    protected function addLimitToSql(&$sql)
    {
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        return $this;
    }

    public function hasLimit()
    {
        return (bool)$this->limit;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($number)
    {
        return $this->limit($number);
    }

    public function clearLimit()
    {
        $this->limit = null;

        return $this;
    }
}