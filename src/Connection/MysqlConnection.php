<?php

namespace Greg\Orm\Connection;

use Greg\Orm\Dialect\MysqlDialect;
use Greg\Orm\Dialect\SqlDialect;

class MysqlConnection extends PdoConnectionAbstract
{
    private $pdo;

    private $dialect;

    public function __construct(Pdo $pdo, MysqlDialect $dialect = null)
    {
        $this->pdo = $pdo;

        if (!$dialect) {
            $dialect = new MysqlDialect();
        }

        $this->dialect = $dialect;

        return $this;
    }

    public function pdo(): Pdo
    {
        return $this->pdo;
    }

    public function dialect(): SqlDialect
    {
        return $this->dialect;
    }

    /**
     * @param string $tableName
     *
     * @return int
     */
    public function truncate(string $tableName): int
    {
        return $this->execute('TRUNCATE ' . $this->dialect->quoteTable($tableName));
    }

    protected function describeTable(string $tableName): array
    {
        $records = $this->fetchAll('DESCRIBE ' . $this->dialect->quoteTable($tableName));

        $columns = $primary = [];

        foreach ($records as $record) {
            if ($record['Key'] == 'PRI') {
                $primary[] = $record['Field'];
            }

            preg_match("#^(?'type'[a-z]+)(?:\((?'length'.+?)\))?(?: (?'unsigned'unsigned))?#i", $record['Type'], $matches);

            $extra = [
                'isInt'     => in_array($matches['type'], ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'bool', 'boolean']),
                'isFloat'   => in_array($matches['type'], ['float', 'double', 'double precision', 'real', 'decimal']),
                'isNumeric' => in_array($matches['type'], ['numeric']),
            ];

            if ($record['Extra'] == 'auto_increment') {
                $extra['autoIncrement'] = true;
            }

            if ($length = $matches['length'] ?? null) {
                if (in_array($matches['type'], ['enum', 'set'])) {
                    $extra['values'] = str_getcsv($length, ',', "'");
                } else {
                    $extra['length'] = $length;
                }
            }

            if (($matches['unsigned'] ?? null) === 'unsigned') {
                $extra['unsigned'] = true;
            }

            $columns[$record['Field']] = [
                'name'    => $record['Field'],
                'type'    => $matches['type'],
                'null'    => $record['Null'] === 'YES',
                'default' => $record['Default'] === '' ? null : $record['Default'],
                'extra'   => $extra,
            ];
        }

        return compact('columns', 'primary');
    }
}
