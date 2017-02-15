<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Query;

use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Query\UpdateQuery;
use Greg\Orm\Query\UpdateQueryStrategy;
use Greg\Orm\Tests\Query\UpdateQueryAbstract;

class SqliteUpdateQueryTest extends UpdateQueryAbstract
{
    protected function newQuery(): UpdateQueryStrategy
    {
        return new UpdateQuery(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQueryStrategy
    {
        return new SelectQuery(new SqliteDialect());
    }
}
