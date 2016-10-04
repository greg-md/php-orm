<?php

namespace Greg\Orm\Driver;

use Greg\Support\Arr;
use Greg\Support\Str;

class PdoStmt implements StmtInterface
{
    protected $stmt = null;

    protected $adapter = null;

    public function __construct(\PDOStatement $stmt, DriverInterface $adapter)
    {
        $this->stmt = $stmt;

        $this->adapter = $adapter;
    }

    public function bindParams(array $params)
    {
        $k = 1;

        foreach ($params as $key => $param) {
            $this->bindParam(is_int($key) ? $k++ : $key, $param);
        }

        return $this;
    }

    public function bindParam($key, $value)
    {
        return $this->stmt->bindValue($key, $value);
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function execute(array $params = [])
    {
        $this->adapter->fire($this->stmt->queryString);

        return $this->tryParent(__FUNCTION__, func_get_args());
    }

    protected function tryParent($method, array $args = [])
    {
        try {
            return $this->callParent($method, $args);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 2006) {
                $this->adapter->reconnect();

                return $this->callParent($method, $args);
            }
            throw $e;
        }
    }

    protected function callParent($method, array $args = [])
    {
        $result = call_user_func_array([$this->stmt, $method], $args);

        if ($result === false) {
            $this->errorCheck();
        }

        return $result;
    }

    protected function errorCheck()
    {
        $errorInfo = $this->stmt->errorInfo();

        // Bind or column index out of range
        if ($errorInfo[1] and $errorInfo[1] != 25) {
            throw new \Exception($errorInfo[2]);
        }

        return $this;
    }

    public function fetch()
    {
        return $this->stmt->fetch();
    }

    public function fetchAll()
    {
        return $this->stmt->fetchAll();
    }

    public function fetchGenerator()
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

    public function fetchAssocGenerator()
    {
        while ($record = $this->stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
        }
    }

    public function fetchColumn($column = 0)
    {
        if (Str::isNaturalNumber($column)) {
            return $this->stmt->fetchColumn($column);
        }

        $row = $this->fetchAssoc();

        return $row ? Arr::getRef($row, $column) : null;
    }

    public function fetchAllColumn($column = 0)
    {
        return array_column($this->fetchAll(), $column);
    }

    public function fetchPairs($key = 0, $value = 1)
    {
        $pairs = [];

        foreach ($this->fetchAll() as $row) {
            $pairs[Arr::getRef($row, $key)] = Arr::getRef($row, $value);
        }

        return $pairs;
    }
}
