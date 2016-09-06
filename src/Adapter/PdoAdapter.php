<?php

namespace Greg\Orm\Adapter;

class PdoAdapter extends \PDO implements AdapterInterface
{
    protected $stmtClass = PdoStmt::class;

    protected $constructorArgs = [];

    protected $dsnProvider = null;

    protected $dsnInfo = null;

    protected $listeners = [];

    public function __construct($dsn, $username = null, $password = null, array $options = [])
    {
        $this->constructorArgs = $args = func_get_args();

        $this->parseDnsParams($dsn);

        $this->reconnect();

        return $this;
    }

    protected function parseDnsParams($dsn)
    {
        list($this->dsnProvider, $this->dsnInfo) = explode(':', $dsn, 2);

        return $this;
    }

    public function getStmtClass()
    {
        return $this->stmtClass;
    }

    public function setStmtClass($className)
    {
        $this->stmtClass = (string)$className;

        return $this;
    }

    public function reconnect()
    {
        parent::__construct(...$this->constructorArgs);

        $this->setDefaultAttributes();

        return $this;
    }

    protected function setDefaultAttributes()
    {
        if ($stmtClass = $this->getStmtClass()) {
            $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, [$stmtClass, [$this]]);
        }

        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        $this->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);

        return $this;
    }

    public function transaction(callable $callable)
    {
        $this->beginTransaction();

        call_user_func_array($callable, []);

        $this->commit();
    }

    public function prepare($sql)
    {
        return $this->callStmt(__FUNCTION__, func_get_args());
    }

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

    protected function callStmt($method, array $args = [])
    {
        /** @var PdoStmt $stmt */
        $stmt = $this->tryParent($method, $args);

        $stmt->setAdapter($this);

        return $stmt;
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

    public function listen(callable $callable)
    {
        $this->listeners[] = $callable;

        return $this;
    }

    public function fire($sql)
    {
        foreach ($this->listeners as $listener) {
            call_user_func_array($listener, [$sql]);
        }

        return $this;
    }
}