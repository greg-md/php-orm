<?php

namespace Greg\Orm\Storage\Mysql\Adapter;

use Greg\Orm\Adapter\PdoAdapter;

class MysqlPdoAdapter extends PdoAdapter
{
    public function __construct($dsn, $username = null, $password = null, $options = [])
    {
        if (is_array($dsn)) {
            foreach($dsn as $key => &$value) {
                $value = $key . '=' . $value;
            }
            unset($value);

            $dsn = implode(';', $dsn);
        }

        parent::__construct('mysql:' . $dsn, $username, $password, $options);

        return $this;
    }
}