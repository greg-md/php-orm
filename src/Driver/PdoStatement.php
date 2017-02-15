<?php

namespace Greg\Orm\Driver;

use Greg\Support\Arr;
use Greg\Support\Str;

class PdoStatement implements StatementStrategy
{
    protected $stmt = null;

    protected $driver = null;

    public function __construct(\PDOStatement $stmt, PdoDriverStrategy $driver)
    {
        $this->stmt = $stmt;

        $this->driver = $driver;
    }

    public function bindParam($key, $value)
    {
        return $this->stmt->bindValue($key, $value);
    }

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
     * @return mixed
     */
    public function execute(array $params = [])
    {
        $this->driver->fire((string) $this->stmt->queryString);

        return $this->tryStatement(__FUNCTION__, func_get_args());
    }

    public function fetch()
    {
        return $this->stmt->fetch();
    }

    public function fetchAll()
    {
        return $this->stmt->fetchAll();
    }

    public function fetchYield()
    {
        while ($record = $this->stmt->fetch()) {
            yield $record;
        }
    }

    public function fetchAssoc()
    {
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAssocAll()
    {
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchAssocYield()
    {
        while ($record = $this->stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
        }
    }

    public function fetchColumn($column = 0)
    {
        if (Str::isDigit($column)) {
            return $this->stmt->fetchColumn($column);
        }

        $row = $this->fetchAssoc();

        return $row ? Arr::get($row, $column) : null;
    }

    public function fetchColumnAll($column = 0)
    {
        return array_column($this->fetchAll(), $column);
    }

    public function fetchPairs($key = 0, $value = 1)
    {
        $all = $this->fetchAll();

        return Arr::pairs($all, $key, $value);
    }

    protected function tryStatement($method, array $args = [])
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

    protected function callStatement($method, array $args = [])
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
