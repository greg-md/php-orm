<?php

namespace Greg\Orm;

use Greg\Cache\CacheStorageInterface;
use Greg\Orm\Query\Expr;
use Greg\Orm\Query\Where;
use Greg\Orm\Table\Column;
use Greg\Orm\Table\Row;
use Greg\Engine\InternalTrait;
use Greg\Support\DateTime;
use Greg\Support\Arr;
use Greg\Support\Url;

/**
 * Class Table
 * @package Greg\Orm
 *
 * @method beginTransaction()
 * @method commit()
 * @method rollBack()
 * @method lastInsertId($name = null)
 */
class TableOld implements TableInterface
{
    use InternalTrait;

    protected $prefix = null;

    protected $name = null;

    protected $alias = null;

    protected $columns = [];

    protected $columnsTypes = [];

    protected $columnName = null;

    protected $autoIncrement = null;

    protected $primary = [];

    protected $unique = [];

    protected $references = [];

    protected $relationships = [];

    protected $dependencies = [];

    protected $relationshipsAliases = [];

    protected $referencesAliases = [];

    protected $rowableClass = Table\Rowable::class;

    protected $rowClass = Table\Row::class;

    protected $rowFullClass = Table\RowFull::class;

    protected $rowsClass = Table\Rows::class;

    protected $rowsPaginationClass = Table\RowsPagination::class;

    protected $scaffolding = [];

    protected $nameColumn = null;

    protected $label = null;

    protected $storage = null;

    protected $cacheStorage = null;

    protected $autoLoadSchema = false;

    protected $autoLoadInfo = true;

    protected $autoLoadReferences = true;

    /**
     * If you want to enable it, make sure this table have cache storage, because the queries are slow.
     * @var bool
     */
    protected $autoLoadRelationships = false;

    protected $cacheSchema = true;

    protected $cacheSchemaLifetime = 0;

    protected $knownTables = [];

    public function init()
    {
        if ($this->autoLoadSchema()) {
            if ($this->autoLoadInfo()) {
                $info = $this->getInfo();

                $info['columns'] && $this->columns($info['columns']);

                $info['primaryKeys'] && $this->primary($info['primaryKeys']);

                $info['autoIncrement'] && $this->autoIncrement($info['autoIncrement']);
            }

            if ($this->autoLoadReferences()) {
                $this->references($references = $this->getReferences());
            }

            if ($this->autoLoadRelationships()) {
                $this->relationships($relationships = $this->getRelationships());
            }
        }

        if (method_exists($this, 'loadSchema')) {
            $this->loadSchema();
        }

        return $this;
    }

    protected function getInfo()
    {
        $caller = function() {
            return $this->storage()->getTableInfo($this->getName());
        };

        if ($this->cacheSchema() and ($cache = $this->cacheStorage())) {
            $info = $cache->fetch('table_info:' . $this->getName(), $caller, $this->cacheSchemaLifetime());
        } else {
            $info = $caller();
        }

        return $info;
    }

    protected function getReferences()
    {
        $caller = function() {
            return $this->storage()->getTableReferences($this->getName());
        };

        if ($this->cacheSchema() and ($cache = $this->cacheStorage())) {
            $info = $cache->fetch('table_references:' . $this->getName(), $caller, $this->cacheSchemaLifetime());
        } else {
            $info = $caller();
        }

        return $info;
    }

    protected function getRelationships()
    {
        $caller = function() {
            return $this->storage()->getTableRelationships($this->getName());
        };

        if ($this->cacheSchema() and ($cache = $this->cacheStorage())) {
            $info = $cache->fetch('table_relationships:' . $this->getName(), $caller, $this->cacheSchemaLifetime());
        } else {
            $info = $caller();
        }

        return $info;
    }

    /**
     * @param Table\Column[] $columns
     * @return $this
     */
    public function addColumns(array $columns)
    {
        foreach($columns as $column) {
            $this->columns($column->name(), $column);
        }

        return $this;
    }

    public function getFirstUnique()
    {
        if ($autoIncrement = $this->autoIncrement()) {
            return [$autoIncrement];
        }

        if ($primary = $this->primary()) {
            return $primary;
        }

        if ($unique = current($this->unique())) {
            return $unique['keys'];
        }

        return array_keys($this->columns());
    }

    public function combineFirstUnique($values)
    {
        $values = (array)$values;

        if (!$keys = $this->getFirstUnique()) {
            throw new \Exception('Table does not have primary keys.');
        }

        if (sizeof($keys) !== sizeof($values)) {
            throw new \Exception('Unique columns count should be the same as keys count.');
        }

        return array_combine($keys, $values);
    }

    public function find($keys)
    {
        if (!is_array($keys)) {
            $keys = $this->combineFirstUnique($keys);
        }

        return $this->select()->whereCols($keys)->rows();
    }

    /**
     * @param null $keys
     * @return Table\Rowable|Table\Rowable[]
     * @throws \Exception
     */
    public function findRowable($keys = null)
    {
        $query = $this->select();

        if ($keys) {
            if (!is_array($keys)) {
                $keys = $this->combineFirstUnique($keys);
            }

            $query->whereCols($keys);
        }

        return $query->rowableAll();
    }

    /**
     * @param null $keys
     * @param null $references
     * @param null $relationships
     * @param string $dependencies
     * @return Table\Rowable|Table\Rowable[]
     * @throws \Exception
     */
    public function findRowableFull($keys = null, $references = null, $relationships = null, $dependencies = '*')
    {
        $query = $this->select();

        if ($keys) {
            if (!is_array($keys)) {
                $keys = $this->combineFirstUnique($keys);
            }

            $query->whereCols($keys);
        }

        return $query->rowableAllFull($references, $relationships, $dependencies);
    }

