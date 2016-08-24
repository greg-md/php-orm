<?php

namespace Greg\Orm\Table;

use Greg\Orm\Table;
use Greg\Orm\TableRelationship;

class Row extends RowAbstract implements RowInterface
{
    protected $defaults = [];

    protected $isNew = false;

    protected $tableRelationships = [];

    public function __construct($tableName, $data = [])
    {
        parent::__construct($tableName, $data);

        $this->defaults($data);

        return $this;
    }

    public function update(array $data)
    {
        $this->mergeMe($data);

        $this->save();

        return $this;
    }

    public function save()
    {
        $table = $this->getTable();

        $data = $table->parseData($this->storage(), true, true);

        if ($this->isNew()) {
            $table->insert($data)->exec();

            $this->isNew(false);

            if ($column = $table->autoIncrement()) {
                $data[$column] = $table->lastInsertId();
            }
        } else {
            if ($data = array_diff_assoc($data, $this->defaults())) {
                $table->update($data)->whereCols($this->getFirstUnique())->exec();
            }
        }

        $this->defaults($data);

        $this->mergeMe($data);

        return $this;
    }

    public function autoIncrement()
    {
        return ($key = $this->getTable()->autoIncrement()) ? $this->defaults($key) : null;
    }

    public function primary()
    {
        $keys = [];

        foreach($this->getTable()->primary() as $key) {
            $keys[$key] = $this->defaults($key);
        }

        return $keys;
    }

    public function unique()
    {
        $keys = [];

        foreach($this->getTable()->unique() as $name => $info) {
            foreach($info['Keys'] as $key) {
                $keys[$name][$key['ColumnName']] = $this->defaults($key['ColumnName']);
            }
        }

        return $keys;
    }

    public function getFirstUnique()
    {
        if ($autoIncrement = $this->autoIncrement()) {
            $keys = [$this->getTable()->autoIncrement() => $autoIncrement];
        } else {
            $keys = $this->primary();

            if (!$keys) {
                $keys = current($this->unique());

                if (!$keys) {
                    $keys = $this->defaults();
                }
            }
        }

        return $keys;
    }

    public function delete()
    {
        $key = $this->getFirstUnique();

        $this->getTable()->delete($key)->exec();

        return $this;
    }

    public function tableRelationship($name)
    {
        if (!$relationship = $this->tableRelationships($name)) {
            $relationshipTable = $this->getTable()->getRelationshipTable($name);

            $relationship = $this->newTableRelationship($relationshipTable, $this);

            $this->tableRelationships($name, $relationship);
        }

        return $relationship;
    }

    protected function newTableRelationship(Table $table, Row $row)
    {
        return new TableRelationship($table, $row);
    }

    public function defaults($key = null, $value = null, $type = Obj::PROP_APPEND)
    {
        return Obj::fetchArrayReplaceVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function isNew($value = null)
    {
        return Obj::fetchBoolVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    protected function tableRelationships($key = null, $value = null, $type = Obj::PROP_APPEND)
    {
        return Obj::fetchArrayReplaceVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }
}