<?php

namespace Greg\Orm;

use Greg\Orm\Storage\StorageInterface;

interface TableInterface extends TableTraitInterface, RowTraitInterface
{
    public function setStorage(StorageInterface $storage);

    /**
     * @return StorageInterface
     */
    public function getStorage();
}
