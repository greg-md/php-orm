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
    public function bindParam(string $key, string $value)
    {
        $this->stmt->bindValue($key, $value);

        return $this;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function bindParams(array $params)
    {
        $k = 1;

        foreach ($params as $key => $param) {
            $this->bindParam(is_int($key) ? $k++ : $key, $param);
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
        return $this->stmt->fetch();
    }

    /**
     * @return string[][]
     */
    public function fetchAll()
    {
        return $this->stmt->fetchAll();
    }

    /**
     * @return \Generator
     */
    public function fetchYield()
    {
        while ($record = $this->stmt->fetch()) {
            yield $record;
        }
    }

    /**
     * @return string[]
     */
    public function fetchAssoc()
    {
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @return string[][]
     */
    public function fetchAssocAll()
    {
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @return \Generator
     */
    public function fetchAssocYield()
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
    public function fetchColumn(string $column = '0')
    {
        if (Str::isDigit($column)) {
            return $this->stmt->fetchColumn($column);
        }

        $row = $this->fetchAssoc();

        return $row ? Arr::get($row, $column) : null;
    }

    /**
     * @param string $column
     *
     * @return string[]
     */
    public function fetchColumnAll(string $column = '0')
    {
        return array_column($this->fetchAll(), $column);
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return string[]
     */
    public function fetchPairs(string $key = '0', string $value = '1')
    {
        $all = $this->fetchAll();

        return Arr::pairs($all, $key, $value);
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