    public function getRowable($keys)
    {
        if (!is_array($keys)) {
            $keys = $this->combineFirstUnique($keys);
        }

        return $this->select()->whereCols($keys)->rowable();
    }

    public function getRowableFull($keys, $references = null, $relationships = null, $dependencies = '*')
    {
        if (!is_array($keys)) {
            $keys = $this->combineFirstUnique($keys);
        }

        return $this->select()->whereCols($keys)->rowableFull($references, $relationships, $dependencies);
    }

    public function count(array $whereCols = [])
    {
        return $this->select('count(*)')->whereCols($whereCols)->one();
    }

    public function select($columns = null, $_ = null)
    {
        if (!is_array($columns)) {
            $columns = func_get_args();
        }

        return $this->storage()->select($columns)->table($this)->from($this);
    }

    public function insert(array $data = [])
    {
        return $this->storage()->insert($this)->data($data);
    }

    public function insertData(array $data = [])
    {
        $this->insert($data)->exec();

        return $this;
    }

    public function insertDataFull(array $data = [], $relationships = [])
    {
        $row = $this->newRow($data);

        $row->save();

        $relationships && $this->insertDataRelationships($row, $relationships);

        return $this;
    }

    public function insertDataRelationships(Row $row, $relationships = [])
    {
        foreach($relationships as $relationshipPath => $inserts) {
            list($tableName, $constraintColumns) = explode(':', $relationshipPath, 2);

            $constraintColumns = explode('.', $constraintColumns);

            sort($constraintColumns);

            $table = $this->getKnownTable($tableName);

            foreach($this->getTableRelationships($tableName) as $relationshipInfo) {
                $relationshipColumns = [];

                $relationshipInsert = [];

                foreach($relationshipInfo['Constraint'] as $constraint) {
                    $relationshipColumns[] = $constraint['RelationshipColumnName'];

                    $relationshipInsert[$constraint['RelationshipColumnName']] = $row[$constraint['ColumnName']];
                }

                sort($relationshipColumns);

                if ($constraintColumns != $relationshipColumns) {
                    continue;
                }

                foreach($inserts as $insert) {
                    $table->insertData($relationshipInsert + $insert);
                }

                break;
            }
        }

        return $this;
    }

    public function update(array $values = [])
    {
        $query = $this->storage()->update($this);

        if ($values) {
            $query->set($values);
        }

        return $query;
    }

    public function delete(array $whereIs = [])
    {
        $query = $this->storage()->delete($this, true);

        if ($whereIs) {
            $query->whereCols($whereIs);
        }

        return $query;
    }

    public function getPairs(array $whereIs = [], callable $callable = null)
    {
        if (!$columnName = $this->columnName()) {
            throw new \Exception('Undefined column name for table `' . $this->getName() . '`');
        }

        $query = $this->select();

        $query->columns($query->concat($this->getFirstUnique(), ':'), $columnName);

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
        return $this->select(new Expr(1))->whereCol($column, $value)->exists();
    }

    public function newRow(array $data = [])
    {
        $row = $this->createRow($data);

        $row->isNew(true);

        return $row;
    }

    /**
     * @param array $data
     * @param bool $reset
     * @return \Greg\Orm\Table\Row
     * @throws \Exception
     */
    public function createRow(array $data, $reset = true)
    {
        if (!($class = $this->rowClass())) {
            throw new \Exception('Undefined table row class.');
        }

        if ($reset) {
            $rowData = [];

            foreach($this->columns() as $name => $column) {
                $rowData[$column->name()] = $column->defaultValue();
            }

            $data = array_merge($rowData, $data);
        }

        return $this->loadClassInstance($class, $this, $data);
    }

    public function createRowFull(array $data)
    {
        if (!($class = $this->rowFullClass())) {
            throw new \Exception('Undefined table row full class.');
        }

        return $this->loadClassInstance($class, $this, $data);
    }

    public function newRowableRow(array $row = [], $reset = true)
    {
        return $this->newRowableFullRow(['row' => $row], $reset);
    }

    public function newRowableFullRow(array $row = [], $reset = true)
    {
        return $this->newRowable([$row], $reset);
    }

    public function newRowable(array $rows = [], $reset = true)
    {
        foreach($rows as &$row) {
            $row['isNew'] = true;
        }
        unset($row);

        return $this->createRowable($rows, $reset);
    }

    public function createRowableRow(array $row = [], $reset = true)
    {
        return $this->createRowableFullRow(['row' => $row], $reset);
    }

    public function createRowableFullRow(array $row = [], $reset = true)
    {
        return $this->createRowable([$row], $reset);
    }

    /**
     * @param array $rows
     * @param bool $reset
     * @return Table\Rowable[]|Table\Rowable
     * @throws \Exception
     */
    public function createRowable(array $rows = [], $reset = true)
    {
        if (!($class = $this->rowableClass())) {
            throw new \Exception('Undefined table rowable class.');
        }

        if ($reset && $rows) {
            $defaults = [];

            foreach($this->columns() as $name => $column) {
                $defaults[$column->name()] = $column->defaultValue();
            }

            foreach($rows as &$row) {
                $row['row'] = array_replace($defaults, $row['row']);
            }
            unset($row);
        }

        return $this->loadClassInstance($class, $rows, $this);
    }

    /**
     * @param $data
     * @return \Greg\Orm\Table\Row[]|\Greg\Orm\Table\Rows
     * @throws \Exception
     */
    public function createRows(array $data = [])
    {
        $class = $this->rowsClass();

        if (!$class) {
            throw new \Exception('Undefined table row set class.');
        }

        return $this->loadClassInstance($class, $this, $data);
    }

