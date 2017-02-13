<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverStrategy;

interface TableInterface extends TableTraitInterface, RowTraitInterface
{
    public function setDriver(DriverStrategy $driver);

    /**
     * @return DriverStrategy
     */
    public function getDriver();
}
