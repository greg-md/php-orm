<?php

namespace Greg\Orm\Storage;

use Greg\Orm\Adapter\AdapterInterface;
use Greg\Orm\Query\QueryTrait;
use Greg\Orm\Storage\Sqlite\Query\SqliteDeleteQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteInsertQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteSelectQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteUpdateQuery;

class Sqlite implements StorageInterface
{
    use StorageAdapterTrait;

    public function __construct($adapter = null)
    {
        if ($adapter) {
            if ($adapter instanceof AdapterInterface) {
                $this->setAdapter($adapter);
            } elseif (is_callable($adapter)) {
                $this->setCallableAdapter($adapter);
            } else {
                throw new \Exception('Wrong Mysql adapter type.');
            }
        }
    }

    public function dbName()
    {
        return $this->getAdapter()->dbName();
    }

    public function select($columns = null, $_ = null)
    {
        if (!is_array($columns)) {
            $columns = func_get_args();
        }

        $query = new SqliteSelectQuery($this);

        if ($columns) {
            $query->columns($columns);
        }

        return $query;
    }

    public function insert($into = null)
    {
        $query = new SqliteInsertQuery($this);

        if ($into !== null) {
            $query->into($into);
        }

        return $query;
    }

    public function delete($from = null, $delete = false)
    {
        $query = new SqliteDeleteQuery($this);

        if ($from !== null) {
            $query->from($from);
        }

        return $query;
    }

    public function update($table = null)
    {
        $query = new SqliteUpdateQuery($this);

        if ($table !== null) {
            $query->table($table);
        }

        return $query;
    }

    static public function quoteLike($string, $escape = '\\')
    {
        return QueryTrait::quoteLike($string, $escape);
    }

    static public function concat($array, $delimiter = '')
    {
        return QueryTrait::concat($array, $delimiter);
    }

    public function getTableSchema($tableName)
    {

    }

    public function getTableInfo($tableName)
    {

    }

    public function getTableReferences($tableName)
    {
        throw new \Exception('Table reference not implemented yet.');
    }

    public function getTableRelationships($tableName)
    {
        throw new \Exception('Table relationships not implemented yet.');
    }

    public function beginTransaction()
    {
        return $this->getAdapter()->beginTransaction();
    }

    public function commit()
    {
        return $this->getAdapter()->commit();
    }

    public function errorCode()
    {
        return $this->getAdapter()->errorCode();
    }

    public function errorInfo()
    {
        return $this->getAdapter()->errorInfo();
    }

    public function exec($query)
    {
        return $this->getAdapter()->exec($query);
    }

    public function getAttribute($name)
    {
        return $this->getAdapter()->getAttribute($name);
    }

    public function inTransaction()
    {
        return $this->getAdapter()->inTransaction();
    }

    public function lastInsertId($name = null)
    {
        return $this->getAdapter()->lastInsertId($name);
    }

    public function prepare($query, $options = [])
    {
        return $this->getAdapter()->prepare($query, $options = []);
    }

    public function query($query, $mode = null, $_ = null)
    {
        return call_user_func_array([$this->getAdapter(), 'query'], func_get_args());
    }

    public function quote($string, $type = self::PARAM_STR)
    {
        return $this->getAdapter()->quote($string, $type);
    }

    public function rollBack()
    {
        return $this->getAdapter()->rollBack();
    }

    public function setAttribute($name, $value)
    {
        return $this->getAdapter()->setAttribute($name, $value);
    }
}