    /**
     * @param $items
     * @param $total
     * @param $page
     * @param $limit
     * @return \Greg\Orm\Table\Row[]|\Greg\Orm\Table\RowsPagination
     * @throws \Exception
     */
    public function createRowsPagination($items, $total, $page = null, $limit = null)
    {
        $class = $this->rowsPaginationClass();

        if (!$class) {
            throw new \Exception('Undefined table row set pagination class.');
        }

        return $this->loadClassInstance($class, $this, $items, $total, $page, $limit);
    }

    public function addFullInfo(&$items, $references = null, $relationships = null, $dependencies = '*', $rows = false)
    {
        $this->toFullFormat($items, $rows);

        if ($dependencies) {
            $this->addDependencies($items, $dependencies, $rows);
        }

        if ($references) {
            $this->addReferences($items, $references, $rows);
        }

        if ($relationships) {
            $this->addRelationships($items, $relationships, $rows);
        }

        return $this;
    }

    public function addReferences(&$items, $references, $rows = false)
    {
        if ($references == '*') {
            $references = array_keys($this->references());
        }

        $references = (array)$references;

        $tablesReferences = Arr::group($this->references(), 'ReferencedTableName', false);

        if ($references) {
            foreach($items as &$item) {
                $item['references'] = [];
            }
            unset($item);
        }

        foreach($references as $referenceTableName => $referenceParams) {
            if (is_int($referenceTableName)) {
                $referenceTableName = $referenceParams;

                $referenceParams = [];
            }

            $referenceParams = array_merge([
                'references' => null,
                'relationships' => null,
                'dependencies' => '*',
                'full' => true,
                'callback' => null,
            ], $referenceParams);

            $referenceTable = $this->getKnownTable($referenceTableName);

            $tableReferences = $tablesReferences[$referenceTable->getName()];

            if (!$tableReferences) {
                throw new \Exception('Reference `' . $referenceTableName . '` not found.');
            }

            foreach($tableReferences as $info) {
                $columns = [];

                foreach($info['Constraint'] as $constraint) {
                    $columns[] = $constraint['ColumnName'];
                }

                $key = implode('.', $columns);

                $key = $this->referencesAliases($key) ?: $key;

                foreach($items as &$item) {
                    $item['references'][$key] = null;
                }
            }
            unset($item);

            $parts = [];

            foreach($tableReferences as $info) {
                $columns = [];

                foreach($info['Constraint'] as $constraint) {
                    $columns[] = $constraint['ReferencedColumnName'];
                }

                $columnsCount = sizeof($columns);

                $columns = implode('.', $columns);

                if (!isset($parts[$columns])) {
                    $parts[$columns] = [];
                }

                foreach($items as $item) {
                    $itemKeys = [];

                    foreach($info['Constraint'] as $constraint) {
                        $columnName = $constraint['ColumnName'];

                        $key = $item[$this->getName()][$columnName];

                        if (!$key and !$referenceTable->columns($constraint['ReferencedColumnName'])->allowNull()) {
                            continue 2;
                        }

                        if ($key or $columnsCount > 1) {
                            $itemKeys[] = $key;
                        }
                    }

                    if (!array_filter($itemKeys)) {
                        continue;
                    }

                    $hasKeysCombination = false;

                    foreach($parts[$columns] as $keys) {
                        if ($keys === $itemKeys) {
                            $hasKeysCombination = true;
                            break;
                        }
                    }

                    if (!$hasKeysCombination) {
                        $parts[$columns][] = $itemKeys;
                    }
                }
            }

            foreach($parts as $key => $part) {
                if (!$part) {
                    unset($parts[$key]);
                }
            }

            if (!$parts) {
                continue;
            }

            $query = $referenceTable->select();

            if (is_callable($referenceParams['callback'])) {
                $this->callCallable($referenceParams['callback'], $query);
            }

            $query->where(function(Where $query) use ($parts) {
                foreach($parts as $columns => $values) {
                    $query->orWhereCol(explode('.', $columns), $values);
                }
            });

            if ($referenceParams['full']) {
                $referenceItems = $query->{$rows ? 'rowsFull' : 'assocAllFull'}($referenceParams['references'], $referenceParams['relationships'], $referenceParams['dependencies']);
            } else {
                $referenceItems = $query->{$rows ? 'rows' : 'assocAll'}();
            }

            foreach($tableReferences as $info) {
                $columns = $rColumns = [];

                foreach($info['Constraint'] as $constraint) {
                    $columns[] = $constraint['ColumnName'];

                    $rColumns[] = $constraint['ReferencedColumnName'];
                }

                $key = implode('.', $columns);

                $key = $this->referencesAliases($key) ?: $key;

                foreach($items as &$item) {
                    $values = [];

                    foreach($columns as $column) {
                        $values[] = $item[$this->getName()][$column];
                    }

                    if (array_filter($values)) {
                        foreach($referenceItems as $reItem) {
                            $rValues = [];

                            foreach($rColumns as $rColumn) {
                                if ($referenceParams['full']) {
                                    $rValues[] = $reItem[$referenceTableName][$rColumn];
                                } else {
                                    $rValues[] = $reItem[$rColumn];
                                }
                            }

                            if ($values === $rValues) {
                                $item['references'][$key] = $reItem;
                                break;
                            }
                        }
                    }
                }
                unset($item);
            }
        }

        return $this;
    }

