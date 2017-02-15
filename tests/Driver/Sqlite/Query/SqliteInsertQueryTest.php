<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Query;

use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\InsertQueryStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Tests\Query\InsertQueryAbstract;

class SqliteInsertQueryTest extends InsertQueryAbstract
{
    protected function newQuery(): InsertQueryStrategy
    {
        return new InsertQuery(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQueryStrategy
    {
        return new SelectQuery(new SqliteDialect());
    }
}
