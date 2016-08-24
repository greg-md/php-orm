<?php

namespace Greg\Orm\Table;

/**
 * Class RowFull
 * @package Greg\Orm\Table
 *
 * @method $this update(array $data);
 * @method $this save()
 * @method int autoIncrement()
 * @method array primary()
 * @method array unique()
 * @method array getFirstUnique()
 * @method $this delete()
 * @method \Greg\Orm\TableRelationship tableRelationship($name)
 * @method bool|$this isNew($value = null)
 */
class RowFull extends RowAbstract
{
    /**
     * @return Row
     * @throws \Exception
     */
    public function getRow()
    {
        return parent::offsetGet($this->getTableName());
    }

    /**
     * @param $name
     * @return RowFull|null
     */
    public function getDependency($name)
    {
        return parent::offsetGet($name);
    }

    /**
     * @param $name
     * @return Rows
     */
    public function getRelationship($name)
    {
        return parent::offsetGet('relationships')[$name];
    }

    /**
     * @param $name
     * @return Rows
     */
    public function getReference($name)
    {
        return parent::offsetGet('references')[$name];
    }

    public function offsetExists($index)
    {
        if (!is_array($index) and !parent::offsetExists($index)) {
            if ($this->getRow()->offsetExists($index)) {
                return true;
            }

            foreach($this->getTable()->dependencies() as $name => $info) {
                if ($dependencyRow = $this->getDependency($name) and $dependencyRow->offsetExists($index)) {
                    return true;
                }
            }

            return false;
        }

        return parent::offsetExists($index);
    }

    public function &offsetGet($index)
    {
        if (!is_array($index) and !parent::offsetExists($index)) {
            $row = $this->getRow();

            if ($row->offsetExists($index)) {
                $ref = &$row[$index];

                return $ref;
            }

            foreach($this->getTable()->dependencies() as $name => $info) {
                if ($dependencyRow = $this->getDependency($name) and $dependencyRow->offsetExists($index)) {
                    $ref = &$dependencyRow[$index];

                    return $ref;
                }
            }
        }

        return parent::offsetGet($index);
    }

    public function offsetSet($key, $value)
    {
        if (!parent::offsetExists($key)) {
            $row = $this->getRow();

            if ($row->offsetExists($key)) {
                $row[$key] = $value;

                return $this;
            }

            foreach($this->getTable()->dependencies() as $name => $info) {
                if ($dependencyRow = $this->getDependency($name) and $dependencyRow->offsetExists($key)) {
                    $dependencyRow[$key] = $value;

                    return $this;
                }
            }
        }

        return parent::offsetSet($key, $value);
    }

    public function toArray($recursive = true)
    {
        /* @var $array Row[] */
        $array = parent::toArray();

        if ($recursive) {
            $array[$this->getTableName()] = $array[$this->getTableName()]->toArray();

            foreach($this->getTable()->dependencies() as $name => $info) {
                if ($dependencyRow = $this->getDependency($name)) {
                    $array[$name] = $dependencyRow->toArray();

                    return $this;
                }
            }
        }

        return $array;
    }

    public function toArrayObject($recursive = true)
    {
        /* @var $array Row[] */
        $array = parent::toArrayObject();

        if ($recursive) {
            $array[$this->getTableName()] = $array[$this->getTableName()]->toArrayObject();

            foreach($this->getTable()->dependencies() as $name => $info) {
                if ($dependencyRow = $this->getDependency($name)) {
                    $array[$name] = $dependencyRow->toArrayObject();

                    return $this;
                }
            }
        }

        return $array;
    }

    public function hasMethod($method)
    {
        $row = $this->getRow();

        if (method_exists($row, $method)) {
            return true;
        }

        foreach($this->getTable()->dependencies() as $name => $info) {
            if ($dependencyRow = $this->getDependency($name) and $dependencyRow->hasMethod($method)) {
                return true;
            }
        }

        return method_exists($this, $method);
    }

    public function __call($method, $args)
    {
        $row = $this->getRow();

        if (method_exists($row, $method)) {
            return call_user_func_array(array($row, $method), $args);
        }

        foreach($this->getTable()->dependencies() as $name => $info) {
            if ($dependencyRow = $this->getDependency($name) and $dependencyRow->hasMethod($method)) {
                return call_user_func_array(array($dependencyRow, $method), $args);
            }
        }

        return null;
    }
}