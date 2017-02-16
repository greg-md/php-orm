<?php

namespace Greg\Orm\Tests\Driver\Mysql\Query;

use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;
use Greg\Orm\Tests\Query\UpdateQueryAbstract;

class MysqlUpdateQueryTest extends UpdateQueryAbstract
{
    protected function newQuery(): UpdateQuery
    {
        return new UpdateQuery(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new MysqlDialect());
    }
}
