<?php

namespace Greg\Orm;

use Greg\Orm\Table\RowInterface;

class TableRelationshipOld
{
    protected $table = null;

    protected $onSelect = [];

    protected $relationshipData = [];

    public function __construct(Table $table, RowInterface $row = null)
    {
        $this->table($table);

        if ($row) {
            $this->row($row);
        }

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return Table
     */
    public function getTable()
    {
        if (!($table = $this->table())) {
            throw new \Exception('Please define a table for this table row.');
        }

        return $table;
    }

    public function getTableName()
    {
        return $this->getTable()->getName();
    }

    public function onSelect(callable $callable)
    {
        $this->onSelect[] = $callable;

        return $this;
    }

    public function find($keys = [])
    {
        $table = $this->getTable();

        if (!is_array($keys)) {
            $keys = $table->combineFirstUnique($keys);
        }

        return $table->find($this->getRelationshipData() + $keys);
    }

    public function insertMultiData(array $multiData)
    {
        foreach ($multiData as $data) {
            $this->insertData($data);
        }

        return $this;
    }

    public function insertData(array $data)
    {
        return $this->getTable()->insertData($this->getRelationshipData() + $data);
    }

    public function getRelationshipData()
    {
        $data = $this->relationshipData();

        if ($row = $this->row()) {
            $references = $this->getTable()->getTableReferences($row->getTableName());

            foreach ($references as $reference) {
                foreach ($reference['Constraint'] as $constraint) {
                    $data[$constraint['ColumnName']] = $row[$constraint['ReferencedColumnName']];
                }
            }
        }

        return $data;
    }

    /**
     * @param Table $value
     *
     * @return $this|Table
     */
    public function table(Table $value = null)
    {
        return Obj::fetchVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    /**
     * @param RowInterface $value
     *
     * @return $this|RowInterface
     */
    public function row(RowInterface $value = null)
    {
        return Obj::fetchVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function relationshipData($key = null, $value = null, $type = Obj::PROP_APPEND)
    {
        return Obj::fetchArrayReplaceVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }
}
