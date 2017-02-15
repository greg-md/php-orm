<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\Clause\GroupByClauseAbstract;

class MysqlGroupByClauseTest extends GroupByClauseAbstract
{
    protected function newClause(): GroupByClauseStrategy
    {
        return new GroupByClause(new MysqlDialect());
    }
}
