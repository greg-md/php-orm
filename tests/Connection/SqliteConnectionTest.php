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

class SqliteConnectionTest extends TestCase
{
    use PdoMock;

    /**
     * @var SqliteConnection
     */
    private $connection;

    public function setUp()
    {
        parent::setUp();

        $this->initPdoMock();

        $this->connection = new SqliteConnection($this->pdoMock);
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

        $this->assertEquals(1, $this->connection->execute('INSERT INTO `Table` (`Column`) VALUES ("foo")'));
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

        $this->assertEquals([1], $this->connection->fetch('SELECT foo WHERE bar = ?', [1]));
    }

    public function testCanFetchAll()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn([[1], [1]]);

        $this->assertEquals([[1], [1]], $this->connection->fetchAll('SELECT foo'));
    }

    public function testCanGenerate()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls([1], [1], false);

        $generator = $this->connection->generate('SELECT foo');

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

        $this->assertEquals(1, $this->connection->column('SELECT foo', [], 0));
    }

    public function testCanFetchColumnByName()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(['foo' => 1]);

        $this->assertEquals(1, $this->connection->column('SELECT foo', [], 'foo'));
    }

    public function testCanFetchColumnAllByNumber()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetchColumn')
            ->willReturnOnConsecutiveCalls(1, 1, false);

        $this->assertEquals([1, 1], $this->connection->columnAll('SELECT foo', [], 0));
    }

    public function testCanFetchColumnAllByName()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturn(['foo' => 1], ['foo' => 1], false);

        $this->assertEquals([1, 1], $this->connection->columnAll('SELECT foo', [], 'foo'));
    }

    public function testCanFetchPairs()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls([1, 1], [2, 2], false);

        $this->assertEquals([1 => 1, 2 => 2], $this->connection->pairs('SELECT foo, bar'));
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

        $this->connection->fetch('select 1');

        $this->connection->fetch('select 1');
    }

    public function testCanDescribeTable()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchAll')->willReturn([
            [
                'cid'        => '0',
                'name'       => 'Id',
                'type'       => 'INTEGER',
                'notnull'    => '1',
                'dflt_value' => '',
                'pk'         => '1',
            ],
            [
                'cid'        => '1',
                'name'       => 'Username',
                'type'       => 'BLOB',
                'notnull'    => '1',
                'dflt_value' => '',
                'pk'         => '0',
            ],
        ]);

        $schema = $this->connection->describe('Table');

        $this->assertEquals([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'integer',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'           => true,
                        'isFloat'         => false,
                        'isNumeric'       => false,
                    ],
                ],
                'Username' => [
                    'name'    => 'Username',
                    'type'    => 'blob',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'     => false,
                        'isFloat'   => false,
                        'isNumeric' => false,
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