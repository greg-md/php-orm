<?php

namespace Greg\Orm\Query;

use Greg\Orm\Connection\ConnectionStrategy;

trait QueryTrait
{
    private $connection;

    public function setConnection(ConnectionStrategy $strategy)
    {
        $this->connection = $strategy;

        return $this;
    }

    public function getConnection(): ?ConnectionStrategy
    {
        return $this->connection;
    }

    public function connection(): ConnectionStrategy
    {
        if (!$this->connection) {
            throw new \Exception('Query connection is not defined.');
        }

        return $this->connection;
    }
}
