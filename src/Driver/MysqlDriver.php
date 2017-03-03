<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Dialect\DialectStrategy;
use Greg\Orm\Dialect\MysqlDialect;

class MysqlDriver extends PdoDriverAbstract
{
    private $connector;

    private $dialect;

    public function __construct(PdoConnectorStrategy $strategy, DialectStrategy $dialect = null)
    {
        $this->connector = $strategy;

        if (!$dialect) {
            $dialect = new MysqlDialect();
        }

        $this->dialect = $dialect;

        return $this;
    }

    public function dialect(): DialectStrategy
    {
        return $this->dialect;
    }

    /**
     * @param string $tableName
     *
     * @return int
     */
    public function truncate(string $tableName)
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
                'isInt'   => in_array($matches['type'], ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'bool', 'boolean']),
                'isFloat' => in_array($matches['type'], ['float', 'double', 'double precision', 'real', 'decimal']),
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

    protected function connector(): PdoConnectorStrategy
    {
        return $this->connector;
    }
}
