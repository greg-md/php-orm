<?php

namespace Greg\Orm\Tests\Driver\Mysql\Query;

use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Query\UpdateQuery;
use Greg\Orm\Query\UpdateQueryStrategy;
use Greg\Orm\Tests\Query\UpdateQueryAbstract;

class MysqlUpdateQueryTest extends UpdateQueryAbstract
{
    protected function newQuery(): UpdateQueryStrategy
    {
        return new UpdateQuery(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQueryStrategy
    {
        return new SelectQuery(new MysqlDialect());
    }
}
