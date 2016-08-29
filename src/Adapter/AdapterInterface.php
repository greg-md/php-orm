<?php

namespace Greg\Orm\Adapter;

interface AdapterInterface
{
    const PARAM_BOOL = 5;

    const PARAM_NULL = 0;

    const PARAM_INT = 1;

    const PARAM_STR = 2;

    const PARAM_LOB = 3;

    const PARAM_STMT = 4;

    const FETCH_ORI_NEXT = 0;

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
     * @param array $options
     * @return StmtInterface
     */
    public function prepare($query, $options = null);

    public function query();

    public function quote($string, $type = AdapterInterface::PARAM_STR);

    public function rollBack();

    public function setAttribute($name, $value);

    public function transaction(callable $callable);

    public function truncate($name);

    public function listen(callable $callable);

    public function fire($sql);
}