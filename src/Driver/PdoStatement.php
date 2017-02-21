<?php

namespace Greg\Orm\Driver;

use Greg\Support\Arr;
use Greg\Support\Str;

class PdoStatement implements StatementStrategy
{
    /**
     * @var \PDOStatement
     */
    private $stmt;

    /**
     * @var PdoDriverStrategy
     */
    private $driver;

    /**
     * @param \PDOStatement     $stmt
     * @param PdoDriverStrategy $driver
     */
    public function __construct(\PDOStatement $stmt, PdoDriverStrategy $driver)
    {
        $this->stmt = $stmt;

        $this->driver = $driver;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function bind(string $key, string $value)
    {
        $this->stmt->bindValue($key, $value);

        return $this;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function bindMultiple(array $params)
    {
        $k = 1;

        foreach ($params as $key => $param) {
            $this->bind(is_int($key) ? $k++ : $key, $param);
        }

        return $this;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function execute(array $params = []): bool
    {
        $this->driver->fire((string) $this->stmt->queryString);

        return $this->tryStatement(__FUNCTION__, func_get_args());
    }

    /**
     * @return string[]
     */
    public function fetch()
    {
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @return string[][]
     */
    public function fetchAll()
    {
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return \Generator
     */
    public function fetchYield()
    {
        while ($record = $this->stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
        }
    }

    /**
     * @param string $column
     *
     * @return string
     */
    public function column(string $column = '0')
    {
        if (Str::isDigit($column)) {
            return $this->stmt->fetchColumn($column);
        }

        $record = $this->fetch();

        return $record ? Arr::get($record, $column) : null;
    }

    /**
     * @param string $column
     *
     * @return string[]
     */
    public function columnAll(string $column = '0')
    {
        if (Str::isDigit($column)) {
            $values = [];

            while ($value = $this->stmt->fetchColumn($column)) {
                $values[] = $value;
            }

            return $values;
        }

        $values = [];

        while ($record = $this->stmt->fetch()) {
            $values[] = Arr::get($record, $column);
        }

        return $values;
    }

    public function columnYield(string $column = '0')
    {
        if (Str::isDigit($column)) {
            while ($value = $this->stmt->fetchColumn($column)) {
                yield $value;
            }
        } else {
            while ($record = $this->stmt->fetch()) {
                yield Arr::get($record, $column);
            }
        }
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return string[]
     */
    public function pairs(string $key = '0', string $value = '1')
    {
        $pairs = [];

        while ($record = $this->stmt->fetch()) {
            $pairs[Arr::get($record, $key)] = Arr::get($record, $value);
        }

        return $pairs;
    }

    public function pairsYield(string $key = '0', string $value = '1')
    {
        while ($record = $this->stmt->fetch()) {
            yield Arr::get($record, $key) => Arr::get($record, $value);
        }
    }

    public function affectedRows(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    protected function tryStatement(string $method, array $args = [])
    {
        try {
            return $this->callStatement($method, $args);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 2006) {
                $this->driver->connect();

                return $this->callStatement($method, $args);
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
    protected function callStatement(string $method, array $args = [])
    {
        $result = call_user_func_array([$this->stmt, $method], $args);

        if ($result === false) {
            $errorInfo = $this->stmt->errorInfo();

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
