<?php

namespace Greg\Orm\Tests\Driver\Mysql;

use Greg\Orm\Driver\Mysql\MysqlDriver;
use Greg\Orm\Tests\Driver\PdoDriverAbstract;

class MysqlDriverTest extends PdoDriverAbstract
{
    protected $driver = MysqlDriver::class;

    public function testCanDescribeTable()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchAll')->willReturn([
            [
                'Field'   => 'Id',
                'Type'    => 'int(10) unsigned',
                'Null'    => 'NO',
                'Key'     => 'PRI',
                'Default' => '',
                'Extra'   => 'auto_increment',
            ],
            [
                'Field'   => 'Gender',
                'Type'    => 'enum(\'male\',\'female\')',
                'Null'    => 'YES',
                'Key'     => '',
                'Default' => '',
                'Extra'   => '',
            ],
        ]);

        $schema = $this->db->describe('Table');

        $this->assertEquals([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'autoIncrement' => true,
                        'length'        => 10,
                        'unsigned'      => true,
                    ],
                ],
                'Gender' => [
                    'name'    => 'Gender',
                    'type'    => 'enum',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'   => false,
                        'isFloat' => false,
                        'values'  => ['male', 'female'],
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ], $schema);
    }
}
