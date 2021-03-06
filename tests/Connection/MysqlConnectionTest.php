<?php

namespace Greg\Orm\Connection;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;
use PHPUnit\Framework\TestCase;

class MysqlConnectionTest extends TestCase
{
    use PdoMockTrait;

    /**
     * @var MysqlConnection
     */
    private $connection;

    protected function setUp(): void
    {
        $this->initPdoMock();

        $this->connection = new MysqlConnection($this->pdoMock);
    }

    public function testCanGetConnection()
    {
        $this->assertInstanceOf(Pdo::class, $this->connection->pdo());
    }

    public function testCanCommitTransaction()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('beginTransaction');

        $this->pdoMock->expects($this->once())->method('commit');

        $this->connection->transaction(function () {
        });
    }

    public function testCanRollbackTransaction()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('beginTransaction');

        $this->pdoMock->expects($this->once())->method('rollBack');

        $this->expectException(\Exception::class);

        $this->connection->transaction(function () {
            throw new \Exception('Call rollback.');
        });
    }

    public function testCanDetermineIfInTransaction()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('inTransaction');

        $this->assertFalse($this->connection->inTransaction());
    }

    public function testCanBeginTransaction()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('beginTransaction');

        $this->assertTrue($this->connection->beginTransaction());
    }

    public function testCanCommit()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('commit');

        $this->connection->beginTransaction();

        $this->assertTrue($this->connection->commit());
    }

    public function testCanRollback()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('rollBack');

        $this->connection->beginTransaction();

        $this->assertTrue($this->connection->rollBack());
    }

    public function testCanExecute()
    {
        $this->mockStatements();

        $this->pdoStatementMock->expects($this->once())->method('execute');

        $this->pdoStatementMock->expects($this->once())->method('rowCount')->willReturn(1);

        $this->assertEquals(1, $this->connection->sqlExecute('INSERT INTO `Table` (`Column`) VALUES ("foo")'));
    }

    public function testCanGetLastInsertId()
    {
        $this->pdoMock->expects($this->once())->method('lastInsertId')->willReturn(1);

        $this->assertEquals(1, $this->connection->lastInsertId());
    }

    public function testCanQuote()
    {
        $this->pdoMock->method('quote')->willReturn('"foo"');

        $this->assertEquals('"foo"', $this->connection->quote('foo'));
    }

    public function testCanFetch()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn([1]);

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('bindValue');

        $this->assertEquals([1], $this->connection->sqlFetch('SELECT foo WHERE bar = ?', [1]));
    }

    public function testCanFetchAll()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn([[1], [1]]);

        $this->assertEquals([[1], [1]], $this->connection->sqlFetchAll('SELECT foo'));
    }

    public function testCanGenerate()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls([1], [1], false);

        $generator = $this->connection->sqlGenerate('SELECT foo');

        $this->assertInstanceOf(\Generator::class, $generator);

        foreach ($generator as $record) {
            $this->assertEquals([1], $record);
        }
    }

    public function testCanFetchColumnByNumber()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetchColumn')
            ->willReturn(1);

        $this->assertEquals(1, $this->connection->sqlFetchColumn('SELECT foo', [], 0));
    }

    public function testCanFetchColumnByName()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(['foo' => 1]);

        $this->assertEquals(1, $this->connection->sqlFetchColumn('SELECT foo', [], 'foo'));
    }

    public function testCanFetchColumnAllByNumber()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_COLUMN, 0)
            ->willReturn([1, 1]);

        $this->assertEquals([1, 1], $this->connection->sqlFetchAllColumn('SELECT foo', [], 0));
    }

    public function testCanFetchColumnAllByName()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturn(['foo' => 1], ['foo' => 1], false);

        $this->assertEquals([1, 1], $this->connection->sqlFetchAllColumn('SELECT foo', [], 'foo'));
    }

    public function testCanFetchPairs()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls([1, 1], [2, 2], false);

        $this->assertEquals([1 => 1, 2 => 2], $this->connection->sqlFetchPairs('SELECT foo, bar'));
    }

    public function testCanTruncate()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->assertEquals(1, $this->connection->truncate('Table'));
    }

    public function testCanListen()
    {
        $this->mockStatements();

        $mocker = $this->getMockBuilder('Foo')
            ->setMethods(['call'])
            ->getMock();

        $mocker->expects($this->exactly(2))->method('call');

        $this->connection->listen([$mocker, 'call']);

        $this->connection->sqlFetch('select 1');

        $this->connection->sqlFetch('select 1');
    }

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

        $schema = $this->connection->describe('Table');

        $this->assertEquals([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'           => true,
                        'isFloat'         => false,
                        'isNumeric'       => false,
                        'autoIncrement'   => true,
                        'length'          => 10,
                        'unsigned'        => true,
                    ],
                ],
                'Gender' => [
                    'name'    => 'Gender',
                    'type'    => 'enum',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'     => false,
                        'isFloat'   => false,
                        'isNumeric' => false,
                        'values'    => ['male', 'female'],
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ], $schema);
    }

    /**
     * @dataProvider queries
     *
     * @param $name
     * @param $class
     */
    public function testCanInstanceAQuery($name, $class)
    {
        $this->assertInstanceOf($class, $this->connection->{$name}());
    }

    public function queries()
    {
        yield ['select', SelectQuery::class];
        yield ['insert', InsertQuery::class];
        yield ['delete', DeleteQuery::class];
        yield ['update', UpdateQuery::class];
        yield ['from', FromClause::class];
        yield ['join', JoinClause::class];
        yield ['where', WhereClause::class];
        yield ['having', HavingClause::class];
        yield ['orderBy', OrderByClause::class];
        yield ['groupBy', GroupByClause::class];
        yield ['limit', LimitClause::class];
        yield ['offset', OffsetClause::class];
    }
}
