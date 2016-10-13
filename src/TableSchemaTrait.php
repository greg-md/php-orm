<?php

namespace Greg\Orm;

use Greg\Orm\Driver\MysqlInterface;

trait TableSchemaTrait
{
    protected function populateWithInfo(array $info)
    {
        foreach ($info['columns'] as $column) {
            $this->addColumn($column);
        }

        $info['primaryKeys'] && $this->setPrimaryKeys($info['primaryKeys']);

        $info['autoIncrement'] && $this->setAutoIncrement($info['autoIncrement']);

        return $this;
    }

    protected function populateInfo()
    {
        $info = $this->getDriver()->tableInfo($this->fullName());

        return $this->populateWithInfo($info);
    }

    protected function populateReferences()
    {
        //$references = $this->getDriver()->getTableReferences($this->getName());
    }

    protected function populateRelationships()
    {
        //$relationships = $this->getDriver()->getTableRelationships($this->getName());
    }

    protected function bootTableSchemaTrait()
    {
        $this->populateInfo();
    }

    /**
     * @return MysqlInterface
     */
    abstract public function getDriver();
}
