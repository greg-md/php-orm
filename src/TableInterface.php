<?php

namespace Greg\Orm;

use Greg\Orm\Storage\StorageInterface;

interface TableInterface
{
    public function getName();

    public function getAlias();

    /**
     * @return StorageInterface
     */
    public function getStorage();
}
