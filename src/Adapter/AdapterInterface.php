<?php

namespace Greg\Orm\Adapter;

interface AdapterInterface
{
    public function getStmtClass();

    public function setStmtClass($className);


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


    public function listen(callable $callable);

    public function fire($sql);
}