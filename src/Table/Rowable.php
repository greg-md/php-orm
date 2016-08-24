<?php

namespace Greg\Orm\Table;

use Greg\Orm\Table;
use Greg\Orm\TableRelationship;
use Greg\Engine\InternalTrait;
use Greg\Support\Arr;
use Greg\Support\Debug;

class Rowable implements RowInterface, \ArrayAccess, \IteratorAggregate/*, \Serializable*/, \Countable
{
    use InternalTrait;

    protected $rows = [];

    protected $defaults = [];

    protected $tableRelationships = [];

    protected $table = null;

    protected $total = 0;

    protected $page = 0;

    protected $limit = 0;

    public function __construct(array $rows = [], Table $table = null)
    {
        if ($table) {
            $this->table($table);
        }

        $this->exchange($rows);

        return $this;
    }

    public function exchange(array $rows, array $defaults = [])
    {
        return $this->exchangeRef($rows, $defaults);
    }

    public function exchangeRef(array &$rows, array &$defaults = [])
    {
        $this->rows = &$rows;

        if ($defaults) {
            $this->defaults = &$defaults;
        } else {
            $this->defaults = $rows;
        }

        return $this;
    }

    public function append(array $row, array $default = [])
    {
        return $this->appendRef($row, $default);
    }

    public function appendRef(array &$row, array &$default = [])
    {
        $this->rows[] = &$row;

        if ($default) {
            $this->defaults[] = &$default;
        } else {
            $this->defaults[] = $row;
        }

        return $this;
    }

    public function appendRow(array $row, array $default = [], $isNew = false)
    {
        return $this->appendRowRef($row, $default, $isNew);
    }

    public function appendRowRef(array &$row, array &$default = [], $isNew = false)
    {
        $rowFull = [
            'row' => &$row,
            'isNew' => $isNew,
            'dependencies' => [],
            'references' => [],
            'relationships' => [],
        ];

        $defaultFull = [
            'isNew' => $isNew,
            'dependencies' => [],
            'references' => [],
            'relationships' => [],
        ];

        if ($default) {
            $defaultFull['row'] = &$default;
        } else {
            $defaultFull['row'] = $rowFull;
        }

        $this->rows[] = $rowFull;

        $this->defaults[] = $defaultFull;

        return $this;
    }

    public function getTableRelationship($name)
    {
        if (!$relationship = $this->tableRelationships($name)) {
            $relationshipTable = $this->getTable()->getRelationshipTable($name);

            $relationship = $this->newTableRelationship($relationshipTable, $this);

            $this->tableRelationships($name, $relationship);
        }

        return $relationship;
    }

    protected function getRelationshipTable($name)
    {
        return $this->getTable()->getRelationshipTable($name);
    }

    protected function fetchRelationship($name)
    {
        $row = &$this->firstAssoc();

        $relationships = &$row['relationships'];

        if (!Arr::has($relationships, $name)) {
            $rows = [&$row];

            $this->getTable()->addRowableRelationship($rows, $name);

            if (!Arr::has($relationships, $name)) {
                throw new \Exception('Relationship `' . $name . '` not found for table `' . $this->getTableName() . '`.');
            }

            $this->firstAssocDefault()['relationships'][$name] = $relationships[$name];
        }

        return $this;
    }

    protected function &getRelationshipAssoc($name)
    {
        $this->fetchRelationship($name);

        $relationships = &$this->firstAssoc('relationships');

        $relationships = (array)$relationships;

        return Arr::getArrayRef($relationships, $name);
    }

    protected function &getRelationshipAssocDefault($name)
    {
        $this->fetchRelationship($name);

        $relationships = &$this->firstAssocDefault('relationships');

        $relationships = (array)$relationships;

        return Arr::getArrayRef($relationships, $name);
    }

    public function getRelationship($name)
    {
        $rows = &$this->getRelationshipAssoc($name);

        $rowsDefault = &$this->getRelationshipAssocDefault($name);

        return $this->getRelationshipTable($name)->createRowable([], false)->exchangeRef($rows, $rowsDefault);
    }

