<?php

namespace Greg\Orm\Driver;

trait PdoDriverTrait
{
    /**
     * @return \PDO
     */
    abstract public function connector();

    public function transaction(callable $callable)
    {
        $this->beginTransaction();

        call_user_func_array($callable, []);

        $this->commit();

        return $this;
    }

    public function inTransaction()
    {
        return $this->connector()->inTransaction();
    }

    public function beginTransaction()
    {
        $this->connector()->beginTransaction();

        return $this;
    }

    public function commit()
    {
        $this->connector()->commit();

        return $this;
    }

    public function rollBack()
    {
        $this->connector()->rollBack();

        return $this;
    }

    /**
     * @param string $sql
     * @return PdoStmt
     */
    public function prepare($sql)
    {
        return $this->callStmt(__FUNCTION__, func_get_args());
    }

    /**
     * @param $sql
     * @return PdoStmt
     */
    public function query($sql)
    {
        $this->fire($sql);

        return $this->callStmt(__FUNCTION__, func_get_args());
    }

    public function exec($sql)
    {
        $this->fire($sql);

        return $this->tryParent(__FUNCTION__, func_get_args());
    }

    public function lastInsertId($sequenceId = null)
    {
        return $this->connector()->lastInsertId(...func_get_args());
    }

    public function quote($value)
    {
        return $this->connector()->query($value);
    }

    protected function callStmt($method, array $args = [])
    {
        $stmt = $this->tryParent($method, $args);

        return $this->newPdoStmt($stmt);
    }

    protected function tryParent($method, array $args = [])
    {
        try {
            return $this->callParent($method, $args);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 2006) {
                $this->reconnect();

                return $this->callParent($method, $args);
            }

            throw $e;
        }
    }

    protected function callParent($method, array $args = [])
    {
        $result = call_user_func_array([$this->connector(), $method], $args);

        if ($result === false) {
            $this->errorCheck();
        }

        return $result;
    }

    protected function errorCheck()
    {
        $errorInfo = $this->connector()->errorInfo();

        // Bind or column index out of range
        if ($errorInfo[1] and $errorInfo[1] != 25) {
            throw new \Exception($errorInfo[2]);
        }

        return $this;
    }
}