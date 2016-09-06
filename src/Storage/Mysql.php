<?php

namespace Greg\Orm\Storage;

use Greg\Orm\Adapter\AdapterInterface;
use Greg\Orm\Column;
use Greg\Orm\Query\FromQuery;
use Greg\Orm\Query\HavingQuery;
use Greg\Orm\Query\JoinsQuery;
use Greg\Orm\Query\QueryTrait;
use Greg\Orm\Query\WhereQuery;
use Greg\Orm\Storage\Mysql\Query\MysqlDeleteQuery;
use Greg\Orm\Storage\Mysql\Query\MysqlInsertQuery;
use Greg\Orm\Storage\Mysql\Query\MysqlQueryTrait;
use Greg\Orm\Storage\Mysql\Query\MysqlSelectQuery;
use Greg\Orm\Storage\Mysql\Query\MysqlUpdateQuery;
use Greg\Orm\Constraint;
use Greg\Support\Arr;
use Greg\Support\Str;

/**
 * Class Mysql
 * @package Greg\Orm\Storage
 *
 * @method Mysql\Adapter\MysqlAdapterInterface getAdapter();
 */
class Mysql implements MysqlInterface
{
    use StorageAdapterTrait;

    protected $tablesInfo = [];

    public function dbName()
    {
        return $this->getAdapter()->dbName();
    }

    public function select($column = null, $_ = null)
    {
        $query = new MysqlSelectQuery($this);

        if ($columns = is_array($column) ? $column : func_get_args()) {
            $query->columns($columns);
        }

        return $query;
    }

    public function insert($into = null)
    {
        $query = new MysqlInsertQuery($this);

        if ($into !== null) {
            $query->into($into);
        }

        return $query;
    }

    public function delete($from = null)
    {
        $query = new MysqlDeleteQuery($this);

        if ($from !== null) {
            $query->from($from);
        }

        return $query;
    }

    public function update($table = null)
    {
        $query = new MysqlUpdateQuery($this);

        if ($table !== null) {
            $query->table($table);
        }

        return $query;
    }

    public function from()
    {
        return new FromQuery($this);
    }

    public function joins()
    {
        return new JoinsQuery($this);
    }

    public function where()
    {
        return new WhereQuery($this);
    }

    public function having()
    {
        return new HavingQuery($this);
    }

    static public function quoteLike($value, $escape = '\\')
    {
        return QueryTrait::quoteLike($value, $escape);
    }

    static public function concat(array $values, $delimiter = '')
    {
        return MysqlQueryTrait::concat($values, $delimiter);
    }

    public function tableInfo($tableName, $save = true)
    {
        if (!$save) {
            return $this->fetchTableInfo($tableName);
        }

        if (!array_key_exists($tableName, $this->tablesInfo)) {
            $this->tablesInfo[$tableName] = $this->fetchTableInfo($tableName);
        }

        return $this->tablesInfo[$tableName];
    }

    protected function fetchTableInfo($tableName)
    {
        $stmt = $this->query('Describe `' . $tableName . '`');

        $columnsInfo = $stmt->fetchAssocAll();

        $primaryKeys = [];

        $autoIncrement = null;

        $columns = [];

        foreach($columnsInfo as $info) {
            if ($info['Key'] == 'PRI') {
                $primaryKeys[] = $info['Field'];
            }

            if ($info['Extra'] == 'auto_increment') {
                $autoIncrement = $info['Field'];
            }

            $columns[] = $this->newColumnInfo($info);
        }

        return [
            'columns' => $columns,
            'primaryKeys' => $primaryKeys,
            'autoIncrement' => $autoIncrement,
        ];
    }

    protected function newColumnInfo(array $info)
    {
        $info = $this->parseColumnInfo($info);

        $column = new Column();

        $column->setName($info['name']);

        $column->setType($info['type']);

        $column->setLength($info['length']);

        $column->unsigned($info['unsigned']);

        $column->null($info['null']);

        $column->setDefaultValue($info['defaultValue']);

        $column->setComment($info['comment']);

        $column->setValues($info['values']);

        return $column;
    }

