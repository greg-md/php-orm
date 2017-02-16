<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Query;

use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Tests\Query\DeleteQueryAbstract;

class SqliteDeleteQueryTest extends DeleteQueryAbstract
{
    protected function newQuery(): DeleteQuery
    {
        return new DeleteQuery(new SqliteDialect());
    }
}