    protected function getReferenceTable($name)
    {
        return $this->getTable()->getReferenceTable($name);
    }

    protected function fetchReference($name)
    {
        $row = &$this->firstAssoc();

        $references = &$row['references'];

        if (!Arr::has($references, $name)) {
            $rows = [&$row];

            $referenceTable = $this->getReferenceTable($name);

            $this->getTable()->addRowableReference($rows, $referenceTable->getName());

            $this->firstAssocDefault()['references'][$name] = $references[$name];
        }

        return $this;
    }

    protected function &getReferenceAssoc($name)
    {
        $this->fetchReference($name);

        $references = &$this->firstAssoc('references');

        $references = (array)$references;

        return Arr::getArrayRef($references, $name);
    }

    protected function &getReferenceAssocDefault($name)
    {
        $this->fetchReference($name);

        $references = &$this->firstAssocDefault('references');

        $references = (array)$references;

        return Arr::getArrayRef($references, $name);
    }

    public function hasReference($name)
    {
        return Arr::has($this->firstAssoc('references'), $name);
    }

    /**
     * @param $name
     * @return Rowable|null
     * @throws \Exception
     */
    public function getReference($name)
    {
        $row = &$this->getReferenceAssoc($name);

        if (!$row) {
            return null;
        }

        $rowDefault = &$this->getReferenceAssocDefault($name);

        return $this->getReferenceTable($name)->createRowable([], false)->appendRef($row, $rowDefault);
    }

    protected function getDependenceTable($name)
    {
        return $this->getTable()->getDependenceTable($name);
    }

    protected function fetchDependence($name)
    {
        $row = &$this->firstAssoc();

        $dependencies = &$row['dependencies'];

        if (!Arr::has($dependencies, $name)) {
            $rows = [&$row];

            $this->getTable()->addRowableDependence($rows, $name);

            $this->firstAssocDefault()['dependencies'][$name] = $dependencies[$name];
        }

        return $this;
    }

    protected function &getDependenceAssoc($name)
    {
        $this->fetchDependence($name);

        $dependencies = &$this->firstAssoc('dependencies');

        $dependencies = (array)$dependencies;

        return Arr::getArrayRef($dependencies, $name);
    }

    protected function &getDependenceAssocDefault($name)
    {
        $this->fetchDependence($name);

        $dependencies = &$this->firstAssocDefault('dependencies');

        $dependencies = (array)$dependencies;

        return Arr::getArrayRef($dependencies, $name);
    }

    public function hasDependence($name)
    {
        return Arr::has($this->firstAssoc('dependencies'), $name);
    }

    /**
     * @param $name
     * @return Rowable|null
     * @throws \Exception
     */
    public function getDependence($name)
    {
        $row = &$this->getDependenceAssoc($name);

        if (!$row) {
            return null;
        }

        $rowDefault = &$this->getDependenceAssocDefault($name);

        return $this->getDependenceTable($name)->createRowable([], false)->appendRef($row, $rowDefault);
    }

    /**
     * @return Table
     * @throws \Exception
     */
    public function getTable()
    {
        if (!($table = $this->table())) {
            throw new \Exception('Please define a table for this rowable.');
        }

        return $table;
    }

    public function getTableName()
    {
        return $this->getTable()->getName();
    }

    public function toArray()
    {
        return $this->rows;
    }

    public function column($column)
    {
        $items = [];

        foreach($this as $row) {
            $items[] = $row[$column];
        }

        return $items;
    }

    public function getAll($column)
    {
        $items = [];

        foreach($this->getIterator() as $row) {
            $items[] = $row->get($column);
        }

        return $items;
    }

    public function find(callable $callable = null)
    {
        foreach($this->getIterator() as $key => $value) {
            if (call_user_func_array($callable, [$value, $key])) return $value;
        }

        return null;
    }

