<?php

namespace Greg\Orm\Adapter;

use Greg\Support\Arr;
use Greg\Support\Str;

class PdoStmt extends \PDOStatement implements StmtInterface
{
    /**
     * \PDOStatement require it to be protected
     */
    protected function __construct() {}

    /**
     * @var PdoAdapter|null
     */
    protected $adapter = null;

    public function bindParams(array $params)
    {
        $k = 1;

        foreach($params as $key => $param) {
            $this->bindParam(is_int($key) ? $k++ : $key, $param);
        }

        return $this;
    }

    public function bindParam($key, &$value, $_ = null, $_ = null, $_ = null)
    {
        return parent::bindValue($key, $value);
    }

    /**
     * @todo disable "array" type for $params, until we use PDO as connector.
     * @param array $params
     * @return mixed
     */
    public function execute($params = [])
    {
        $this->getAdapter()->fire($this->queryString);

        return $this->tryParent(__FUNCTION__, func_get_args());
    }

    protected function tryParent($method, array $args = [])
    {
        try {
            return $this->callParent($method, $args);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 2006) {
                $this->getAdapter()->reconnect();

                return $this->callParent($method, $args);
            }
            throw $e;
        }
    }

    protected function callParent($method, array $args = [])
    {
        $result = call_user_func_array(['parent', $method], $args);

        if ($result === false) {
            $this->errorCheck();
        }

        return $result;
    }

    protected function errorCheck()
    {
        $errorInfo = $this->errorInfo();

        // Bind or column index out of range
        if ($errorInfo[1] and $errorInfo[1] != 25) {
            throw new \Exception($errorInfo[2]);
        }

        return $this;
    }

    public function fetchAssoc()
    {
        return $this->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAssocAll()
    {
        return $this->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchAssocAllGenerator()
    {
        while ($record = $this->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
        }
    }

    public function fetchColumn($column = 0)
    {
        if (Str::isNaturalNumber($column)) {
            return parent::fetchColumn($column);
        }

        $row = $this->fetchAssoc();

        return $row ? Arr::get($row, $column) : null;
    }

    public function fetchAllColumn($column = 0)
    {
        return array_column($this->fetchAll(), $column);
    }

    public function fetchPairs($key = 0, $value = 1)
    {
        $pairs = [];

        foreach($this->fetchAll() as $row) {
            $pairs[$row[$key]] = $row[$value];
        }

        return $pairs;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}