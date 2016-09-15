<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverInterface;

interface TableInterface extends TableTraitInterface, RowTraitInterface
{
    public function setDriver(DriverInterface $driver);

    /**
     * @return DriverInterface
     */
    public function getDriver();
}
