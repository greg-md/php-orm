<?php

namespace Greg\Orm;

use Greg\Orm\Storage\StorageInterface;

trait TableSchemaTrait
{
    protected function populateSchema($populateInfo = true, $populateReferences = true, $populateRelationships = false)
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
            //$references = $this->getStorage()->getTableReferences($this->getName());
        }

        if ($populateRelationships) {
            //$relationships = $this->getStorage()->getTableRelationships($this->getName());
        }

        return $this;
    }

    protected function bootTableSchemaTrait()
    {
        $this->populateSchema();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}