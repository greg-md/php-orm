<?php

namespace Greg\Orm\Storage;

use Greg\Orm\Adapter\AdapterInterface;
use Greg\Orm\Column;
use Greg\Orm\Query\QueryTrait;
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
class Mysql implements StorageInterface
{
    use StorageAdapterTrait;

    public function __construct($adapter = null)
    {
        if ($adapter) {
            if ($adapter instanceof AdapterInterface) {
                $this->setAdapter($adapter);
            } elseif (is_callable($adapter)) {
                $this->setCallableAdapter($adapter);
            } else {
                throw new \Exception('Wrong Mysql adapter type.');
            }
        }
    }

    public function dbName()
    {
        return $this->getAdapter()->dbName();
    }

    public function getTableSchema($tableName)
    {
        return [
            $this->getTableInfo($tableName),
            $this->getTableReferences($tableName),
            $this->getTableRelationships($tableName)
        ];
    }

    public function getTableInfo($tableName)
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

    public function newColumnInfo(array $info)
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

    public function getTableReferences($tableName)
    {
        $stmt = $this->query('SHOW CREATE TABLE `' . $tableName . '`');

        $sql = $stmt->fetchOne('Create Table');

        return $this->newTableReferences($sql);
    }

    public function newTableReferences($sql)
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

    public function parseTableReferences($sql)
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

    public function parseTableName($sql)
    {
        if (!preg_match('#^CREATE TABLE `(.+)`#', $sql, $tableMatches)) {
            throw new \Exception('Wrong create table sql.');
        }

        return $tableMatches[1];
    }

    public function getTableRelationships($tableName, $rules = false)
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

        if ($rules) {
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

        return $this->parseTableRelationshipsAsArray($items, $rules);
    }

    protected function parseTableRelationships(array $items, $rules = false)
    {
        $relationships = [];

        foreach($this->parseTableRelationshipsAsArray($items, $rules) as $relationship) {
            $constraint = new Constraint();

            $constraint->setName($relationship['ConstraintName']);

            $constraint->setReferencedTableName($relationship['RelationshipTableName']);

            if ($rules) {
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

    protected function parseTableRelationshipsAsArray(array $items, $rules = false)
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

                if ($rules) {
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

    public function select($column = null, $_ = null)
    {
        if (!is_array($column)) {
            $column = func_get_args();
        }

        $query = new MysqlSelectQuery($this);

        if ($column) {
            $query->columns($column);
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

    static public function quoteLike($string, $escape = '\\')
    {
        return QueryTrait::quoteLike($string, $escape);
    }

    static public function concat($array, $delimiter = '')
    {
        return MysqlQueryTrait::concat($array, $delimiter);
    }

    public function beginTransaction()
    {
        return $this->getAdapter()->beginTransaction();
    }

    public function commit()
    {
        return $this->getAdapter()->commit();
    }

    public function errorCode()
    {
        return $this->getAdapter()->errorCode();
    }

    public function errorInfo()
    {
        return $this->getAdapter()->errorInfo();
    }

    public function exec($query)
    {
        return $this->getAdapter()->exec($query);
    }

    public function getAttribute($name)
    {
        return $this->getAdapter()->getAttribute($name);
    }

    public function inTransaction()
    {
        return $this->getAdapter()->inTransaction();
    }

    public function lastInsertId($name = null)
    {
        return $this->getAdapter()->lastInsertId($name);
    }

    public function prepare($query, $options = [])
    {
        return $this->getAdapter()->prepare($query, $options = []);
    }

    public function query($query, $mode = null, $_ = null)
    {
        return call_user_func_array([$this->getAdapter(), 'query'], func_get_args());
    }

    public function quote($string, $type = self::PARAM_STR)
    {
        return $this->getAdapter()->quote($string, $type);
    }

    public function rollBack()
    {
        return $this->getAdapter()->rollBack();
    }

    public function setAttribute($name, $value)
    {
        return $this->getAdapter()->setAttribute($name, $value);
    }
}