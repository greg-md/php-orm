<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Query;

use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Tests\Query\SelectQueryAbstract;

class SqliteSelectQueryTest extends SelectQueryAbstract
{
    protected function newQuery(): SelectQueryStrategy
    {
        return new SelectQuery(new SqliteDialect());
    }
}
