<?php

namespace Greg\Orm\Table;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Query\InsertQuery;

trait InsertTableQueryTrait
{
    public function newInsertQuery(): InsertQuery
    {
        $query = $this->driver()->insert();

        $query->into($this);

        return $query;
    }

    abstract public function driver(): DriverStrategy;
}
