<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Conditions;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\HavingClauseAbstract;

class SqliteHavingClauseTest extends HavingClauseAbstract
{
    protected function newClause()
    {
        return new HavingClause(new SqliteDialect());
    }

    protected function newConditions()
    {
        return new Conditions(new SqliteDialect());
    }
}
