<?php

namespace Greg\Orm\Driver;

abstract class PdoDriverAbstract extends DriverAbstract implements PdoDriverStrategy
{
    /**
     * @var callable[]
     */
    private $onInit = [];

    /**
     * @var PdoConnectorStrategy
     */
    private $connector;

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * PdoDriverAbstract constructor.
     *
     * @param PdoConnectorStrategy $strategy
     */
    public function __construct(PdoConnectorStrategy $strategy)
    {
        $this->connector = $strategy;

        return $this;
    }

    /**
     * @return $this
     */
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

    /**
     * @return \PDO
     */
    public function connection(): \PDO
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function onInit(callable $callable)
    {
        $this->onInit[] = $callable;

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function transaction(callable $callable)
    {
        $this->beginTransaction();

        call_user_func_array($callable, [$this]);

        $this->commit();

        return $this;
    }

    /**
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->connection()->inTransaction();
    }

    /**
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->connection()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection()->commit();
    }

    /**
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->connection()->rollBack();
    }

    /**
     * @param string $sql
     *
     * @return StatementStrategy
     */
    public function prepare(string $sql): StatementStrategy
    {
        $stmt = $this->tryConnection(__FUNCTION__, func_get_args());

        return $this->newStatement($stmt);
    }

    /**
     * @param string $sql
     *
     * @return StatementStrategy
     */
    public function query(string $sql): StatementStrategy
    {
        $this->fire($sql);

        $stmt = $this->tryConnection(__FUNCTION__, func_get_args());

        return $this->newStatement($stmt);
    }

    /**
     * @param string $sql
     *
     * @return int
     */
    public function exec(string $sql): int
    {
        $this->fire($sql);

        return $this->tryConnection(__FUNCTION__, func_get_args());
    }

    /**
     * @param string|null $sequenceId
     *
     * @return string
     */
    public function lastInsertId(string $sequenceId = null): string
    {
        return $this->connection()->lastInsertId(...func_get_args());
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function quote(string $value): string
    {
        return $this->connection()->quote($value);
    }

    /**
     * @param \PDOStatement $stmt
     *
     * @return StatementStrategy
     */
    protected function newStatement(\PDOStatement $stmt): StatementStrategy
    {
        return new PdoStatement($stmt, $this);
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    protected function tryConnection(string $method, array $args = [])
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

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    protected function callConnection(string $method, array $args = [])
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
