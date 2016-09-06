<?php

namespace Greg\Orm\Storage\Sqlite\Adapter;

use Greg\Orm\Adapter\PdoAdapter;

class SqlitePdoAdapter extends PdoAdapter implements SqliteAdapterInterface
{
    public function __construct($path)
    {
        parent::__construct('sqlite:' . $path);

        return $this;
    }
}