<?php

namespace Greg\Orm\Storage;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;

interface StorageInterface
{
    const PARAM_BOOL = 5;

    const PARAM_NULL = 0;

    const PARAM_INT = 1;

    const PARAM_STR = 2;

    const PARAM_LOB = 3;

    const PARAM_STMT = 4;

    const FETCH_ORI_NEXT = 0;

    /**
     * @param null $columns
     * @param null $_
     * @return SelectQuery
     * @throws \Exception
     */
    public function select($columns = null, $_ = null);

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

    public function query($query, $mode = null, $_ = null);

    public function quote($string, $type = StorageInterface::PARAM_STR);

    public function rollBack();

    public function setAttribute($name, $value);
}