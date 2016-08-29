<?php

namespace Greg\Orm;

use Greg\Orm\Storage\StorageInterface;

abstract class Table implements TableInterface
{
    use TableTrait, RowTrait;

    /**
     * @var StorageInterface|null
     */
    protected $storage = null;

    public function __construct(StorageInterface $storage = null)
    {
        if ($storage) {
            $this->setStorage($storage);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function newInstance()
    {
        $class = get_called_class();

        return new $class($this->getStorage());
    }

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    public function getStorage()
    {
        if (!$this->storage) {
            throw new \Exception('Table storage is not defined.');
        }

        return $this->storage;
    }
}