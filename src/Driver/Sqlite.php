<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Query\FromClause;
use Greg\Orm\Query\GroupByClause;
use Greg\Orm\Query\HavingClause;
use Greg\Orm\Query\JoinClause;
use Greg\Orm\Query\LimitClause;
use Greg\Orm\Query\OrderByClause;
use Greg\Orm\Query\QuerySupport;
use Greg\Orm\Query\WhereClause;
use Greg\Orm\Driver\Sqlite\Query\SqliteDeleteQuery;
use Greg\Orm\Driver\Sqlite\Query\SqliteInsertQuery;
use Greg\Orm\Driver\Sqlite\Query\SqliteSelectQuery;
use Greg\Orm\Driver\Sqlite\Query\SqliteUpdateQuery;

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

    public function select()
    {
        return new SqliteSelectQuery();
    }

    public function insert()
    {
        return new SqliteInsertQuery();
    }

    public function delete()
    {
        return new SqliteDeleteQuery();
    }

    public function update()
    {
        return new SqliteUpdateQuery();
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

    public function groupBy()
    {
        return new GroupByClause();
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