<?php

namespace Greg\Orm\Driver;

abstract class PdoDriverAbstract extends DriverAbstract implements PdoDriverStrategy
{
    private $onInit = [];

    /**
     * @var PdoConnectorStrategy
     */
    private $connector;

    /**
     * @var \PDO
     */
    private $connection;

    public function __construct(PdoConnectorStrategy $strategy)
    {
        $this->connector = $strategy;

        return $this;
    }

    public function connect()
    {
        $this->connection = $this->connector->connect();

        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        $this->connection->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);

        foreach ($this->onInit as $callable) {
            call_user_func_array($callable, [$this->connection]);
        }

        return $this;
    }

    public function connection(): \PDO
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    public function onInit(callable $callable)
    {
        $this->onInit[] = $callable;

        return $this;
    }

    public function transaction(callable $callable)
    {
        $this->beginTransaction();

        call_user_func_array($callable, [$this]);

        $this->commit();

        return $this;
    }

    public function inTransaction(): bool
    {
        return $this->connection()->inTransaction();
    }

    public function beginTransaction(): bool
    {
        return $this->connection()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection()->commit();
    }

    public function rollBack(): bool
    {
        return $this->connection()->rollBack();
    }

    public function prepare(string $sql): StatementStrategy
    {
        $stmt = $this->tryConnection(__FUNCTION__, func_get_args());

        return $this->newStatement($stmt);
    }

    public function query(string $sql): StatementStrategy
    {
        $this->fire($sql);

        $stmt = $this->tryConnection(__FUNCTION__, func_get_args());

        return $this->newStatement($stmt);
    }

    public function exec(string $sql): int
    {
        $this->fire($sql);

        return $this->tryConnection(__FUNCTION__, func_get_args());
    }

    public function lastInsertId(string $sequenceId = null): string
    {
        return $this->connection()->lastInsertId(...func_get_args());
    }

    public function quote(string $value): string
    {
        return $this->connection()->quote($value);
    }

    protected function newStatement(\PDOStatement $stmt): StatementStrategy
    {
        return new PdoStatement($stmt, $this);
    }

    protected function tryConnection($method, array $args = [])
    {
        try {
            return $this->callConnection($method, $args);
        } catch (\PDOException $e) {
            // If connection expired, reconnect.
            if ($e->errorInfo[1] == 2006) {
                $this->connect();

                return $this->callConnection($method, $args);
            }

            throw $e;
        }
    }

    protected function callConnection($method, array $args = [])
    {
        $result = call_user_func_array([$this->connection(), $method], $args);

        if ($result === false) {
            $errorInfo = $this->connection()->errorInfo();

            // Exclude: Bind or column index out of range. It shouldn't be an exception.
//        if ($errorInfo[1] == 25) {
//            return $this;
//        }

            $e = new \PDOException($errorInfo[2], $errorInfo[1]);

            $e->errorInfo = $errorInfo;

            throw $e;
        }

        return $result;
    }
}