    protected function parseColumnInfo($info)
    {
        $name = $info['Field'];

        $type = null;

        $length = null;

        $unsigned = false;

        $null = true;

        $defaultValue = null;

        $comment = null;

        $values = [];

        if (preg_match('#^([a-z]+)(?:\((.+?)\))?(?: (unsigned))?#i', $info['Type'], $matches)) {
            $type = $matches[1];

            if (Arr::hasRef($matches, 2)) {
                if ($matches[1] === 'enum') {
                    $values = str_getcsv($matches[2], ',', "'");
                } else {
                    $length = $matches[2];
                }
            }

            if (Arr::hasRef($matches, 3)) {
                $unsigned = true;
            }

            if ($matches[1] === 'text') {
                $length = 65535;
            }
        }

        if ($info['Null'] == 'NO') {
            $null = false;
        }

        if ($info['Default'] === '') {
            $info['Default'] = null;
        }

        if (!$null) {
            $info['Default'] = (string)$info['Default'];
        }

        if (Column::isNumericType($type) and (!$null or $info['Default'] !== null)) {
            $info['Default'] = (int)$info['Default'];
        }

        $defaultValue = $info['Default'];

        return compact('name', 'type', 'length', 'unsigned', 'null', 'defaultValue', 'comment', 'values');
    }

    public function tableReferences($tableName)
    {
        $stmt = $this->query('SHOW CREATE TABLE `' . $tableName . '`');

        $sql = $stmt->fetchColumn('Create Table');

        return $this->newTableReferences($sql);
    }

    protected function newTableReferences($sql)
    {
        $references = [];

        foreach($this->parseTableReferences($sql) as $info) {
            $reference = new Constraint();

            $reference->setName($info['ConstraintName'])
                ->setReferencedTableName($info['ReferencedTableName'])
                ->setOnUpdate($info['OnUpdate'])
                ->setOnDelete($info['OnDelete']);

            foreach($info['Constraint'] as $constraintInfo) {
                $reference->setRelation($constraintInfo['Position'], $constraintInfo['ColumnName'], $constraintInfo['ReferencedColumnName']);
            }

            $references[] = $reference;
        }

        return $references;
    }

    protected function parseTableReferences($sql)
    {
        $tableName = $this->parseTableName($sql);

        $regex = 'CONSTRAINT `(.+)` FOREIGN KEY \((.+)\) REFERENCES `(.+)` \((.+)\) ON DELETE (.+) ON UPDATE (.+)';

        $references = [];

        if (preg_match_all('#' . $regex . '#i', $sql, $matches)) {
            foreach($matches[0] as $k => $match) {
                $constraint = [];

                $columnsNames = Str::splitQuoted($matches[2][$k], ', ', '`');

                $referencesColumnsNames = Str::splitQuoted($matches[4][$k], ', ', '`');

                foreach($columnsNames as $kk => $columnName) {
                    $constraint[$kk + 1] = [
                        'Position' => $kk + 1,
                        'ColumnName' => $columnName,
                        'ReferencedColumnName' => $referencesColumnsNames[$kk],
                    ];
                }

                $references[] = [
                    'ConstraintName' => $matches[1][$k],
                    'TableName' => $tableName,
                    'ReferencedTableName' => $matches[3][$k],
                    'OnUpdate' => $matches[5][$k],
                    'OnDelete' => $matches[6][$k],
                    'Constraint' => $constraint,
                ];
            }
        }

        return $references;
    }

    protected function parseTableName($sql)
    {
        if (!preg_match('#^CREATE TABLE `(.+)`#', $sql, $tableMatches)) {
            throw new \Exception('Wrong create table sql.');
        }

        return $tableMatches[1];
    }

