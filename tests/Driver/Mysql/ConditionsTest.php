<?php

namespace Greg\Orm\Tests\Driver\Mysql;

use Greg\Orm\Conditions;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\ConditionsAbstract;

class ConditionsTest extends ConditionsAbstract
{
    protected function newClause()
    {
        return new Conditions(new MysqlDialect());
    }

    protected function newConditions()
    {
        return new Conditions(new MysqlDialect());
    }
}
