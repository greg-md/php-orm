<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OffsetClauseStrategy;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\Clause\OffsetClauseAbstract;

class MysqlOffsetClauseTest extends OffsetClauseAbstract
{
    protected function newClause(): OffsetClauseStrategy
    {
        return new OffsetClause(new MysqlDialect());
    }
}
