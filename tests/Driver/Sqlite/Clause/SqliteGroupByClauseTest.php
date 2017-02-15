<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\GroupByClauseAbstract;

class SqliteGroupByClauseTest extends GroupByClauseAbstract
{
    protected function newClause(): GroupByClauseStrategy
    {
        return new GroupByClause(new SqliteDialect());
    }
}
