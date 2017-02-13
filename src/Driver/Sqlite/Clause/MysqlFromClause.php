<?php

namespace Greg\Orm\Driver\Sqlite\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Clause\FromClause;
use Greg\Orm\Driver\Sqlite\SqliteUtilsTrait;

class SqliteFromClause extends FromClause
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
