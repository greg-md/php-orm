<?php

namespace Greg\Orm\Table;

use Greg\Orm\Connection\Connection;
use Greg\Orm\Query\InsertQuery;

trait InsertTableQueryTrait
{
    public function newInsertQuery(): InsertQuery
    {
        $query = $this->connection()->insert();

        $query->into($this);

        return $query;
    }

    abstract public function connection(): Connection;
}