    /* START rows methods */

    public function set($key, $value = null)
    {
        foreach($this->rows as &$row) {
            if (is_array($key)) {
                $row['row'] = array_replace($row['row'], $key);
            } else {
                $row['row'][$key] = $value;
            }
        }

        return $this;
    }

    public function save()
    {
        foreach($this->rows as $key => &$row) {
            $default = &$this->defaults[$key];

            $data = $this->getTable()->parseData($row['row'], true, true);

            if (Arr::get($row, 'isNew')) {
                $this->getTable()->insert($data)->exec();

                $default['isNew'] = $row['isNew'] = false;

                if ($column = $this->getTable()->autoIncrement()) {
                    $data[$column] = (int)$this->getTable()->lastInsertId();
                }
            } else {
                if ($data = array_diff_assoc($data, $default['row'])) {
                    $this->getTable()
                        ->update($data)
                        ->whereCols($this->getFirstUniqueFromRow($default['row']))
                        ->exec();
                }
            }

            $default['row'] = $row['row'] = array_replace($row['row'], $data);
        }
        unset($row);

        return $this;
    }

    public function update(array $data)
    {
        $this->set($data);

        $this->save();

        return $this;
    }

    public function delete()
    {
        $keys = [];

        foreach($this->rows as $key => &$row) {
            $default = &$this->defaults[$key];

            $keys[] = array_values($this->getFirstUniqueFromRow($default['row']));

            $default['isNew'] = $row['isNew'] = true;
        }
        unset($row);

        $table = $this->getTable();

        $query = $table->delete()->whereCol($table->getFirstUnique(), $keys);

        $query->exec();

        return $this;
    }

    /* END rows methods */

    /* START First row methods */

    /**
     * @return $this
     */
    public function first()
    {
        $row = &$this->firstAssoc();

        if (!$row) {
            return null;
        }

        return $this->newRowable([], $this->table())->appendRef($row, $this->firstAssocDefault());
    }

    protected function &firstAssoc($key = null)
    {
        $row = &Arr::first($this->rows);

        if ($row and $key) {
            return Arr::getRef($row, $key);
        }

        return $row;
    }

    protected function &firstAssocDefault($key = null)
    {
        $row = &Arr::first($this->defaults);

        if ($row and $key) {
            return Arr::getRef($row, $key);
        }

        return $row;
    }

    protected function &firstAssocRow($key = null, $value = null)
    {
        $row = &$this->firstAssoc('row');

        if (func_num_args() === 1 and !is_array($key) and !array_key_exists($key, $row)) {
            $methodName = 'let' . ucfirst($key);

            if (method_exists($this, $methodName)) {
                $value = $this->callCallableWith([$this, $methodName]);

                return $value;
            }
        }

        return Obj::fetchArrayReplaceVar($this, $row, ...func_get_args());
    }

    protected function &firstAssocDefaultRow($key = null, $value = null)
    {
        return Obj::fetchArrayReplaceVar($this, $this->firstAssocDefault('row'), ...func_get_args());
    }

    public function autoIncrement()
    {
        return ($key = $this->getTable()->autoIncrement()) ? $this->firstAssocDefaultRow($key) : null;
    }

    public function primary()
    {
        $keys = [];

        foreach($this->getTable()->primary() as $key) {
            $keys[$key] = $this->firstAssocDefaultRow($key);
        }

        return $keys;
    }

    public function unique()
    {
        $keys = [];

        foreach($this->getTable()->unique() as $name => $info) {
            foreach($info['Keys'] as $key) {
                $keys[$name][$key['ColumnName']] = $this->firstAssocDefaultRow($key['ColumnName']);
            }
        }

        return $keys;
    }

    public function getFirstUnique()
    {
        return $this->getFirstUniqueFromRow($this->firstAssocDefaultRow());
    }