    public function addRelationships(&$items, $relationships, $rows = false)
    {
        if ($relationships == '*') {
            $relationships = array_keys($this->relationships());
        }

        if ($relationships = (array)$relationships) {
            foreach($items as &$item) {
                $item['relationships'] = [];
            }
            unset($item);
        }

        foreach($relationships as $relationshipTableName => $relationshipParams) {
            if (is_int($relationshipTableName)) {
                $relationshipTableName = $relationshipParams;

                $relationshipParams = [];
            }

            $relationshipParams = array_merge([
                'references' => null,
                'relationships' => null,
                'dependencies' => '*',
                'full' => true,
                'callback' => null,
            ], $relationshipParams);

            $relationshipTable = $this->getKnownTable($relationshipTableName);

            $tableRelationships = $this->getTableRelationships($relationshipTable->getName());

            foreach($items as &$item) {
                foreach($tableRelationships as $info) {
                    $key = [$relationshipTableName];

                    foreach($info['Constraint'] as $constraint) {
                        $key[] = $constraint['RelationshipColumnName'];
                    }

                    $key = implode('.', $key);

                    $item['relationships'][$this->relationshipsAliases($key) ?: $key] = $rows ? $relationshipTable->createRows() : [];
                }
            }
            unset($item);

            $query = $relationshipTable->select();

            if (is_callable($relationshipParams['callback'])) {
                $this->callCallable($relationshipParams['callback'], $query);
            }

            if ($query->limit()) {
                dd('under construction references with limit');
                /*
                $queries = [];

                foreach($items as $item) {
                    $parts = [];
                    foreach($tableRelationships as $info) {
                        $itemKeys = [];
                        foreach($info['Constraint'] as $constraint) {
                            $key = $item[$modelName][$constraint['ColumnName']];
                            if ($key) {
                                $itemKeys[] = $key;
                            }
                        }
                        if (!array_filter($itemKeys)) {
                            continue;
                        }
                        $columns = [];
                        foreach($info['Constraint'] as $constraint) {
                            $columns[] = $constraint['RelationshipColumnName'];
                        }
                        $columns = implode('.', $columns);
                        $parts[$columns] = $itemKeys;
                    }

                    $q = clone $query;
                    foreach($parts as $columns => $values) {
                        $q->whereRow(explode('.', $columns), $values);
                    }
                    $queries[] = $q;
                }

                $query = $this->select()->union($queries);
                */
            } else {
                $parts = [];

                foreach($tableRelationships as $info) {
                    $columns = [];

                    foreach($info['Constraint'] as $constraint) {
                        $columns[] = $constraint['RelationshipColumnName'];
                    }

                    $columns = implode('.', $columns);

                    if (!isset($parts[$columns])) {
                        $parts[$columns] = [];
                    }

                    foreach($items as $item) {
                        $itemKeys = [];

                        foreach($info['Constraint'] as $constraint) {
                            $key = $item[$this->getName()][$constraint['ColumnName']];

                            if ($key) {
                                $itemKeys[] = $key;
                            }
                        }

                        if (!array_filter($itemKeys)) {
                            continue;
                        }

                        $hasKeysCombination = false;

                        foreach($parts[$columns] as $keys) {
                            if ($keys === $itemKeys) {
                                $hasKeysCombination = true;
                                break;
                            }
                        }

                        if (!$hasKeysCombination) {
                            $parts[$columns][] = $itemKeys;
                        }
                    }
                }

                foreach($parts as $key => $part) {
                    if (!$part) {
                        unset($parts[$key]);
                    }
                }

                if (!$parts) {
                    continue;
                }

                $query->where(function(Where $query) use ($parts) {
                    foreach($parts as $columns => $values) {
                        $query->orWhereCol(explode('.', $columns), $values);
                    }
                });
            }

            if ($relationshipParams['full']) {
                $relationshipItems = $query->{$rows ? 'rowsFull' : 'assocAllFull'}($relationshipParams['references'], $relationshipParams['relationships'], $relationshipParams['dependencies']);
            } else {
                $relationshipItems = $query->{$rows ? 'rows' : 'assocAll'}();
            }

            foreach($tableRelationships as $info) {
                $columns = $relationshipColumns = [];

                foreach($info['Constraint'] as $constraint) {
                    $columns[] = $constraint['ColumnName'];

                    $relationshipColumns[] = $constraint['RelationshipColumnName'];
                }

                $key = $relationshipColumns;

                Arr::prepend($key, $relationshipTableName);

                $key = implode('.', $key);

                $key = $this->relationshipsAliases($key) ?: $key;

                foreach($items as &$item) {
                    $values = [];

                    foreach($columns as $column) {
                        $values[] = $item[$this->getName()][$column];
                    }

                    if (array_filter($values)) {
                        foreach($relationshipItems as $relationshipItem) {
                            $relationshipValues = [];

                            foreach($relationshipColumns as $relationshipColumn) {
                                if ($relationshipParams['full']) {
                                    $relationshipValues[] = $relationshipItem[$relationshipTableName][$relationshipColumn];
                                } else {
                                    $relationshipValues[] = $relationshipItem[$relationshipColumn];
                                }
                            }

                            if ($values === $relationshipValues) {
                                $item['relationships'][$key][] = $relationshipItem;
                            }
                        }
                    }
                }
                unset($item);
            }
        }

        return $this;
    }

