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

        $this->loadSchema();

        return $this;
    }

    public function loadSchema()
    {
        return $this;
    }

    public function populateSchema($populateInfo = true, $populateReferences = true, $populateRelationships = false)
    {
        if ($populateInfo) {
            $info = $this->getStorage()->getTableInfo($this->fullName());

            foreach($info['columns'] as $column) {
                $this->addColumn($column);
            }

            $info['primaryKeys'] && $this->setPrimaryKeys($info['primaryKeys']);

            $info['autoIncrement'] && $this->setAutoIncrement($info['autoIncrement']);
        }

        if ($populateReferences) {
            $references = $this->getStorage()->getTableReferences($this->getName());
        }

        if ($populateRelationships) {
            $relationships = $this->getStorage()->getTableRelationships($this->getName());
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

    public function lastInsertId()
    {
        return $this->getStorage()->lastInsertId();
    }
}