<?php

namespace Greg\Orm\Driver;

interface PdoConnectorStrategy
{
    /**
     * @return \PDO
     */
    public function connect(): \PDO;
}
