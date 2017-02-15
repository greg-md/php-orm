<?php

namespace Greg\Orm\Driver;

interface PdoConnectorStrategy
{
    public function connect(): \PDO;
}
