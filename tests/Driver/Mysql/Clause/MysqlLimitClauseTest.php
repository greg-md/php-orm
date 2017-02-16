<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\Clause\LimitClauseAbstract;

class MysqlLimitClauseTest extends LimitClauseAbstract
{
    protected function newClause(): LimitClause
    {
        return new LimitClause(new MysqlDialect());
    }
}
