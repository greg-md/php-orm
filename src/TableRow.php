<?php

namespace Greg\Orm;

use Greg\Orm\Storage\StorageInterface;

abstract class TableRow implements TableInterface
{
    use TableTrait;

    public function __construct(StorageInterface $storage = null)
    {
        if ($storage) {
            $this->setStorage($storage);
        }

        return $this;
    }
}