<?php

namespace Greg\Orm\Query;

interface UpdateQueryInterface extends QueryTraitInterface, WhereQueryTraitInterface
{
    public function table($table, $_ = null);

    public function set($key, $value = null);

    public function setRaw($raw, $param = null, $_ = null);

    public function increment($column, $value = 1);

    public function decrement($column, $value = 1);

    public function exec();

    public function updateStmtToSql();

    public function updateStmtToString();

    public function setStmtToSql();

    public function setStmtToString();

    public function updateToSql();

    public function updateToString();
}