    public function tableRelationships($tableName, $withRules = false)
    {
        $query = $this->select()
            ->from(['KCU' => 'information_schema.KEY_COLUMN_USAGE'], [
                'TABLE_SCHEMA',
                'TABLE_NAME',
                'COLUMN_NAME',
                'CONSTRAINT_NAME',
                'ORDINAL_POSITION',
                'POSITION_IN_UNIQUE_CONSTRAINT',
                'REFERENCED_TABLE_SCHEMA',
                'REFERENCED_TABLE_NAME',
                'REFERENCED_COLUMN_NAME',
            ])
            ->from(['TC' => 'information_schema.TABLE_CONSTRAINTS'], 'CONSTRAINT_TYPE')
            ->whereRel('KCU.TABLE_SCHEMA', 'TC.TABLE_SCHEMA')
            ->whereRel('KCU.TABLE_NAME', 'TC.TABLE_NAME')
            ->whereRel('KCU.CONSTRAINT_NAME', 'TC.CONSTRAINT_NAME')

            ->order('KCU.TABLE_SCHEMA')
            ->order('KCU.TABLE_NAME')
            ->order('KCU.CONSTRAINT_NAME')
            ->order('KCU.ORDINAL_POSITION')
            ->order('KCU.POSITION_IN_UNIQUE_CONSTRAINT');

        if ($withRules) {
            $query->from(['RC' => 'information_schema.REFERENTIAL_CONSTRAINTS'], [
                    'UPDATE_RULE',
                    'DELETE_RULE',
                ])
                ->whereRel('KCU.CONSTRAINT_SCHEMA', 'RC.CONSTRAINT_SCHEMA')
                ->whereRel('KCU.CONSTRAINT_NAME', 'RC.CONSTRAINT_NAME');
        }

        $query->where('TC.CONSTRAINT_TYPE', 'FOREIGN KEY');

        $query->where('KCU.REFERENCED_TABLE_SCHEMA', $this->dbName());

        $query->where('KCU.REFERENCED_TABLE_NAME', $tableName);

        $items = $query->assocAll();

        return $this->parseTableRelationshipsAsArray($items, $withRules);
    }

    protected function parseTableRelationships(array $items, $withRules = false)
    {
        $relationships = [];

        foreach($this->parseTableRelationshipsAsArray($items, $withRules) as $relationship) {
            $constraint = new Constraint();

            $constraint->setName($relationship['ConstraintName']);

            $constraint->setReferencedTableName($relationship['RelationshipTableName']);

            if ($withRules) {
                $constraint->setOnUpdate($relationship['OnUpdate']);

                $constraint->setOnDelete($relationship['OnDelete']);
            }

            foreach($relationship['Constraint'] as $relation) {
                $constraint->setRelation($relation['Position'], $relation['ColumnName'], $relation['RelationshipColumnName']);
            }

            $relationships[] = $constraint;
        }

        return $relationships;
    }

    protected function parseTableRelationshipsAsArray(array $items, $withRules = false)
    {
        $relationships = [];

        foreach($items as $item) {
            if (!isset($relationships[$item['CONSTRAINT_NAME']])) {
                $relationships[$item['CONSTRAINT_NAME']] = [
                    'ConstraintName' => $item['CONSTRAINT_NAME'],
                    'DbName' => $item['REFERENCED_TABLE_SCHEMA'],
                    'TableName' => $item['REFERENCED_TABLE_NAME'],
                    'RelationshipTableSchema' => $item['TABLE_SCHEMA'],
                    'RelationshipTableName' => $item['TABLE_NAME'],
                ];

                if ($withRules) {
                    $relationships[$item['CONSTRAINT_NAME']]['OnUpdate'] = $item['UPDATE_RULE'];
                    $relationships[$item['CONSTRAINT_NAME']]['OnDelete'] = $item['DELETE_RULE'];
                }
            }

            $relationships[$item['CONSTRAINT_NAME']]['Constraint'][$item['POSITION_IN_UNIQUE_CONSTRAINT']] = [
                'Position' => $item['POSITION_IN_UNIQUE_CONSTRAINT'],
                'ColumnName' => $item['REFERENCED_COLUMN_NAME'],
                'RelationshipColumnName' => $item['COLUMN_NAME'],
            ];
        }

        return $relationships;
    }

    public function transaction(callable $callable)
    {
        return $this->getAdapter()->transaction($callable);
    }

    public function inTransaction()
    {
        return $this->getAdapter()->inTransaction();
    }

    public function beginTransaction()
    {
        return $this->getAdapter()->beginTransaction();
    }

    public function commit()
    {
        return $this->getAdapter()->commit();
    }

    public function rollBack()
    {
        return $this->getAdapter()->rollBack();
    }

    public function prepare($sql)
    {
        return $this->getAdapter()->prepare($sql);
    }

    public function query($sql)
    {
        return $this->getAdapter()->query($sql);
    }

    public function exec($sql)
    {
        return $this->getAdapter()->exec($sql);
    }

    public function truncate($name)
    {
        return $this->exec('TRUNCATE ' . $name);
    }

    public function lastInsertId($sequenceId = null)
    {
        return $this->getAdapter()->lastInsertId($sequenceId);
    }

    public function quote($value)
    {
        return $this->getAdapter()->quote($value);
    }

    public function listen(callable $callable)
    {
        return $this->getAdapter()->listen($callable);
    }

    public function fire($sql)
    {
        return $this->getAdapter()->fire($sql);
    }
}