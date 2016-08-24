<?php

namespace Greg\Orm\Adapter;

class PdoAdapter extends \PDO implements AdapterInterface
{
    protected $stmtClass = PdoStmt::class;

    protected $constructorArgs = [];

    public function __construct($dsn, $username = null, $password = null, $options = null)
    {
        $this->constructorArgs = $args = func_get_args();

        parent::__construct(...$args);

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

    public function reconnect()
    {
        parent::__construct(...$this->constructorArgs);

        return $this;
    }

    public function exec($query)
    {
        return $this->callParent(__FUNCTION__, func_get_args());
    }

    public function prepare($query, $options = null)
    {
        return $this->callParentStmt(__FUNCTION__, func_get_args());
    }

    public function query()
    {
        return $this->callParentStmt(__FUNCTION__, func_get_args());
    }

    protected function callParentStmt($method, array $args = [])
    {
        /** @var PdoStmt $stmt */
        $stmt = $this->callParent($method, $args);

        $stmt->setAdapter($this);

        return $stmt;
    }

    protected function callParent($method, array $args = [])
    {
        try {
            return $this->_callParent($method, $args);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 2006) {
                $this->reconnect();

                return $this->_callParent($method, $args);
            }
            throw $e;
        }
    }

    protected function _callParent($method, array $args = [])
    {
        $result = call_user_func_array(['parent', $method], $args);

        if ($result === false) {
            $this->errorCheck();
        }

        return $result;
    }

    public function errorCheck()
    {
        $errorInfo = $this->errorInfo();

        // Bind or column index out of range
        if ($errorInfo[1] and $errorInfo[1] != 25) {
            throw new \Exception($errorInfo[2]);
        }

        return $this;
    }

    public function setStmtClass($name)
    {
        $this->stmtClass = (string)$name;

        return $this;
    }

    public function getStmtClass()
    {
        return $this->stmtClass;
    }
}