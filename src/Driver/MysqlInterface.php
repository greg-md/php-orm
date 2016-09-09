<?php

namespace Greg\Orm\Driver;

interface MysqlInterface extends DriverInterface
{
    public function dsn($name = null);

    public function dbName();

    public function charset();


    public function tableInfo($tableName, $save = true);

    public function tableReferences($tableName);

    public function tableRelationships($tableName, $withRules = false);
}