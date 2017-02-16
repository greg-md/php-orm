<?php

namespace Greg\Orm\Tests\Driver\Mysql\Query;

use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Query\InsertQueryAbstract;

class MysqlInsertQueryTest extends InsertQueryAbstract
{
    protected function newQuery(): InsertQuery
    {
        return new InsertQuery(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new MysqlDialect());
    }
}
