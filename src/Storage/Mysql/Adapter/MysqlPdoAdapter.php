<?php

namespace Greg\Orm\Storage\Mysql\Adapter;

use Greg\Orm\Adapter\PdoAdapter;

class MysqlPdoAdapter extends PdoAdapter implements MysqlAdapterInterface
{
    protected $dsnParams = [];

    public function __construct($dsn, $username = null, $password = null, array $options = [])
    {
        $args = func_get_args();

        if (is_array($dsn)) {
            foreach($dsn as $key => &$value) {
                $value = $key . '=' . $value;
            }
            unset($value);

            $dsn = implode(';', $dsn);
        }

        $args[0] = 'mysql:' . $dsn;

        parent::__construct(...$args);

        return $this;
    }

    protected function parseDnsParams($dsn)
    {
        parent::parseDnsParams($dsn);

        $this->dsnParams = [];

        foreach(explode(';', $this->dsnInfo) as $info) {
            list($key, $value) = explode('=', $info, 2);

            $this->dsnParams[$key] = $value;
        }

        return $this;
    }

    public function dbName()
    {
        return $this->dsnParams['dbname'];
    }
}