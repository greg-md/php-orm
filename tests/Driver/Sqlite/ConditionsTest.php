<?php

namespace Greg\Orm\Tests\Driver\Sqlite;

use Greg\Orm\Conditions;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\ConditionsAbstract;

class ConditionsTest extends ConditionsAbstract
{
    protected function newClause()
    {
        return new Conditions(new SqliteDialect());
    }

    protected function newConditions()
    {
        return new Conditions(new SqliteDialect());
    }
}
