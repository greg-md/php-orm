<?php

namespace Greg\Orm\Driver\Sqlite\Query;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Sqlite\Clause\SqliteConditions;
use Greg\Orm\Driver\Sqlite\SqliteUtilsTrait;
use Greg\Orm\Query\SelectQuery;

class SqliteSelectQuery extends SelectQuery
{
    use SqliteUtilsTrait;

    /**
     * @return $this
     */
    public function lockForUpdate()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function lockInShareMode()
    {
        return $this;
    }

    /**
     * @return ConditionsStrategy
     */
    protected function newHavingConditions(): ConditionsStrategy
    {
        return new SqliteConditions();
    }

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
