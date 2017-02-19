<?php

namespace Greg\Orm\Tests\Driver\Mysql\Query;

use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Tests\Query\DeleteQueryTrait;
use PHPUnit\Framework\TestCase;

class MysqlDeleteQueryTest extends TestCase
{
    use DeleteQueryTrait;

    protected function newQuery(): DeleteQuery
    {
        return new DeleteQuery(new MysqlDialect());
    }
}
