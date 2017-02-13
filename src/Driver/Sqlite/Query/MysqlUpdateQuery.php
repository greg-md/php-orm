<?php

namespace Greg\Orm\Driver\Sqlite\Query;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Sqlite\Clause\SqliteConditions;
use Greg\Orm\Driver\Sqlite\SqliteUtilsTrait;
use Greg\Orm\Query\UpdateQuery;

class SqliteUpdateQuery extends UpdateQuery
{
    use SqliteUtilsTrait;

    /**
     * @return ConditionsStrategy
     */
    protected function newOn(): ConditionsStrategy
    {
        return new SqliteConditions();
    }

    /**
     * @return ConditionsStrategy
     */
    protected function newWhereConditions(): ConditionsStrategy
    {
        return new SqliteConditions();
    }
}
