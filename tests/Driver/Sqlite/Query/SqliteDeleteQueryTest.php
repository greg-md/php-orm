<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Query;

use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Tests\Query\DeleteQueryTrait;
use PHPUnit\Framework\TestCase;

class SqliteDeleteQueryTest extends TestCase
{
    use DeleteQueryTrait;

    protected function newQuery(): DeleteQuery
    {
        return new DeleteQuery(new SqliteDialect());
    }
}
