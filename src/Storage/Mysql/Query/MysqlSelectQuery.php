<?php

namespace Greg\Orm\Storage\Mysql\Query;

use Greg\Orm\Query\SelectQuery;

class MysqlSelectQuery extends SelectQuery implements MysqlSelectQueryInterface
{
    use MysqlQueryTrait, MysqlSelectQueryTrait;

    public function toString()
    {
        $query = parent::toString();

        $this->addSqlType($query);

        return $query;
    }
}