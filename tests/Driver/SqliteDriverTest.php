<?php

namespace Greg\Orm\Driver;

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

class SqliteDriverTest extends TestCase
{
    use PdoMock;

    /**
     * @var SqliteDriver
     */
    private $driver;

    public function setUp()
    {
        parent::setUp();

        $this->initPdoMock();

        $this->driver = new SqliteDriver($this->pdoConnector);
    }

    public function testCanConnect()
    {
        $this->pdoConnector->expects($this->once())->method('connect');

        $this->driver->connect();
    }

    public function testCanGetConnection()
    {
        $this->assertInstanceOf(\PDO::class, $this->driver->connection());
    }

    public function testCanExecInitEvent()
    {
        $used = false;

        $this->driver->onInit(function () use (&$used) {
            $used = true;
        });

        $this->driver->connect();

        $this->assertTrue($used);
    }

    public function testCanReconnectIfConnectionExpired()
    {
        /** @var \PDOException $e */
        $e = $this->getMockBuilder(\PDOException::class)
            ->disableOriginalConstructor()
            ->getMock();

        $e->errorInfo = ['Error', 2006, 'Expired'];

        $this->pdoMock
            ->expects($this->exactly(2))
            ->method('prepare')
            ->will($this->onConsecutiveCalls($this->throwException($e), $this->pdoStatementMock));

        $this->driver->fetch('SELECT 1');
    }

    public function testCanCommitTransaction()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('beginTransaction');

        $this->pdoMock->expects($this->once())->method('commit');

        $this->driver->transaction(function () {
        });
    }

    public function testCanRollbackTransaction()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('beginTransaction');

        $this->pdoMock->expects($this->once())->method('rollBack');

        $this->expectException(\Exception::class);

        $this->driver->transaction(function () {
            throw new \Exception('Call rollback.');
        });
    }

    public function testCanDetermineIfInTransaction()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('inTransaction');

        $this->assertFalse($this->driver->inTransaction());
    }

    public function testCanBeginTransaction()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('beginTransaction');

        $this->assertTrue($this->driver->beginTransaction());
    }

    public function testCanCommit()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('commit');

        $this->driver->beginTransaction();

        $this->assertTrue($this->driver->commit());
    }

    public function testCanRollback()
    {
        $this->mockTransactions();

        $this->pdoMock->expects($this->once())->method('rollBack');

        $this->driver->beginTransaction();

        $this->assertTrue($this->driver->rollBack());
    }

    public function testCanExecute()
    {
        $this->mockStatements();

        $this->pdoStatementMock->expects($this->once())->method('execute');

        $this->pdoStatementMock->expects($this->once())->method('rowCount')->willReturn(1);

        $this->assertEquals(1, $this->driver->execute('INSERT INTO `Table` (`Column`) VALUES ("foo")'));
    }

    public function testCanGetLastInsertId()
    {
        $this->pdoMock->expects($this->once())->method('lastInsertId')->willReturn(1);

        $this->assertEquals(1, $this->driver->lastInsertId());
    }

    public function testCanQuote()
    {
        $this->pdoMock->method('quote')->willReturn('"foo"');

        $this->assertEquals('"foo"', $this->driver->quote('foo'));
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

        $this->assertEquals([1], $this->driver->fetch('SELECT foo WHERE bar = ?', [1]));
    }

    public function testCanFetchAll()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn([[1], [1]]);

        $this->assertEquals([[1], [1]], $this->driver->fetchAll('SELECT foo'));
    }

    public function testCanFetchYield()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls([1], [1], false);

        $generator = $this->driver->fetchYield('SELECT foo');

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

        $this->assertEquals(1, $this->driver->column('SELECT foo', [], 0));
    }

    public function testCanFetchColumnByName()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->once())
            ->method('fetch')
            ->willReturn(['foo' => 1]);

        $this->assertEquals(1, $this->driver->column('SELECT foo', [], 'foo'));
    }

    public function testCanFetchColumnAllByNumber()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetchColumn')
            ->willReturnOnConsecutiveCalls(1, 1, false);

        $this->assertEquals([1, 1], $this->driver->columnAll('SELECT foo', [], 0));
    }

    public function testCanFetchColumnAllByName()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturn(['foo' => 1], ['foo' => 1], false);

        $this->assertEquals([1, 1], $this->driver->columnAll('SELECT foo', [], 'foo'));
    }

    public function testCanFetchColumnYieldByNumber()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetchColumn')
            ->willReturnOnConsecutiveCalls(1, 1, false);

        $generator = $this->driver->columnYield('SELECT foo', [], 0);

        $this->assertInstanceOf(\Generator::class, $generator);

        foreach ($generator as $record) {
            $this->assertEquals(1, $record);
        }
    }

    public function testCanFetchColumnYieldByName()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls(['foo' => 1], ['foo' => 1], false);

        $generator = $this->driver->columnYield('SELECT foo', [], 'foo');

        $this->assertInstanceOf(\Generator::class, $generator);

        foreach ($generator as $record) {
            $this->assertEquals(1, $record);
        }
    }

    public function testCanFetchPairs()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls([1, 1], [2, 2], false);

        $this->assertEquals([1 => 1, 2 => 2], $this->driver->pairs('SELECT foo, bar'));
    }

    public function testCanFetchPairsYield()
    {
        $this->mockStatements();

        $this->pdoStatementMock
            ->expects($this->exactly(3))
            ->method('fetch')
            ->willReturnOnConsecutiveCalls([1, 1], [2, 2], false);

        $generator = $this->driver->pairsYield('SELECT foo, bar');

        $result = [[1 => 1], [2 => 2]];

        foreach ($generator as $key => $value) {
            $this->assertEquals(array_shift($result), [$key => $value]);
        }
    }

    public function testCanThrowPDOExceptionIfStatementIsWrong()
    {
        $this->pdoMock->method('prepare')->willReturn(false);

        $this->pdoMock->method('errorInfo')->willReturn(['ERROR', 1, 'Something wrong.']);

        $this->expectException(\PDOException::class);

        $this->driver->fetch('SELECT error');
    }

    public function testCanTruncate()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->assertEquals(1, $this->driver->truncate('Table'));
    }

    public function testCanListen()
    {
        $this->mockStatements();

        $mocker = $this->getMockBuilder('Foo')
            ->setMethods(['call'])
            ->getMock();

        $mocker->expects($this->exactly(2))->method('call');

        $this->driver->listen([$mocker, 'call']);

        $this->driver->fetch('select 1');

        $this->driver->fetch('select 1');
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

        $schema = $this->driver->describe('Table');

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
        $this->assertInstanceOf($class, $this->driver->{$name}());
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
