<?php

namespace Greg\Orm\Storage;

use Greg\Orm\Query\FromQuery;
use Greg\Orm\Query\HavingQuery;
use Greg\Orm\Query\JoinsQuery;
use Greg\Orm\Query\QueryTrait;
use Greg\Orm\Query\WhereQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteDeleteQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteInsertQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteSelectQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteUpdateQuery;

class Sqlite implements SqliteInterface
{
    use StorageAdapterTrait;

    public function select($column = null, $_ = null)
    {
        $query = new SqliteSelectQuery($this);

        if ($columns = is_array($column) ? $column : func_get_args()) {
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

    public function delete($from = null)
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

    public function from()
    {
        return new FromQuery($this);
    }

    public function joins()
    {
        return new JoinsQuery($this);
    }

    public function where()
    {
        return new WhereQuery($this);
    }

    public function having()
    {
        return new HavingQuery($this);
    }

    static public function quoteLike($value, $escape = '\\')
    {
        return QueryTrait::quoteLike($value, $escape);
    }

    static public function concat(array $values, $delimiter = '')
    {
        return QueryTrait::concat($values, $delimiter);
    }

    public function transaction(callable $callable)
    {
        return $this->getAdapter()->transaction($callable);
    }

    public function inTransaction()
    {
        return $this->getAdapter()->inTransaction();
    }

    public function beginTransaction()
    {
        return $this->getAdapter()->beginTransaction();
    }

    public function commit()
    {
        return $this->getAdapter()->commit();
    }

    public function rollBack()
    {
        return $this->getAdapter()->rollBack();
    }

    public function prepare($sql)
    {
        return $this->getAdapter()->prepare($sql);
    }

    public function query($sql)
    {
        return $this->getAdapter()->query($sql);
    }

    public function exec($sql)
    {
        return $this->getAdapter()->exec($sql);
    }

    public function truncate($name)
    {
        return $this->exec('TRUNCATE ' . $name);
    }

    public function lastInsertId($sequenceId = null)
    {
        return $this->getAdapter()->lastInsertId($sequenceId);
    }

    public function quote($value)
    {
        return $this->getAdapter()->quote($value);
    }

    public function listen(callable $callable)
    {
        return $this->getAdapter()->listen($callable);
    }

    public function fire($sql)
    {
        return $this->getAdapter()->fire($sql);
    }
}