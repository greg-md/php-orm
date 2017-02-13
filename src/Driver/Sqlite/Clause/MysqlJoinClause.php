<?php

namespace Greg\Orm\Driver\Sqlite\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Driver\Sqlite\SqliteUtilsTrait;

class MysqlJoinClause extends JoinClause
{
    use SqliteUtilsTrait;

    /**
     * @return ConditionsStrategy
     */
    protected function newOn(): ConditionsStrategy
    {
        return new SqliteConditions();
    }
}
