<?php

namespace Greg\Orm\Tests\Driver\Mysql\Query;

use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Query\SelectQueryAbstract;

class MysqlSelectQueryTest extends SelectQueryAbstract
{
    protected function newQuery(): SelectQuery
    {
        return new SelectQuery(new MysqlDialect());
    }

    public function testCanLock()
    {
        $query = $this->newQuery()->lockForUpdate();

        $this->assertEquals(['SELECT * FOR UPDATE', []], $query->toSql());

        $query->lockInShareMode();

        $this->assertEquals(['SELECT * LOCK IN SHARE MODE', []], $query->toSql());
    }
}
