<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Query;

use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Query\SelectQueryTrait;
use PHPUnit\Framework\TestCase;

class SqliteSelectQueryTest extends TestCase
{
    use SelectQueryTrait;

    protected function newQuery(): SelectQuery
    {
        return new SelectQuery(new SqliteDialect());
    }
}
