<?php

namespace Greg\Orm\Storage\Mysql\Query;

interface MysqlSelectQueryInterface
{
    const FOR_UPDATE = 'FOR UPDATE';

    const LOCK_IN_SHARE_MODE = 'LOCK IN SHARE MODE';
}