<?php

namespace Greg\Orm\Driver\Sqlite\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Clause\HavingClause;

class SqliteHavingClause extends HavingClause
{
    /**
     * @return ConditionsStrategy
     */
    protected function newHavingConditions(): ConditionsStrategy
    {
        return new SqliteConditions();
    }
}
