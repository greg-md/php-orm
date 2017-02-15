<?php

namespace Greg\Orm\Tests\Driver\Mysql\Query;

use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\InsertQueryStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Tests\Query\InsertQueryAbstract;

class MysqlInsertQueryTest extends InsertQueryAbstract
{
    protected function newQuery(): InsertQueryStrategy
    {
        return new InsertQuery(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQueryStrategy
    {
        return new SelectQuery(new MysqlDialect());
    }
}
