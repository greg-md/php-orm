<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\Clause\OrderByClauseAbstract;

class MysqlOrderByClauseTest extends OrderByClauseAbstract
{
    protected function newClause(): OrderByClause
    {
        return new OrderByClause(new MysqlDialect());
    }
}
