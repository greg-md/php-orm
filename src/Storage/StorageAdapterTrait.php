<?php

namespace Greg\Orm\Storage;

use Greg\Orm\Adapter\AdapterInterface;

trait StorageAdapterTrait
{
    /**
     * @var AdapterInterface|callable|null
     */
    protected $adapter = null;

    public function __construct($adapter = null)
    {
        if ($adapter) {
            if ($adapter instanceof AdapterInterface) {
                $this->setAdapter($adapter);
            } elseif (is_callable($adapter)) {
                $this->setCallableAdapter($adapter);
            } else {
                throw new \Exception('Wrong Mysql adapter type.');
            }
        }
    }

    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function setCallableAdapter(callable $callable)
    {
        $this->adapter = $callable;

        return $this;
    }

    public function getAdapter()
    {
        if (is_callable($this->adapter)) {
            $this->adapter = call_user_func_array($this->adapter, []);
        }

        if (!$this->adapter) {
            throw new \Exception('Undefined storage adapter.');
        }

        return $this->adapter;
    }
}