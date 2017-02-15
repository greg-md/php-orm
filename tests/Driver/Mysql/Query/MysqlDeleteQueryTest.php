<?php

namespace Greg\Orm\Tests\Driver\Mysql\Query;

use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\DeleteQueryStrategy;
use Greg\Orm\Tests\Query\DeleteQueryAbstract;

class MysqlDeleteQueryTest extends DeleteQueryAbstract
{
    protected function newQuery(): DeleteQueryStrategy
    {
        return new DeleteQuery(new MysqlDialect());
    }
}
