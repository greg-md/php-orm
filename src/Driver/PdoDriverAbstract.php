<?php

namespace Greg\Orm\Driver;

use Greg\Support\Arr;
use Greg\Support\Str;

abstract class PdoDriverAbstract extends DriverAbstract
{
    private const ERROR_CONNECTION_EXPIRED = 2006;

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var callable[]
     */
    private $onInit = [];

    /**
     * @return $this
     */
    public function connect()
    {
        $this->connection = $this->connector()->connect();

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
     * @throws \Exception
     *
     * @return $this
     */
    public function transaction(callable $callable)
    {
        $this->beginTransaction();

        try {
            call_user_func_array($callable, [$this]);

            $this->commit();

            return $this;
        } catch (\Exception $e) {
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->tryConnection(__FUNCTION__);
    }

    /**
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->tryConnection(__FUNCTION__);
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->tryConnection(__FUNCTION__);
    }

    /**
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->tryConnection(__FUNCTION__);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return int
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->prepare($sql, $params)->rowCount();
    }

    /**
     * @param string|null $sequenceId
     *
     * @return string
     */
    public function lastInsertId(string $sequenceId = null): string
    {
        return $this->tryConnection(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function quote(string $value): string
    {
        return $this->tryConnection(__FUNCTION__, [$value]);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return \string[]
     */
    public function fetch(string $sql, array $params = [])
    {
        return $this->prepare($sql, $params)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return \string[][]
     */
    public function fetchAll(string $sql, array $params = [])
    {
        return $this->prepare($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return \Generator
     */
    public function fetchYield(string $sql, array $params = [])
    {
        $stmt = $this->prepare($sql, $params);

        while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
        }
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return string
     */
    public function column(string $sql, array $params = [], string $column = '0')
    {
        $stmt = $this->prepare($sql, $params);

        if (Str::isDigit($column)) {
            return $stmt->fetchColumn($column);
        }

        $record = $stmt->fetch();

        return $record ? Arr::get($record, $column) : null;
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return \string[]
     */
    public function columnAll(string $sql, array $params = [], string $column = '0')
    {
        $stmt = $this->prepare($sql, $params);

        if (Str::isDigit($column)) {
            $values = [];

            while (($value = $stmt->fetchColumn($column)) !== false) {
                $values[] = $value;
            }

            return $values;
        }

        $values = [];

        while ($record = $stmt->fetch()) {
            $values[] = Arr::get($record, $column);
        }

        return $values;
    }

    public function columnYield(string $sql, array $params = [], string $column = '0')
    {
        $stmt = $this->prepare($sql, $params);

        if (Str::isDigit($column)) {
            while (($value = $stmt->fetchColumn($column)) !== false) {
                yield $value;
            }
        } else {
            while ($record = $stmt->fetch()) {
                yield Arr::get($record, $column);
            }
        }
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $key
     * @param string $value
     *
     * @return \string[]
     */
    public function pairs(string $sql, array $params = [], string $key = '0', string $value = '1')
    {
        $stmt = $this->prepare($sql, $params);

        $pairs = [];

        while ($record = $stmt->fetch()) {
            $pairs[Arr::get($record, $key)] = Arr::get($record, $value);
        }

        return $pairs;
    }

    public function pairsYield(string $sql, array $params = [], string $key = '0', string $value = '1')
    {
        $stmt = $this->prepare($sql, $params);

        while ($record = $stmt->fetch()) {
            yield Arr::get($record, $key) => Arr::get($record, $value);
        }
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return \PDOStatement
     */
    protected function prepare(string $sql, array $params = []): \PDOStatement
    {
        /** @var \PDOStatement $stmt */
        $stmt = $this->tryConnection(__FUNCTION__, [$sql, $params]);

        if ($params) {
            $k = 1;

            foreach ($params as $key => $value) {
                $stmt->bindValue(is_int($key) ? $k++ : $key, $value);
            }
        }

        $this->fire($sql, $params);

        $stmt->execute();

        return $stmt;
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
            if ($e->errorInfo[1] == self::ERROR_CONNECTION_EXPIRED) {
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

        $errorInfo = $this->connection()->errorInfo();

        if ($errorInfo[1]) {
            $e = new \PDOException($errorInfo[2], $errorInfo[1]);

            $e->errorInfo = $errorInfo;

            throw $e;
        }

        return $result;
    }

    abstract protected function connector(): PdoConnectorStrategy;
}
