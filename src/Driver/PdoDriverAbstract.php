<?php

namespace Greg\Orm\Driver;

abstract class PdoDriverAbstract extends DriverAbstract
{
    private $dsn;

    private $username;

    private $password;

    private $options = [];

    private $onInit = [];

    private $connection;

    public function __construct(string $dsn, string $username, string $password = null, array $options = [])
    {
        $this->dsn = $dsn;

        $this->username = $username;

        $this->password = $password;

        $this->options = $options;

        return $this;
    }

    public function dsn(string $name = null): ?string
    {
        if ($name) {
            return $this->dsnInfo()[$name] ?? null;
        }

        return $this->dsn;
    }

    public function connect()
    {
        $this->reconnect();

        return $this;
    }

    public function connection(): \PDO
    {
        if (!$this->connection) {
            $this->reconnect();
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

    protected function reconnect()
    {
        $this->connection = new \PDO($this->dsn, $this->username, $this->password, $this->options);

        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        $this->connection->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);

        foreach ($this->onInit as $callable) {
            call_user_func_array($callable, [$this->connection]);
        }

        return $this;
    }

    protected function tryConnection($method, array $args = [])
    {
        try {
            return $this->callConnection($method, $args);
        } catch (\PDOException $e) {
            // If connection expired, reconnect.
            if ($e->errorInfo[1] == 2006) {
                $this->reconnect();

                return $this->callConnection($method, $args);
            }

            throw $e;
        }
    }

    protected function callConnection($method, array $args = [])
    {
        $result = call_user_func_array([$this->connection(), $method], $args);

        if ($result === false) {
            $this->errorCheck();
        }

        return $result;
    }

    protected function errorCheck()
    {
        $errorInfo = $this->connection()->errorInfo();

        // Exclude: Bind or column index out of range. It shouldn't be an exception.
//        if ($errorInfo[1] == 25) {
//            return $this;
//        }

        if ($errorInfo[1]) {
            throw new \Exception($errorInfo[2]);
        }

        return $this;
    }

    protected function dsnInfo(): array
    {
        $parts = explode(':', $this->dsn, 2);

        $info = [];

        foreach (explode(';', $parts[1] ?? '') as $part) {
            list($key, $value) = explode('=', $part, 2);

            $info[$key] = $value;
        }

        return $info;
    }
}
