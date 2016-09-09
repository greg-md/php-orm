<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Query\FromQuery;
use Greg\Orm\Query\HavingQuery;
use Greg\Orm\Query\JoinsQuery;
use Greg\Orm\Query\QueryTrait;
use Greg\Orm\Query\WhereQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteDeleteQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteInsertQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteSelectQuery;
use Greg\Orm\Storage\Sqlite\Query\SqliteUpdateQuery;

class Sqlite extends DriverAbstract implements SqliteInterface
{
    use PdoDriverTrait;

    private $path = null;

    private $connector = null;

    public function __construct($path)
    {
        $this->path = $path;

        return $this;
    }

    public function connector()
    {
        if (!$this->connector) {
            $this->connector = new \PDO('sqlite:' . $this->path);
        }

        return $this->connector;
    }

    public function reconnect()
    {
        $this->connector = null;

        return $this;
    }

    public function truncate($tableName)
    {
        return $this->exec('TRUNCATE ' . $tableName);
    }

    protected function newPdoStmt(\PDOStatement $stmt)
    {
        return new PdoStmt($stmt, $this);
    }

    public function select($column = null, $_ = null)
    {
        $query = new SqliteSelectQuery();

        if ($columns = is_array($column) ? $column : func_get_args()) {
            $query->columns($columns);
        }

        return $query;
    }

    public function insert($into = null)
    {
        $query = new SqliteInsertQuery();

        if ($into !== null) {
            $query->into($into);
        }

        return $query;
    }

    public function delete($from = null)
    {
        $query = new SqliteDeleteQuery();

        if ($from !== null) {
            $query->from($from);
        }

        return $query;
    }

    public function update($table = null)
    {
        $query = new SqliteUpdateQuery();

        if ($table !== null) {
            $query->table($table);
        }

        return $query;
    }

    public function from()
    {
        return new FromQuery();
    }

    public function joins()
    {
        return new JoinsQuery();
    }

    public function where()
    {
        return new WhereQuery();
    }

    public function having()
    {
        return new HavingQuery();
    }

    static public function quoteLike($value, $escape = '\\')
    {
        return QueryTrait::quoteLike($value, $escape);
    }

    static public function concat(array $values, $delimiter = '')
    {
        return QueryTrait::concat($values, $delimiter);
    }
}