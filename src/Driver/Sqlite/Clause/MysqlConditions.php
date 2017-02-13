<?php

namespace Greg\Orm\Driver\Sqlite\Clause;

use Greg\Orm\Clause\Conditions;
use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Sqlite\SqliteUtilsTrait;

class SqliteConditions extends Conditions
{
    use SqliteUtilsTrait;

    /**
     * @return ConditionsStrategy
     */
    protected function newConditions(): ConditionsStrategy
    {
        return new static();
    }
}