    public function addDependencies(&$items, $dependencies, $rows = false)
    {
        if ($dependencies == '*') {
            $dependencies = array_keys($this->dependencies());
        }

        $dependencies = (array)$dependencies;

        foreach($dependencies as $dependenceName => $dependenceParams) {
            if (is_int($dependenceName)) {
                $dependenceName = $dependenceParams;

                $dependenceParams = [];
            }

            $dependenceParams = array_merge([
                'references' => null,
                'relationships' => null,
                'dependencies' => '*',
                'full' => true,
                'callback' => null,
            ], $dependenceParams);

            foreach($items as $item) {
                $item[$dependenceName] = null;
            }
            unset($item);

            $dependenceInfo = $this->dependencies($dependenceName);

            /* @var $dependenceTable string|Table */
            $dependenceTable = $this->getKnownTable($dependenceInfo['table']);

            $tableRelationships = $this->getTableRelationships($dependenceTable->name());

            $parts = [];

            foreach($tableRelationships as $tableRelationship) {
                $columns = [];

                foreach($tableRelationship['Constraint'] as $constraint) {
                    $columns[] = $constraint['RelationshipColumnName'];
                }

                $columns = implode('.', $columns);

                if (!isset($parts[$columns])) {
                    $parts[$columns] = [];
                }

                foreach($items as $item) {
                    $itemKeys = [];

                    foreach($tableRelationship['Constraint'] as $constraint) {
                        $key = $item[$this->name()][$constraint['ColumnName']];

                        if ($key) {
                            $itemKeys[] = $key;
                        }
                    }

                    if (!array_filter($itemKeys)) {
                        continue;
                    }

                    $hasKeysCombination = false;

                    foreach($parts[$columns] as $keys) {
                        if ($keys === $itemKeys) {
                            $hasKeysCombination = true;

                            break;
                        }
                    }

                    if (!$hasKeysCombination) {
                        $parts[$columns][] = $itemKeys;
                    }
                }
            }

            foreach($parts as $key => $part) {
                if (!$part) {
                    unset($parts[$key]);
                }
            }

            if (!$parts) {
                continue;
            }

            $query = $dependenceTable->select();

            if (is_callable($dependenceParams['callback'])) {
                $this->callCallable($dependenceParams['callback'], $query);
            }

            if ($filter = $dependenceInfo['filter']) {
                $query->whereCols($filter);
            }

            $query->where(function(Where $query) use ($parts) {
                foreach($parts as $columns => $values) {
                    $query->orWhereCol(explode('.', $columns), $values);
                }
            });

            if ($dependenceParams['full']) {
                if ($rows) {
                    $dependenceItems = $query->rowsFull($dependenceParams['references'], $dependenceParams['relationships'], $dependenceParams['dependencies'], false);
                } else {
                    $dependenceItems = $query->assocAllFull($dependenceParams['references'], $dependenceParams['relationships'], $dependenceParams['dependencies']);
                }
            } else {
                if ($rows) {
                    $dependenceItems = $query->rows();
                } else {
                    $dependenceItems = $query->assocAll();
                }
            }

            foreach($tableRelationships as $tableRelationship) {
                $columns = $relationshipColumns = [];

                foreach($tableRelationship['Constraint'] as $constraint) {
                    $columns[] = $constraint['ColumnName'];

                    $relationshipColumns[] = $constraint['RelationshipColumnName'];
                }

                foreach($items as &$item) {
                    $values = [];

                    foreach($columns as $column) {
                        $values[] = $item[$this->getName()][$column];
                    }

                    if (array_filter($values)) {
                        foreach($dependenceItems as $dependenceItem) {
                            $relationshipValues = [];

                            foreach($relationshipColumns as $relationshipColumn) {
                                if ($dependenceParams['full']) {
                                    $relationshipValues[] = $dependenceItem[$dependenceTable->name()][$relationshipColumn];
                                } else {
                                    $relationshipValues[] = $dependenceItem[$relationshipColumn];
                                }
                            }

                            if ($values === $relationshipValues) {
                                $item[$dependenceName] = $dependenceItem;
                            }
                        }
                    }
                }
                unset($item);
            }
        }

        return $this;
    }

    public function toFullFormat(&$items, $rows = false)
    {
        foreach($items as $key => &$item) {
            if ($rows) {
                $item = $this->createRow($item);
            }

            $item = [
                $this->getName() => $item,
            ];

            if ($rows) {
                $item = $this->createRowFull($item);
            }
        }
        unset($item);

        return $this;
    }







    public function fixRowableFormat(&$rows)
    {
        foreach($rows as &$row) {
            $row = [
                'row' => $row,
                'dependencies' => [],
                'references' => [],
                'relationships' => [],
            ];
        }
        unset($row);

        return $this;
    }

    public function addRowableInfo(&$rows, $references = null, $relationships = null, $dependencies = '*')
    {
        $this->fixRowableFormat($rows);

        if ($dependencies) {
            $this->addRowableDependencies($rows, $dependencies);
        }

        if ($references) {
            $this->addRowableReferences($rows, $references);
        }

        if ($relationships) {
            $this->addRowableRelationships($rows, $relationships);
        }

        return $rows;
    }

    protected function fixRowableFullParams(&$params)
    {
        if (is_bool($params)) {
            $params = [
                'full' => $params,
            ];
        }

        if ($params instanceof \Closure) {
            $params = [
                'callback' => $params,
            ];
        }

        $params = array_merge([
            'references' => null,
            'relationships' => null,
            'dependencies' => '*',
            'full' => true,
            'callback' => null,
        ], $params);

        return $this;
    }

