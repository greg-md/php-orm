<?php

namespace Greg\Orm\Query;

use Greg\Orm\Connection\Connection;

trait QueryTrait
{
    private $connection;

    public function setConnection(Connection $strategy)
    {
        $this->connection = $strategy;

        return $this;
    }

    public function getConnection(): ?Connection
    {
        return $this->connection;
    }

    public function connection(): Connection
    {
        if (!$this->connection) {
            throw new \Exception('Query connection is not defined.');
        }

        return $this->connection;
    }
}
