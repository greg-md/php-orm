<?php

namespace Greg\Orm\Driver\Mysql\Query;

interface MysqlSelectQueryInterface
{
    public function forUpdate();

    public function lockInShareMode();
}