    protected function getRowableRelationshipsParts(&$rows, $relationships, $tableName, $constraintName = 'RelationshipColumnName')
    {
        $table = $this->getKnownTable($tableName);

        $parts = [];

        foreach($relationships as $info) {
            $columns = [];

            foreach($info['Constraint'] as $constraint) {
                $columns[] = $constraint[$constraintName];
            }

            $columnsCount = sizeof($columns);

            $columns = implode('.', $columns);

            if (!isset($parts[$columns])) {
                $parts[$columns] = [];
            }

            foreach($rows as &$rowFull) {
                $itemKeys = [];

                foreach($info['Constraint'] as $constraint) {
                    $key = $rowFull['row'][$constraint['ColumnName']];

                    if (!$key and !$table->columns($constraint[$constraintName])->allowNull()) {
                        continue 2;
                    }

                    if ($key or $columnsCount > 1) {
                        $itemKeys[] = $key;
                    }
                }

                if (!array_filter($itemKeys)) {
                    continue;
                }

                $hasKeysCombination = false;

                foreach($parts[$columns] as $keys) {
                    if ($keys === $itemKeys) {
                        $hasKeysCombination = true;
                        break;
                    }
                }

                if (!$hasKeysCombination) {
                    $parts[$columns][] = $itemKeys;
                }
            }
            unset($rowFull);
        }

        foreach($parts as $key => $part) {
            if (!$part) {
                unset($parts[$key]);
            }
        }

        return $parts;
    }




    protected function findDependencies($dependencies = '*')
    {
        if ($dependencies == '*') {
            $dependencies = array_keys($this->dependencies());
        }

        return (string)$dependencies;
    }

    protected function prepareRowableTableDependenceFormat(&$rows, $name)
    {
        foreach($rows as &$row) {
            $row['dependencies'][$name] = null;
        }
        unset($row);

        return $this;
    }

    public function addRowableDependencies(&$rows, $dependencies = '*')
    {
        foreach($this->findDependencies($dependencies) as $name => $params) {
            if (is_int($name)) {
                $name = $params;

                $params = [];
            }

            $this->addRowableDependence($rows, $name, $params);
        }

        return $this;
    }

    public function addRowableDependence(&$rows, $name, $params = [])
    {
        $this->fixRowableFullParams($params);

        $this->prepareRowableTableDependenceFormat($rows, $name);

        $dependenceInfo = $this->dependencies($name);

        /* @var $table string|Table */
        $table = $this->getKnownTable($dependenceInfo['table']);

        $relationships = $this->getTableRelationships($table->name());

        $parts = $this->getRowableRelationshipsParts($rows, $relationships, $table->getName());

        if (!$parts) {
            return $this;
        }

        $query = $table->select();

        if (is_callable($params['callback'])) {
            $this->callCallable($params['callback'], $query);
        }

        if ($filter = $dependenceInfo['filter']) {
            $query->whereCols($filter);
        }

        $query->where(function(Where $query) use ($parts) {
            foreach($parts as $columns => $values) {
                $query->orWhereCol(explode('.', $columns), $values);
            }
        });

        if ($params['full']) {
            $dependenceRows = $query->assocAllRowableFull($params['references'], $params['relationships'], $params['dependencies']);
        } else {
            $dependenceRows = $query->assocAllRowable();
        }

        foreach($relationships as $info) {
            $columns = $relationshipColumns = [];

            foreach($info['Constraint'] as $constraint) {
                $columns[] = $constraint['ColumnName'];

                $relationshipColumns[] = $constraint['RelationshipColumnName'];
            }

            foreach($rows as &$rowFull) {
                $values = [];

                foreach($columns as $column) {
                    $values[] = $rowFull['row'][$column];
                }

                if (array_filter($values)) {
                    foreach($dependenceRows as $dependenceRow) {
                        $relationshipValues = [];

                        foreach($relationshipColumns as $relationshipColumn) {
                            $relationshipValues[] = $dependenceRow['row'][$relationshipColumn];
                        }

                        if ($values === $relationshipValues) {
                            $rowFull['dependencies'][$name] = $dependenceRow;
                        }
                    }
                }
            }
            unset($rowFull);
        }

        return $this;
    }




    protected function findReferences($references = '*')
    {
        if ($references == '*') {
            $references = array_keys($this->references());
        }

        return (array)$references;
    }

    protected function prepareRowableTableReferencesFormat(&$rows, $tableName)
    {
        $references = $this->getTableReferences($tableName);

        foreach($references as $info) {
            $key = [];

            foreach($info['Constraint'] as $constraint) {
                $key[] = $constraint['ColumnName'];
            }

            $key = implode('.', $key);

            $key = $this->referencesAliases($key) ?: $key;

            foreach($rows as &$rowFull) {
                $rowFull['references'][$key] = null;
            }
        }
        unset($rowFull);

        return $this;
    }

    public function addRowableReferences(&$rows, $references)
    {
        foreach($this->findReferences($references) as $name => $params) {
            if (is_int($name)) {
                $name = $params;

                $params = [];
            }

            $this->addRowableReference($rows, $name, $params);
        }

        return $this;
    }

    public function addRowableReference(&$rows, $tableName, $params = [])
    {
        $this->fixRowableFullParams($params);

        $this->prepareRowableTableReferencesFormat($rows, $tableName);

        $table = $this->getKnownTable($tableName);

        $references = $this->getTableReferences($tableName);

        $parts = $this->getRowableRelationshipsParts($rows, $references, $tableName, 'ReferencedColumnName');

        if (!$parts) {
            return $this;
        }

        $query = $table->select();

        if (is_callable($params['callback'])) {
            $this->callCallable($params['callback'], $query);
        }

        $query->where(function(Where $query) use ($parts) {
            foreach($parts as $columns => $values) {
                $query->orWhereCol(explode('.', $columns), $values);
            }
        });

        if ($params['full']) {
            $referenceRows = $query->assocAllRowableFull($params['references'], $params['relationships'], $params['dependencies']);
        } else {
            $referenceRows = $query->assocAllRowable();
        }

        foreach($references as $info) {
            $columns = $rColumns = [];

            foreach($info['Constraint'] as $constraint) {
                $columns[] = $constraint['ColumnName'];

                $rColumns[] = $constraint['ReferencedColumnName'];
            }

            $key = implode('.', $columns);

            $key = $this->referencesAliases($key) ?: $key;

            foreach($rows as &$rowFull) {
                $values = [];

                foreach($columns as $column) {
                    $values[] = $rowFull['row'][$column];
                }

                if (array_filter($values)) {
                    foreach($referenceRows as $referenceRow) {
                        $rValues = [];

                        foreach($rColumns as $rColumn) {
                            $rValues[] = $referenceRow['row'][$rColumn];
                        }

                        if ($values === $rValues) {
                            $rowFull['references'][$key] = $referenceRow;
                            break;
                        }
                    }
                }
            }
            unset($rowFull);
        }

        return $this;
    }

