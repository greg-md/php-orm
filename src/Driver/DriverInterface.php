<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\FromClauseInterface;
use Greg\Orm\Query\HavingClauseInterface;
use Greg\Orm\Query\InsertQueryInterface;
use Greg\Orm\Query\JoinClauseInterface;
use Greg\Orm\Query\LimitClauseInterface;
use Greg\Orm\Query\OrderByClauseInterface;
use Greg\Orm\Query\SelectQueryInterface;
use Greg\Orm\Query\UpdateQueryInterface;
use Greg\Orm\Query\WhereClauseInterface;

interface DriverInterface
{
    public function connector();

    public function reconnect();


    public function transaction(callable $callable);

    public function inTransaction();

    public function beginTransaction();

    public function commit();

    public function rollBack();


    /**
     * @param $sql
     * @return StmtInterface
     */
    public function prepare($sql);

    public function query($sql);

    public function exec($sql);

    public function lastInsertId($sequenceId = null);

    public function quote($value);

    public function truncate($tableName);


    public function listen(callable $callable);

    public function fire($sql);


    /**
     * @param null $column
     * @param null $_
     * @return SelectQueryInterface
     * @throws \Exception
     */
    public function select($column = null, $_ = null);

    /**
     * @param null $into
     * @return InsertQueryInterface
     * @throws \Exception
     */
    public function insert($into = null);

    /**
     * @param null $from
     * @return DeleteQueryInterface
     * @throws \Exception
     */
    public function delete($from = null);

    /**
     * @param null $table
     * @return UpdateQueryInterface
     * @throws \Exception
     */
    public function update($table = null);

    /**
     * @return FromClauseInterface
     * @throws \Exception
     */
    public function from();

    /**
     * @return JoinClauseInterface
     * @throws \Exception
     */
    public function join();

    /**
     * @return WhereClauseInterface
     * @throws \Exception
     */
    public function where();

    /**
     * @return HavingClauseInterface
     * @throws \Exception
     */
    public function having();

    /**
     * @return OrderByClauseInterface
     * @throws \Exception
     */
    public function orderBy();

    /**
     * @return LimitClauseInterface
     * @throws \Exception
     */
    public function limit();


    static public function quoteLike($value, $escape = '\\');

    static public function concat(array $values, $delimiter = '');
}