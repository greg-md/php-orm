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
        $this->type(MysqlSelectQuery::FOR_UPDATE);

        return $this;
    }

    public function lockInShareMode()
    {
        $this->type(MysqlSelectQuery::LOCK_IN_SHARE_MODE);

        return $this;
    }

    public function addType($query)
    {
        switch($type = $this->type()) {
            case MysqlSelectQuery::FOR_UPDATE:
                $query .= ' FOR UPDATE';
                break;
            case MysqlSelectQuery::LOCK_IN_SHARE_MODE:
                $query .= ' LOCK IN SHARE MODE';
                break;
        }

        return $query;
    }

    public function type($type = null)
    {
        if (func_num_args()) {
            $this->type = (string)$type;

            return $this;
        }

        return $this->type;
    }

    abstract public function limit($value = null);

    abstract public function offset($value = null);
}