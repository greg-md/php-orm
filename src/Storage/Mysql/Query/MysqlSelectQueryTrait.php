<?php

namespace Greg\Orm\Storage\Mysql\Query;

trait MysqlSelectQueryTrait
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

    protected $type = null;

    public function forUpdate()
    {
        $this->type = MysqlSelectQueryInterface::FOR_UPDATE;

        return $this;
    }

    public function lockInShareMode()
    {
        $this->type = MysqlSelectQueryInterface::LOCK_IN_SHARE_MODE;

        return $this;
    }

    public function addType($query)
    {
        if ($this->type) {
            $query .= ' ' . $this->type;
        }

        return $query;
    }

    abstract public function limit($value = null);

    abstract public function offset($value = null);
}