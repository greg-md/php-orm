<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Query;

use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;
use Greg\Orm\Tests\Query\UpdateQueryTrait;
use PHPUnit\Framework\TestCase;

class SqliteUpdateQueryTest extends TestCase
{
    use UpdateQueryTrait;

    protected function newQuery(): UpdateQuery
    {
        return new UpdateQuery(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new SqliteDialect());
    }
}
