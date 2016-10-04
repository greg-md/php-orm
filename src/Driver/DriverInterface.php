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
     *
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
     * @throws \Exception
     *
     * @return SelectQueryInterface
     */
    public function select();

    /**
     * @throws \Exception
     *
     * @return InsertQueryInterface
     */
    public function insert();

    /**
     * @throws \Exception
     *
     * @return DeleteQueryInterface
     */
    public function delete();

    /**
     * @throws \Exception
     *
     * @return UpdateQueryInterface
     */
    public function update();

    /**
     * @throws \Exception
     *
     * @return FromClauseInterface
     */
    public function from();

    /**
     * @throws \Exception
     *
     * @return JoinClauseInterface
     */
    public function join();

    /**
     * @throws \Exception
     *
     * @return WhereClauseInterface
     */
    public function where();

    /**
     * @throws \Exception
     *
     * @return HavingClauseInterface
     */
    public function having();

    /**
     * @throws \Exception
     *
     * @return OrderByClauseInterface
     */
    public function orderBy();

    /**
     * @throws \Exception
     *
     * @return OrderByClauseInterface
     */
    public function groupBy();

    /**
     * @throws \Exception
     *
     * @return LimitClauseInterface
     */
    public function limit();

    public static function quoteLike($value, $escape = '\\');

    public static function concat(array $values, $delimiter = '');
}
