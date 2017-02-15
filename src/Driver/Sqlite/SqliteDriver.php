<?php

namespace Greg\Orm\Driver\Sqlite;

use Greg\Orm\DialectStrategy;
use Greg\Orm\Driver\PdoDriverAbstract;

class SqliteDriver extends PdoDriverAbstract
{
    /**
     * @var DialectStrategy|null
     */
    private $dialect;

    public function dialect(): DialectStrategy
    {
        if (!$this->dialect) {
            $this->dialect = new SqliteDialect();
        }

        return $this->dialect;
    }

    public function truncate(string $tableName)
    {
        return $this->exec('TRUNCATE ' . $tableName);
    }
}
