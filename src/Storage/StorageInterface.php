<?php

namespace Greg\Orm\Storage;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\InsertQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\SelectQueryInterface;
use Greg\Orm\Query\UpdateQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;

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
     * @return WhereQueryInterface
     * @throws \Exception
     */
    public function where();

    /**
     * @return JoinsQueryInterface
     * @throws \Exception
     */
    public function joins();

    static public function quoteLike($string, $escape = '\\');

    static public function concat($array, $delimiter = '');

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

    public function quote($string, $type = self::PARAM_STR);

    public function rollBack();

    public function setAttribute($name, $value);

    public function transaction(callable $callable);

    public function truncate($name);

    public function listen(callable $callable);

    public function fire($sql);
}