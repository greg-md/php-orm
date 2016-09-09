<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverInterface;

interface TableInterface extends TableTraitInterface, RowTraitInterface
{
    public function setStorage(DriverInterface $storage);

    /**
     * @return DriverInterface
     */
    public function getStorage();
}
