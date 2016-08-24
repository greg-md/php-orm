<?php

namespace Greg\Orm;

class TableConstraint
{
    protected $name = null;

    protected $referencedTableName = null;

    protected $onUpdate = null;

    protected $onDelete = null;
    
    protected $relations = null;

    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setReferencedTableName($name)
    {
        $this->referencedTableName = (string)$name;

        return $this;
    }

    public function getReferencedTableName()
    {
        return $this->referencedTableName;
    }

    public function setOnUpdate($type)
    {
        $this->onUpdate = (string)$type;

        return $this;
    }

    public function getOnUpdate()
    {
        return $this->onUpdate;
    }

    public function setOnDelete($type)
    {
        $this->onDelete = (string)$type;

        return $this;
    }

    public function getOnDelete()
    {
        return $this->onDelete;
    }

    public function setRelation($position, $columnName, $referencedColumnName)
    {
        $this->relations[$position] = [
            'columnName' => $columnName,
            'referencedColumnName' => $referencedColumnName,
        ];

        return $this;
    }

    public function getRelation($position)
    {
        if (!array_key_exists($position, $this->relations)) {
            throw new \Exception('Constraint position `' . $position . '` does not exists for constraint `' . $this->getName() . '`.');
        }

        return $this->relations[$position];
    }

    public function getRelations()
    {
        return $this->relations;
    }
}