<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\Clause\OrderByClauseAbstract;

class MysqlOrderByClauseTest extends OrderByClauseAbstract
{
    protected function newClause(): OrderByClauseStrategy
    {
        return new OrderByClause(new MysqlDialect());
    }
}
