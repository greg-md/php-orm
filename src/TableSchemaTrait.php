<?php

namespace Greg\Orm;

use Greg\Orm\Driver\MysqlInterface;

trait TableSchemaTrait
{
    protected function populateSchema($populateInfo = true, $populateReferences = true, $populateRelationships = false)
    {
        if ($populateInfo) {
            $info = $this->getDriver()->tableInfo($this->fullName());

            foreach ($info['columns'] as $column) {
                $this->addColumn($column);
            }

            $info['primaryKeys'] && $this->setPrimaryKeys($info['primaryKeys']);

            $info['autoIncrement'] && $this->setAutoIncrement($info['autoIncrement']);
        }

        if ($populateReferences) {
            //$references = $this->getDriver()->getTableReferences($this->getName());
        }

        if ($populateRelationships) {
            //$relationships = $this->getDriver()->getTableRelationships($this->getName());
        }

        return $this;
    }

    protected function bootTableSchemaTrait()
    {
        $this->populateSchema();
    }

    /**
     * @return MysqlInterface
     */
    abstract public function getDriver();
}