    protected function findRelationships($relationships = '*')
    {
        if ($relationships == '*') {
            $relationships = array_keys($this->relationships());
        }

        return (array)$relationships;
    }

    protected function prepareRowableTableRelationshipsFormat(&$rows, $relationships)
    {
        foreach($relationships as $info) {
            $key = $this->relationshipsAliases($info['ConstraintName']) ?: $info['ConstraintName'];

            foreach($rows as &$row) {
                $row['relationships'][$key] = [];
            }
        }
        unset($row);

        return $this;
    }

    public function addRowableRelationships(&$rows, $relationships)
    {
        foreach($this->findRelationships($relationships) as $name => $params) {
            if (is_int($name)) {
                $name = $params;

                $params = [];
            }

            $this->addRowableRelationship($rows, $name, $params);
        }

        return $this;
    }

    public function addRowableRelationship(&$rows, $name, $params = [])
    {
        $table = $this->getRelationshipTable($name);

        $this->fixRowableFullParams($params);

        $relationships = $this->getTableRelationships($table->getName());

        $this->prepareRowableTableRelationshipsFormat($rows, $relationships);

        $query = $table->select();

        if (is_callable($params['callback'])) {
            $this->callCallable($params['callback'], $query);
        }

        if ($query->limit()) {
            dd('under construction references with limit');
            /*
            $queries = [];

            foreach($items as $item) {
                $parts = [];
                foreach($tableRelationships as $info) {
                    $itemKeys = [];
                    foreach($info['Constraint'] as $constraint) {
                        $key = $item[$modelName][$constraint['ColumnName']];
                        if ($key) {
                            $itemKeys[] = $key;
                        }
                    }
                    if (!array_filter($itemKeys)) {
                        continue;
                    }
                    $columns = [];
                    foreach($info['Constraint'] as $constraint) {
                        $columns[] = $constraint['RelationshipColumnName'];
                    }
                    $columns = implode('.', $columns);
                    $parts[$columns] = $itemKeys;
                }

                $q = clone $query;
                foreach($parts as $columns => $values) {
                    $q->whereRow(explode('.', $columns), $values);
                }
                $queries[] = $q;
            }

            $query = $this->select()->union($queries);
            */
        } else {
            $parts = $this->getRowableRelationshipsParts($rows, $relationships, $table->getName());

            if (!$parts) {
                return $this;
            }

            $query->where(function(Where $query) use ($parts) {
                foreach($parts as $columns => $values) {
                    $query->orWhereCol(explode('.', $columns), $values);
                }
            });
        }

        if ($params['full']) {
            $relationshipRows = $query->assocAllRowableFull($params['references'], $params['relationships'], $params['dependencies']);
        } else {
            $relationshipRows = $query->assocAllRowable();
        }

        foreach($relationships as $info) {
            $columns = $relationshipColumns = [];

            foreach($info['Constraint'] as $constraint) {
                $columns[] = $constraint['ColumnName'];

                $relationshipColumns[] = $constraint['RelationshipColumnName'];
            }

            $key = $this->relationshipsAliases($info['ConstraintName']) ?: $info['ConstraintName'];

            foreach($rows as &$rowFull) {
                $values = [];

                foreach($columns as $column) {
                    $values[] = $rowFull['row'][$column];
                }

                if (array_filter($values)) {
                    foreach($relationshipRows as $relationshipItem) {
                        $relationshipValues = [];

                        foreach($relationshipColumns as $relationshipColumn) {
                            $relationshipValues[] = $relationshipItem['row'][$relationshipColumn];
                        }

                        if ($values === $relationshipValues) {
                            $rowFull['relationships'][$key][] = $relationshipItem;
                        }
                    }
                }
            }
            unset($rowFull);
        }

        return $this;
    }







    public function parseData(array $data, $clear = false, $reverse = false)
    {
        foreach($data as $columnName => &$value) {
            if (!($column = $this->columns($columnName))) {
                if ($clear) {
                    unset($data[$columnName]);
                }

                continue;
            }

            if (($value instanceof Expr)) {
                continue;
            }

            if ($value === '') {
                $value = null;
            }

            if (!$column->allowNull()) {
                $value = (string)$value;
            }

            if ($column->isInt() and (!$column->allowNull() or $value !== null)) {
                $value = (int)$value;
            }

            if ($column->isFloat() and (!$column->allowNull() or $value !== null)) {
                $value = (double)$value;
            }

            switch($this->columnsTypes($columnName) ?: $column->type()) {
                case Column::TYPE_DATETIME:
                case Column::TYPE_TIMESTAMP:
                    if ($value) {
                        $value = DateTime::formatTimeLocale('%Y-%m-%d %H:%M:%S', strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);
                    }

                    break;
                case Column::TYPE_DATE:
                    if ($value) {
                        $value = DateTime::formatTimeLocale('%Y-%m-%d', $value);
                    }

                    break;
                case Column::TYPE_TIME:
                    if ($value) {
                        $value = DateTime::formatTimeLocale('%H:%M:%S', $value);
                    }

                    break;
                case 'sys_name':
                    if ($reverse && $value) {
                        $value = Url::transform($value);
                    }

                    break;
                case 'boolean':
                    $value = (bool)$value;

                    break;
                case 'json':
                    $value = $reverse ? json_encode($value) : json_decode($value, true);

                    break;
            }
        }
        unset($value);

        return $data;
    }

