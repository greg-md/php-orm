<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Column;
use Greg\Orm\Constraint;
use Greg\Orm\Driver\Mysql\Query\MysqlDeleteQuery;
use Greg\Orm\Driver\Mysql\Query\MysqlInsertQuery;
use Greg\Orm\Driver\Mysql\Query\MysqlQuerySupport;
use Greg\Orm\Driver\Mysql\Query\MysqlSelectQuery;
use Greg\Orm\Driver\Mysql\Query\MysqlUpdateQuery;
use Greg\Orm\Query\FromClause;
use Greg\Orm\Query\HavingClause;
use Greg\Orm\Query\JoinClause;
use Greg\Orm\Query\LimitClause;
use Greg\Orm\Query\OrderByClause;
use Greg\Orm\Query\WhereClause;
use Greg\Support\Arr;
use Greg\Support\Str;

class Mysql extends DriverAbstract implements MysqlInterface
{
    use PdoDriverTrait;

    private $dsn = [];

    private $username = null;

    private $password = null;

    private $options = [];

    private $tablesInfo = [];

    private $connector = null;

    public function __construct($dsn, $username, $password = null, array $options = [])
    {
        if (!is_array($dsn)) {
            $dsnString = $dsn;

            $dsn = [];

            foreach(explode(';', $dsnString) as $info) {
                list($key, $value) = explode('=', $info, 2);

                $dsn[$key] = $value;
            }
        }

        $this->dsn = $dsn;

        $this->username = $username;

        $this->password = $password;

        $this->options = $options;

        return $this;
    }

    protected function dsnToString()
    {
        $dsn = $this->dsn;

        foreach($dsn as $key => &$value) {
            $value = $key . '=' . $value;
        }
        unset($value);

        return implode(';', $dsn);
    }

    public function dsn($name = null)
    {
        if (func_num_args()) {
            return Arr::getRef($this->dsn, $name);
        }

        return $this->dsn;
    }

    public function dbName()
    {
        return $this->dsn('dbname');
    }

    public function charset()
    {
        return $this->dsn('charset');
    }

    public function connector()
    {
        if (!$this->connector) {
            $this->connector = new \PDO('mysql:' . $this->dsnToString(), $this->username, $this->password, $this->options);

            $this->connector->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->connector->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

            $this->connector->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
        }

        return $this->connector;
    }

    public function reconnect()
    {
        $this->connector = null;

        return $this;
    }

    public function truncate($tableName)
    {
        return $this->exec('TRUNCATE ' . $tableName);
    }

    protected function newPdoStmt(\PDOStatement $stmt)
    {
        return new PdoStmt($stmt, $this);
    }

    public function select($column = null, $_ = null)
    {
        $query = new MysqlSelectQuery();

        if ($columns = is_array($column) ? $column : func_get_args()) {
            $query->columns($columns);
        }

        return $query;
    }

    public function insert($into = null)
    {
        $query = new MysqlInsertQuery();

        if ($into !== null) {
            $query->into($into);
        }

        return $query;
    }

    public function delete($from = null)
    {
        $query = new MysqlDeleteQuery();

        if ($from !== null) {
            $query->from($from);
        }

        return $query;
    }

    public function update($table = null)
    {
        $query = new MysqlUpdateQuery();

        if ($table !== null) {
            $query->table($table);
        }

        return $query;
    }

    public function from()
    {
        return new FromClause();
    }

    public function join()
    {
        return new JoinClause();
    }

    public function where()
    {
        return new WhereClause();
    }

    public function having()
    {
        return new HavingClause();
    }

    public function orderBy()
    {
        return new OrderByClause();
    }

    public function limit()
    {
        return new LimitClause();
    }

    static public function quoteLike($value, $escape = '\\')
    {
        return MysqlQuerySupport::quoteLike($value, $escape);
    }

    static public function concat(array $values, $delimiter = '')
    {
        return MysqlQuerySupport::concat($values, $delimiter);
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
                ->onUpdate($info['OnUpdate'])
                ->onDelete($info['OnDelete']);

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

            ->orderBy('KCU.TABLE_SCHEMA')
            ->orderBy('KCU.TABLE_NAME')
            ->orderBy('KCU.CONSTRAINT_NAME')
            ->orderBy('KCU.ORDINAL_POSITION')
            ->orderBy('KCU.POSITION_IN_UNIQUE_CONSTRAINT');

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

        $recors = $this->prepare($query->toSql())->fetchAssocAll();

        return $this->parseTableRelationshipsAsArray($recors, $withRules);
    }

    protected function parseTableRelationships(array $items, $withRules = false)
    {
        $relationships = [];

        foreach($this->parseTableRelationshipsAsArray($items, $withRules) as $relationship) {
            $constraint = new Constraint();

            $constraint->setName($relationship['ConstraintName']);

            $constraint->setReferencedTableName($relationship['RelationshipTableName']);

            if ($withRules) {
                $constraint->onUpdate($relationship['OnUpdate']);

                $constraint->onDelete($relationship['OnDelete']);
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
}