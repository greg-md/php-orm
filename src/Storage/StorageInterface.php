<?php

namespace Greg\Orm\Storage;

use Greg\Orm\Adapter\AdapterInterface;
use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;

interface StorageInterface
{
    /**
     * @param null $column
     * @param null $_
     * @return SelectQuery
     * @throws \Exception
     */
    public function select($column = null, $_ = null);

    /**
     * @param null $into
     * @return InsertQuery
     * @throws \Exception
     */
    public function insert($into = null);

    /**
     * @param null $from
     * @return DeleteQuery
     * @throws \Exception
     */
    public function delete($from = null);

    /**
     * @param null $table
     * @return UpdateQuery
     * @throws \Exception
     */
    public function update($table = null);

    static public function quoteLike($string, $escape = '\\');

    static public function concat($array, $delimiter = '');

    public function getTableSchema($tableName);

    public function getTableInfo($tableName);

    public function getTableReferences($tableName);

    public function getTableRelationships($tableName);

    public function beginTransaction();

    public function commit();

    public function errorCode();

    public function errorInfo();

    public function exec($query);

    public function getAttribute($name);

    public function inTransaction();

    public function lastInsertId($name = null);

    /**
     * @param $query
     * @return StmtInterface
     */
    public function prepare($query);

    /**
     * @param $query
     * @param null $mode
     * @param null $_
     * @return StmtInterface
     */
    public function query($query, $mode = null, $_ = null);

    public function quote($string, $type = AdapterInterface::PARAM_STR);

    public function rollBack();

    public function setAttribute($name, $value);

    public function transaction(callable $callable);

    public function truncate($name);

    public function listen(callable $callable);

    public function fire($sql);
}