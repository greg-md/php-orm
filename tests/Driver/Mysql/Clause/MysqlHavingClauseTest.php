<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Conditions;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\Clause\HavingClauseAbstract;

class MysqlHavingClauseTest extends HavingClauseAbstract
{
    protected function newClause()
    {
        return new HavingClause(new MysqlDialect());
    }

    protected function newConditions()
    {
        return new Conditions(new MysqlDialect());
    }
}
