<?php

namespace Greg\Orm\Driver\Mysql\Query;

use Greg\Orm\Query\SelectQuery;

class MysqlSelectQuery extends SelectQuery implements MysqlSelectQueryInterface
{
    use MysqlClauseSupportTrait;

    const FOR_UPDATE = 'FOR UPDATE';

    const LOCK_IN_SHARE_MODE = 'LOCK IN SHARE MODE';

    protected $type = null;

    public function forUpdate()
    {
        $this->type = static::FOR_UPDATE;

        return $this;
    }

    public function lockInShareMode()
    {
        $this->type = static::LOCK_IN_SHARE_MODE;

        return $this;
    }

    protected function addTypeToSql(&$sql)
    {
        if ($this->type) {
            $sql .= ' ' . $this->type;
        }

        return $sql;
    }

    public function toString()
    {
        $query = parent::toString();

        $this->addTypeToSql($query);

        return $query;
    }
}