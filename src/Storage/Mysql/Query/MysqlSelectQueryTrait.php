<?php

namespace Greg\Orm\Storage\Mysql\Query;

trait MysqlSelectQueryTrait
{
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

    public function addSqlType(&$query)
    {
        if ($this->type) {
            $query .= ' ' . $this->type;
        }

        return $query;
    }
}