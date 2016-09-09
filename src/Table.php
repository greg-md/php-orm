<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverInterface;
use Greg\Support\Obj;

abstract class Table implements TableInterface
{
    use TableTrait, RowTrait;

    /**
     * @var DriverInterface|null
     */
    protected $storage = null;

    public function __construct(array $data = [], DriverInterface $storage = null)
    {
        if ($storage) {
            $this->setStorage($storage);
        }

        $this->boot();

        $this->bootTraits();

        if ($data) {
            $this->___appendRowData($data, true);
        }

        return $this;
    }

    protected function boot()
    {
        return $this;
    }

    protected function bootTraits()
    {
        foreach (Obj::usesRecursive(static::class, Table::class) as $trait) {
            if (method_exists($this, $method = 'boot' . Obj::baseName($trait))) {
                call_user_func_array([$this, $method], []);
            }
        }

        return $this;
    }

    protected function newInstance(array $data = [])
    {
        $class = get_called_class();

        return new $class($data, $this->getStorage());
    }

    public function setStorage(DriverInterface $storage)
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

    public function lastInsertId()
    {
        return $this->getStorage()->lastInsertId();
    }
}