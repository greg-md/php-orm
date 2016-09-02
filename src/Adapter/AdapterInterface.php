<?php

namespace Greg\Orm\Adapter;

use Greg\Orm\Storage\StorageInterface;

interface AdapterInterface
{
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

    public function query($query, $mode = null, $_ = null);

    public function quote($string, $type = StorageInterface::PARAM_STR);

    public function rollBack();

    public function setAttribute($name, $value);

    public function transaction(callable $callable);

    public function truncate($name);

    public function listen(callable $callable);

    public function fire($sql);
}