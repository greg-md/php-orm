<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Dialect\DialectStrategy;
use Greg\Orm\Dialect\SqliteDialect;

class SqliteDriver extends PdoDriverAbstract
{
    private $pdo;

    private $dialect;

    public function __construct(Pdo $pdo, DialectStrategy $dialect = null)
    {
        $this->pdo = $pdo;

        if (!$dialect) {
            $dialect = new SqliteDialect();
        }

        $this->dialect = $dialect;

        return $this;
    }

    public function pdo(): Pdo
    {
        return $this->pdo;
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
    public function truncate(string $tableName): int
    {
        return $this->execute('TRUNCATE ' . $this->dialect()->quoteTable($tableName));
    }

    protected function describeTable(string $tableName): array
    {
        $records = $this->fetchAll('PRAGMA TABLE_INFO(' . $this->dialect()->quoteTable($tableName) . ')');

        $columns = $primary = [];

        foreach ($records as $record) {
            if ($record['pk']) {
                $primary[] = $record['name'];
            }

            $record['type'] = strtolower($record['type']);

            $extra = [
                'isInt'     => in_array($record['type'], ['integer']),
                'isFloat'   => in_array($record['type'], ['real']),
                'isNumeric' => in_array($record['type'], ['numeric']),
            ];

            $columns[$record['name']] = [
                'name'    => $record['name'],
                'type'    => $record['type'],
                'null'    => !$record['notnull'],
                'default' => $record['dflt_value'] === '' ? null : $record['dflt_value'],
                'extra'   => $extra,
            ];
        }

        return [
            'columns' => $columns,
            'primary' => $primary,
        ];
    }
}
