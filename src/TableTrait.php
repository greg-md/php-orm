<?php

namespace Greg\Orm;

use Greg\Orm\Query\ExprQuery;
use Greg\Orm\Storage\StorageInterface;
use Greg\Support\Arr;

trait TableTrait
{
    protected $prefix = null;

    protected $name = null;

    protected $alias = null;

    protected $columns = [];

    protected $customColumnsTypes = [];

    protected $autoIncrement = null;

    protected $primaryKeys = [];

    protected $uniqueKeys = [];

    protected $references = [];

    protected $relationships = [];

    protected $dependencies = [];

    protected $relationshipsAliases = [];

    protected $referencesAliases = [];

    protected $nameColumn = null;

    protected $query = null;

    /**
     * @var StorageInterface|null
     */
    protected $storage = null;

    public function setAutoIncrement($columnName)
    {
        $this->autoIncrement = (string)$columnName;

        return $this;
    }

    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    public function setPrimaryKeys($columnsNames)
    {
        $this->primaryKeys = (array)$columnsNames;

        return $this;
    }

    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    public function addUniqueKeys(array $keys)
    {
        $this->uniqueKeys[] = $keys;
    }

    public function getUniqueKeys()
    {
        return $this->uniqueKeys;
    }

    public function getFirstUniqueKeys()
    {
        return Arr::firstRef($this->uniqueKeys);
    }

    public function firstUniqueKeys()
    {
        if ($autoIncrement = $this->getAutoIncrement()) {
            return [$autoIncrement];
        }

        if ($primaryKeys = $this->getPrimaryKeys()) {
            return $primaryKeys;
        }

        if ($uniqueKeys = $this->getFirstUniqueKeys()) {
            return $uniqueKeys;
        }

        return array_keys($this->getColumns());
    }

    public function combineFirstUniqueKeys($values)
    {
        $values = (array)$values;

        if (!$keys = $this->firstUniqueKeys()) {
            throw new \Exception('Table does not have primary keys.');
        }

        if (sizeof($keys) !== sizeof($values)) {
            throw new \Exception('Unique columns count should be the same as keys count.');
        }

        return array_combine($keys, $values);
    }

    public function selectQuery($columns = null, $_ = null)
    {
        if (!is_array($columns)) {
            $columns = func_get_args();
        }

        $query = $this->getStorage()->select($columns);

        $query->from($this);

        return $query;
    }

    public function select($columns = null, $_ = null)
    {
        $this->query = $this->selectQuery(...func_get_args());

        return $this;
    }

    public function updateQuery(array $values = [])
    {
        $query = $this->getStorage()->update($this);

        if ($values) {
            $query->set($values);
        }

        return $query;
    }

    public function update(array $values = [])
    {
        $this->query = $this->updateQuery(...func_get_args());

        return $this;
    }

    public function deleteQuery(array $whereIs = [])
    {
        $query = $this->getStorage()->delete($this, true);

        if ($whereIs) {
            $query->whereCols($whereIs);
        }

        return $query;
    }

    public function delete(array $whereIs = [])
    {
        $this->query = $this->deleteQuery(...func_get_args());

        return $this;
    }

    public function insertQuery(array $data = [])
    {
        $query = $this->getStorage()->insert($this);

        $query->data($data);

        return $query;
    }

    public function insert(array $data = [])
    {
        $this->query = $this->insertQuery(...func_get_args());

        return $this;
    }

    public function insertData(array $data = [])
    {
        $this->insertQuery($data)->exec();

        return $this;
    }

    public function pairs(array $whereIs = [], callable $callable = null)
    {
        if (!$columnName = $this->getNameColumn()) {
            throw new \Exception('Undefined column name for table `' . $this->getName() . '`.');
        }

        $query = $this->selectQuery();

        $query->columns($query->concat($this->getFirstUniqueKeys(), ':'), $columnName);

        if ($whereIs) {
            $query->whereCols($whereIs);
        }

        if ($callable) {
            $callable($query);
        }

        return $query->pairs();
    }

    public function exists($column, $value)
    {
        return $this->selectQuery(new ExprQuery(1))->whereCol($column, $value)->exists();
    }

    public function fullName()
    {
        return $this->getPrefix() . $this->getName();
    }

    public function setPrefix($name)
    {
        $this->prefix = (string)$name;

        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }

    public function getName()
    {
        if (!$this->name) {
            throw new \Exception('Table name is not defined.');
        }

        return $this->name;
    }

    public function setAlias($name)
    {
        $this->alias = (string)$name;

        return $this;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function addColumn(Column $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function setCustomColumnType($key, $value)
    {
        $this->customColumnsTypes[$key] = (string)$value;

        return $this;
    }

    public function getCustomColumnType($key)
    {
        return Arr::getRef($this->customColumnsTypes, $key);
    }

    public function getCustomColumnTypes()
    {
        return $this->customColumnsTypes;
    }

    public function addReference(TableConstraint $constraint)
    {
        $this->references[] = $constraint;
    }

    public function getReferences()
    {
        return $this->references;
    }

    public function addRelationship(TableConstraint $constraint)
    {
        $this->relationships[] = $constraint;
    }

    public function getRelationships()
    {
        return $this->relationships;
    }

    public function setDependence($name, $tableName, array $filter = [])
    {
        $this->dependencies[$name] = [
            'tableName' => $tableName,
            'filter' => $filter,
        ];

        return $this;
    }

    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function setRelationshipAlias($key, $value)
    {
        $this->relationshipsAliases[$key] = (string)$value;

        return $this;
    }

    public function getRelationshipAlias($key)
    {
        return Arr::getRef($this->relationshipsAliases, $key);
    }

    public function getRelationshipsAliases()
    {
        return $this->relationshipsAliases;
    }

    public function setReferenceAlias($key, $value)
    {
        $this->referencesAliases[$key] = (string)$value;

        return $this;
    }

    public function getReferenceAlias($key)
    {
        return Arr::getRef($this->referencesAliases, $key);
    }

    public function getReferencesAliases()
    {
        return $this->referencesAliases;
    }

    public function setNameColumn($name)
    {
        $this->nameColumn = (string)$name;

        return $this;
    }

    public function getNameColumn()
    {
        return $this->nameColumn;
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
}