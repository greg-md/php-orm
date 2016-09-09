<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Query\FromClause;
use Greg\Orm\Query\HavingClause;
use Greg\Orm\Query\JoinClause;
use Greg\Orm\Query\LimitClause;
use Greg\Orm\Query\OrderByClause;
use Greg\Orm\Query\QuerySupport;
use Greg\Orm\Query\WhereClause;
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
        return new FromClause();
    }

    public function join()
    {
        return new JoinClause();
    }

    public function where()
    {
        return new WhereClause();
    }

    public function having()
    {
        return new HavingClause();
    }

    public function orderBy()
    {
        return new OrderByClause();
    }

    public function limit()
    {
        return new LimitClause();
    }

    static public function quoteLike($value, $escape = '\\')
    {
        return QuerySupport::quoteLike($value, $escape);
    }

    static public function concat(array $values, $delimiter = '')
    {
        return QuerySupport::concat($values, $delimiter);
    }
}