    public function get($column)
    {
        if (is_array($column)) {
            $values = [];

            foreach($column as $key => $name) {
                $filter = null;

                if (is_array($name)) {
                    $args = $name;

                    $name = array_shift($args);

                    $filter = array_shift($args);
                }

                $value = $this->firstAssocRow((string)$name);

                if (is_callable($filter)) {
                    $value = $this->callCallable($filter, $value);
                }

                $values[is_int($key) ? $name : $key] = $value;
            }

            return $values;
        }

        return $this->firstAssocRow($column);
    }

    public function offsetExists($offset)
    {
        return Arr::hasRef($this->firstAssocRow(), $offset);
    }


    public function offsetGet($offset)
    {
        return $this->firstAssocRow($offset);
    }


    public function offsetSet($offset, $value)
    {
        Arr::set($this->firstAssocRow(), $offset, $value);

        return $this;
    }

    public function offsetUnset($offset)
    {
        Arr::del($this->firstAssocRow(), $offset);

        return $this;
    }

    /* END First row methods */

    protected function getFirstUniqueFromRow(array $row)
    {
        $keys = [];

        foreach($this->getTable()->getFirstUnique() as $key) {
            $keys[$key] = $row[$key];
        }

        return $keys;
    }

    protected function newTableRelationship(Table $table, RowInterface $rowable)
    {
        return new TableRelationship($table, $rowable);
    }

    /**
     * @param Table $value
     * @return $this|Table
     */
    public function table(Table $value = null)
    {
        return Obj::fetchVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    protected function tableRelationships($key = null, $value = null, $type = Obj::PROP_APPEND)
    {
        return Obj::fetchArrayReplaceVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    /**
     * @param array $dataSet
     * @param Table|null $table
     * @return static
     */
    protected function newRowable(array $dataSet = [], Table $table = null)
    {
        $class = get_called_class();

        return new $class($dataSet, $table);
    }

    /**
     * @return static[]
     */
    public function getIterator()
    {
        foreach($this->rows as $key => &$row) {
            yield $this->newRowable([], $this->table())->appendRef($row, $this->defaults[$key]);
        }
    }

    /*
    protected function serializeParams()
    {
        return [
            'rows' => $this->rows,
            'defaults' => $this->defaults,
            'tableRelationships' => $this->tableRelationships,
            'total' => $this->total,
            'page' => $this->page,
            'limit' => $this->limit,
        ];
    }

    public function serialize()
    {
        return serialize($this->serializeParams());
    }

    protected function unserializeParams($data)
    {
        $this->rows = $data['rows'];

        $this->defaults = $data['defaults'];

        $this->tableRelationships = $data['tableRelationships'];

        $this->total = $data['total'];

        $this->page = $data['page'];

        $this->limit = $data['limit'];
    }

    public function unserialize($storage)
    {
        $this->unserializeParams(unserialize($storage));

        return $this;
    }
    */

    public function count()
    {
        return sizeof($this->rows);
    }

    public function maxPage()
    {
        $maxPage = 1;

        if (($total = $this->total()) > 0) {
            $maxPage = ceil($total / $this->limit());
        }

        return $maxPage;
    }

    public function prevPage()
    {
        $page = $this->page() - 1;

        return $page > 1 ? $page : 1;
    }

    public function nextPage()
    {
        $page = $this->page() + 1;

        $maxPage = $this->maxPage();

        return $page > $maxPage ? $maxPage : $page;
    }

    public function hasMorePages()
    {
        return $this->page() < $this->maxPage();
    }

    public function total($value = null)
    {
        return Obj::fetchIntVar($this, $this->{__FUNCTION__}, true, ...func_get_args());
    }

    public function page($value = null)
    {
        return Obj::fetchIntVar($this, $this->{__FUNCTION__}, true, ...func_get_args());
    }

    public function limit($value = null)
    {
        return Obj::fetchIntVar($this, $this->{__FUNCTION__}, true, ...func_get_args());
    }

    public function __debugInfo()
    {
        return Debug::fixInfo($this, get_object_vars($this), false);
    }
}