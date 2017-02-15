<?php

namespace Greg\Orm\Driver\Mysql;

use Greg\Orm\DialectStrategy;
use Greg\Orm\Driver\PdoDriverAbstract;

class MysqlDriver extends PdoDriverAbstract
{
    //private $schema = [];

    /**
     * @var DialectStrategy|null
     */
    private $dialect;

    /**
     * @return DialectStrategy
     */
    public function dialect(): DialectStrategy
    {
        if (!$this->dialect) {
            $this->dialect = new MysqlDialect();
        }

        return $this->dialect;
    }

    /**
     * @param string $tableName
     *
     * @return int
     */
    public function truncate(string $tableName)
    {
        return $this->exec('TRUNCATE ' . $tableName);
    }

    /*
    public function dnName(): string
    {
        if (!$name = $this->dsn('dbname')) {
            $name = $this->query('select database()')->fetchColumn();
        }

        return $name;
    }

    public function tableInfo(string $tableName, bool $save = true): array
    {
        if (!$save) {
            return $this->fetchTableInfo($tableName);
        }

        if (!array_key_exists($tableName, $this->schema)) {
            $this->schema[$tableName] = $this->fetchTableInfo($tableName);
        }

        return $this->schema[$tableName];
    }

    public function tableReferences(string $tableName): array
    {
        $stmt = $this->query('SHOW CREATE TABLE `' . $tableName . '`');

        $sql = $stmt->fetchColumn('Create Table');

        return $this->newTableReferences($sql);
    }

    public function tableRelationships(string $tableName, bool $withRules = false): array
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

        $records = $this->prepare($query->toSql())->fetchAssocAll();

        return $this->parseTableRelationshipsAsArray($records, $withRules);
    }

    protected function fetchTableInfo(string $tableName): array
    {
        $stmt = $this->query('Describe `' . $tableName . '`');

        $columnsInfo = $stmt->fetchAssocAll();

        $primaryKeys = [];

        $autoIncrement = null;

        $columns = [];

        foreach ($columnsInfo as $info) {
            if ($info['Key'] == 'PRI') {
                $primaryKeys[] = $info['Field'];
            }

            if ($info['Extra'] == 'auto_increment') {
                $autoIncrement = $info['Field'];
            }

            $columns[] = $this->newColumnInfo($info);
        }

        return [
            'columns'       => $columns,
            'primaryKeys'   => $primaryKeys,
            'autoIncrement' => $autoIncrement,
        ];
    }

    protected function newColumnInfo(array $info): Column
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

    protected function parseColumnInfo(array $info): array
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

            if (Arr::has($matches, 2)) {
                if ($matches[1] === 'enum') {
                    $values = str_getcsv($matches[2], ',', "'");
                } else {
                    $length = $matches[2];
                }
            }

            if (Arr::has($matches, 3)) {
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
            $info['Default'] = (string) $info['Default'];
        }

        if (Column::isNumericType($type) and (!$null or $info['Default'] !== null)) {
            $info['Default'] = (int) $info['Default'];
        }

        $defaultValue = $info['Default'];

        return compact('name', 'type', 'length', 'unsigned', 'null', 'defaultValue', 'comment', 'values');
    }

    protected function newTableReferences(string $sql): array
    {
        $references = [];

        foreach ($this->parseTableReferences($sql) as $info) {
            $reference = new Constraint();

            $reference->setName($info['ConstraintName'])
                ->setReferencedTableName($info['ReferencedTableName'])
                ->onUpdate($info['OnUpdate'])
                ->onDelete($info['OnDelete']);

            foreach ($info['Constraint'] as $constraintInfo) {
                $reference->setRelation($constraintInfo['Position'], $constraintInfo['ColumnName'], $constraintInfo['ReferencedColumnName']);
            }

            $references[] = $reference;
        }

        return $references;
    }

    protected function parseTableReferences(string $sql): array
    {
        $tableName = $this->parseTableName($sql);

        $regex = 'CONSTRAINT `(.+)` FOREIGN KEY \((.+)\) REFERENCES `(.+)` \((.+)\) ON DELETE (.+) ON UPDATE (.+)';

        $references = [];

        if (preg_match_all('#' . $regex . '#i', $sql, $matches)) {
            foreach ($matches[0] as $k => $match) {
                $constraint = [];

                $columnsNames = Str::splitQuoted($matches[2][$k], ', ', '`');

                $referencesColumnsNames = Str::splitQuoted($matches[4][$k], ', ', '`');

                foreach ($columnsNames as $kk => $columnName) {
                    $constraint[$kk + 1] = [
                        'Position'             => $kk + 1,
                        'ColumnName'           => $columnName,
                        'ReferencedColumnName' => $referencesColumnsNames[$kk],
                    ];
                }

                $references[] = [
                    'ConstraintName'      => $matches[1][$k],
                    'TableName'           => $tableName,
                    'ReferencedTableName' => $matches[3][$k],
                    'OnUpdate'            => $matches[5][$k],
                    'OnDelete'            => $matches[6][$k],
                    'Constraint'          => $constraint,
                ];
            }
        }

        return $references;
    }

    protected function parseTableName(string $sql): string
    {
        if (!preg_match('#^CREATE TABLE `(.+)`#', $sql, $tableMatches)) {
            throw new \Exception('Wrong create table sql.');
        }

        return $tableMatches[1];
    }


    protected function parseTableRelationships(array $items, bool $withRules = false): array
    {
        $relationships = [];

        foreach ($this->parseTableRelationshipsAsArray($items, $withRules) as $relationship) {
            $constraint = new Constraint();

            $constraint->setName($relationship['ConstraintName']);

            $constraint->setReferencedTableName($relationship['RelationshipTableName']);

            if ($withRules) {
                $constraint->onUpdate($relationship['OnUpdate']);

                $constraint->onDelete($relationship['OnDelete']);
            }

            foreach ($relationship['Constraint'] as $relation) {
                $constraint->setRelation($relation['Position'], $relation['ColumnName'], $relation['RelationshipColumnName']);
            }

            $relationships[] = $constraint;
        }

        return $relationships;
    }

    protected function parseTableRelationshipsAsArray(array $items, bool $withRules = false): array
    {
        $relationships = [];

        foreach ($items as $item) {
            if (!isset($relationships[$item['CONSTRAINT_NAME']])) {
                $relationships[$item['CONSTRAINT_NAME']] = [
                    'ConstraintName'          => $item['CONSTRAINT_NAME'],
                    'DbName'                  => $item['REFERENCED_TABLE_SCHEMA'],
                    'TableName'               => $item['REFERENCED_TABLE_NAME'],
                    'RelationshipTableSchema' => $item['TABLE_SCHEMA'],
                    'RelationshipTableName'   => $item['TABLE_NAME'],
                ];

                if ($withRules) {
                    $relationships[$item['CONSTRAINT_NAME']]['OnUpdate'] = $item['UPDATE_RULE'];
                    $relationships[$item['CONSTRAINT_NAME']]['OnDelete'] = $item['DELETE_RULE'];
                }
            }

            $relationships[$item['CONSTRAINT_NAME']]['Constraint'][$item['POSITION_IN_UNIQUE_CONSTRAINT']] = [
                'Position'               => $item['POSITION_IN_UNIQUE_CONSTRAINT'],
                'ColumnName'             => $item['REFERENCED_COLUMN_NAME'],
                'RelationshipColumnName' => $item['COLUMN_NAME'],
            ];
        }

        return $relationships;
    }
    */
}