    public function getColumn($name)
    {
        if (!$column = $this->columns($name)) {
            throw new \Exception('Column `' . $name . '` not found in table `' . $this->name() . '`.');
        }

        return $column;
    }

    public function getColumnType($name)
    {
        return $this->columnsTypes($name) ?: (($column = $this->columns($name)) ? $column->type() : null);
    }

    public function getName()
    {
        if (!($name = $this->name())) {
            throw new \Exception('Table name is not defined.');
        }

        return $name;
    }

    public function getTableRelationships($name = null)
    {
        $tablesRelationships = Arr::group($this->relationships(), 'RelationshipTableName', false);

        if ($name) {
            if (!Arr::hasRef($tablesRelationships, $name)) {
                throw new \Exception('Relationship table `' . $name . '` not found in `' . $this->name() . '`.');
            }

            return $tablesRelationships[$name];
        }

        return $tablesRelationships;
    }

    public function getTableReferences($name = null)
    {
        $tablesReferences = Arr::group($this->references(), 'ReferencedTableName', false);

        if ($name) {
            if (!Arr::hasRef($tablesReferences, $name)) {
                throw new \Exception('Referenced table `' . $name . '` not found in `' . $this->name() . '`.');
            }

            return $tablesReferences[$name];
        }

        return $tablesReferences;
    }

    public function getRelationshipTable($name)
    {
        $name = Arr::get(array_flip($this->relationshipsAliases()), $name, $name);

        if (!$relationship = $this->relationships($name)) {
            throw new \Exception('Relationship `' . $name . '` not found in table `' . $this->getName() . '`.');
        }

        return $this->getKnownTable($relationship['RelationshipTableName']);
    }

    /**
     * @param $name
     * @return Table
     * @throws \Exception
     */
    public function getReferenceTable($name)
    {
        $reference = Arr::get(array_flip($this->referencesAliases()), $name, $name);

        return $this->getReferenceTableByColumn($reference);
    }

    /**
     * @param $name
     * @return Table
     * @throws \Exception
     */
    public function getDependenceTable($name)
    {
        $dependenceInfo = $this->dependencies($name);

        return $this->getKnownTable($dependenceInfo['table']);
    }

    public function findReferenceTableByColumn($name)
    {
        foreach($this->references() as $reference) {
            if ($name == implode('.', array_column($reference['Constraint'], 'ColumnName'))) {
                return $this->getKnownTable($reference['ReferencedTableName']);
            }
        }

        return null;
    }

    public function getReferenceTableByColumn($name)
    {
        if (!$reference = $this->findReferenceTableByColumn($name)) {
            throw new \Exception('Reference table not found by column `' . $name . '` in table `' . $this->getName() . '`.');
        }

        return $reference;
    }

    /**
     * @param $name
     * @return Table
     * @throws \Exception
     */
    public function getKnownTable($name)
    {
        $table = $this->knownTables($name);

        if (!$table) {
            throw new \Exception('Known table `' . $name . '` not found in table `' . $this->getName() . '`.');
        }

        if (is_callable($table)) {
            $table = $this->callCallable($table);
        }

        return $table;
    }

    public function prefix($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function name($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function alias($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    /**
     * @param null $key
     * @param null $value
     * @param string $type
     * @param bool $replace
     * @return $this|Table\Column|Table\Column[]
     */
    public function columns($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function columnsTypes($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function columnName($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function autoIncrement($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function primary($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function unique($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function references($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function relationships($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function dependencies($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function relationshipsAliases($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function referencesAliases($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function rowableClass($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function rowClass($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function rowFullClass($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function rowsClass($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function rowsPaginationClass($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function scaffolding($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function nameColumn($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function label($value = null, $type = Obj::PROP_REPLACE)
    {
        return Obj::fetchStrVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    /**
     * @param \Greg\Orm\StorageInterface $value
     * @return \Greg\Orm\StorageInterface|null
     */
    public function storage(\Greg\Orm\StorageInterface $value = null)
    {
        return Obj::fetchVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    /**
     * @param CacheStorageInterface $value
     * @return CacheStorageInterface|null
     */
    public function cacheStorage(CacheStorageInterface $value = null)
    {
        return Obj::fetchVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function autoLoadSchema($value = null)
    {
        return Obj::fetchBoolVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function autoLoadInfo($value = null)
    {
        return Obj::fetchBoolVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function autoLoadReferences($value = null)
    {
        return Obj::fetchBoolVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function autoLoadRelationships($value = null)
    {
        return Obj::fetchBoolVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function cacheSchema($value = null)
    {
        return Obj::fetchBoolVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function cacheSchemaLifetime($value = null)
    {
        return Obj::fetchIntVar($this, $this->{__FUNCTION__}, true, ...func_get_args());
    }

    public function knownTables($key = null, $value = null, $type = Obj::PROP_APPEND, $replace = false)
    {
        return Obj::fetchArrayVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function __call($method, $args = [])
    {
        return $this->storage()->{$method}(...$args);
    }
}