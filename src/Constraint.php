<?php

namespace Greg\Orm;

class Constraint
{
    protected $name = null;

    protected $referencedTableName = null;

    protected $onUpdate = null;

    protected $onDelete = null;
    
    protected $relations = null;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }

    public function getReferencedTableName()
    {
        return $this->referencedTableName;
    }

    public function setReferencedTableName($name)
    {
        $this->referencedTableName = (string)$name;

        return $this;
    }

    public function onUpdate($type = null)
    {
        if (func_num_args()) {
            $this->onUpdate = (string)$type;

            return $this;
        }

        return $this->onUpdate;
    }

    public function onDelete($type = null)
    {
        if (func_num_args()) {
            $this->onDelete = (string)$type;

            return $this;
        }

        return $this->onDelete;
    }

    public function getRelations()
    {
        return $this->relations;
    }

    public function getRelation($position)
    {
        if (!array_key_exists($position, $this->relations)) {
            throw new \Exception('Constraint position `' . $position . '` does not exists for constraint `' . $this->getName() . '`.');
        }

        return $this->relations[$position];
    }

    public function setRelation($position, $columnName, $referencedColumnName)
    {
        $this->relations[$position] = [
            'columnName' => $columnName,
            'referencedColumnName' => $referencedColumnName,
        ];

        return $this;
    }
}