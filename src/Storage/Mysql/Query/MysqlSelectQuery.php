<?php

namespace Greg\Orm\Storage\Mysql\Query;

use Greg\Orm\Query\SelectQuery;

class MysqlSelectQuery extends SelectQuery
{
    use MysqlQueryTrait, MysqlSelectQueryTrait;

    const FOR_UPDATE = 'FOR UPDATE';

    const LOCK_IN_SHARE_MODE = 'LOCK IN SHARE MODE';

    public function toString()
    {
        $query = parent::toString();

        $this->addType($query);

        return $query;
    }
}