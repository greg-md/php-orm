<?php

namespace Greg\Orm\Storage\Sqlite\Query;

trait SqliteSelectQueryTrait
{
    protected function parseLimit(&$query)
    {
        if ($this->limit()) {
            $query[] = 'LIMIT ' . $this->limit();
        }

        if ($this->offset()) {
            $query[] = 'OFFSET ' . $this->offset();
        }

        return $this;
    }

    abstract public function limit($value = null);

    abstract public function offset($value = null);
}