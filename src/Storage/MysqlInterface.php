<?php

namespace Greg\Orm\Storage;

interface MysqlInterface extends StorageInterface
{
    public function dbName();


    public function tableInfo($tableName, $save = true);

    public function tableReferences($tableName);

    public function tableRelationships($tableName, $withRules = false);
}