<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Query;

use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Query\InsertQueryAbstract;

class SqliteInsertQueryTest extends InsertQueryAbstract
{
    protected function newQuery(): InsertQuery
    {
        return new InsertQuery(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new SqliteDialect());
    }
}
