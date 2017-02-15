<?php

namespace Greg\Orm\Tests\Driver;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Driver\PdoConnectorStrategy;
use Greg\Orm\Driver\PdoDriverStrategy;
use Greg\Orm\Driver\StatementStrategy;

class PdoDriverAbstract extends DriverAbstract
{
    /**
     * @var DriverStrategy
     */
    protected $driver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pdoMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pdoStatementMock;

    /**
     * @var PdoDriverStrategy
     */
    protected $db;

    public function setUp()
    {
        parent::setUp();

        $this->pdoMock = $pdoMock = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pdoStatementMock = $this->getMockBuilder(\PDOStatement::class)
            ->getMock();

        $this->db = new $this->driver(new class($pdoMock) implements PdoConnectorStrategy {
            /**
             * @var
             */
            private $mock;

            public function __construct($mock)
            {
                $this->mock = $mock;
            }

            public function connect(): \PDO
            {
                return $this->mock;
            }
        });
    }

    public function testCanExecInitEvent()
    {
        $used = false;

        $this->db->onInit(function () use (&$used) {
            $used = true;
        });

        $this->db->connect();

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
            ->method('query')
            ->will($this->onConsecutiveCalls($this->throwException($e), $this->pdoStatementMock));

        $this->db->query('SELECT 1');
    }

    public function testCanThrowPDOException()
    {
        $this->pdoMock->method('getAttribute')->will($this->throwException(new \PDOException()));

        $this->expectException(\PDOException::class);

        $this->db->connection()->getAttribute('lol');
    }

    public function testCanThrowPDOExceptionIfStatementIsWrong()
    {
        $this->pdoMock->method('query')->willReturn(false);

        $this->pdoMock->method('errorInfo')->willReturn(['ERROR', 1, 'Something wrong.']);

        $this->expectException(\PDOException::class);

        $this->db->query('SELECT error');
    }

    public function testCanExec()
    {
        $this->pdoMock->method('exec')->willReturn(1);

        $this->assertEquals(1, $this->db->exec('INSERT INTO `Table` (`Column`) VALUES ("foo")'));
    }

    public function testCanGetLastInsertId()
    {
        $this->pdoMock->method('exec')->willReturn(1);

        $this->db->exec('INSERT INTO `Table` (`Column`) VALUES ("foo")');

        $this->pdoMock->method('lastInsertId')->willReturn(1);

        $this->assertEquals(1, $this->db->lastInsertId());
    }

    public function testCanQuote()
    {
        $this->pdoMock->method('quote')->willReturn('"foo"');

        $this->assertEquals('"foo"', $this->db->quote('foo'));
    }

    public function testCanTruncate()
    {
        $this->pdoMock->method('exec')->willReturn(1);

        $this->assertEquals(1, $this->db->truncate('Table'));
    }

    public function testCanListenQueries()
    {
        $this->mockStatements();

        $called = false;

        $this->db->listen(function () use (&$called) {
            $called = true;
        });

        $this->db->query('SELECT 1');

        $this->assertTrue($called);
    }

    public function testCanPrepareAStatement()
    {
        $this->mockStatements();

        $stmt = $this->db->prepare('SELECT 1');

        $this->assertInstanceOf(StatementStrategy::class, $stmt);
    }

    public function testCanExecInTransaction()
    {
        $this->mockTransactions();

        $inTransaction = false;

        $this->db->transaction(function (DriverStrategy $db) use (&$inTransaction) {
            $inTransaction = $db->inTransaction();
        });

        $this->assertTrue($inTransaction);
    }

    public function testCanRollback()
    {
        $this->mockTransactions();

        $this->db->beginTransaction();

        $this->assertTrue($this->db->rollBack());
    }

    public function testCanTestQuery()
    {
        $this->mockStatements();

        $this->assertInstanceOf(StatementStrategy::class, $this->db->query('SELECT 1'));
    }

    protected function mockStatements()
    {
        $this->pdoMock->method('prepare')->willReturn($this->pdoStatementMock);

        $this->pdoMock->method('query')->willReturn($this->pdoStatementMock);

        return $this;
    }

    protected function mockTransactions()
    {
        $pdoTransaction = false;

        $this->pdoMock->method('beginTransaction')->will($this->returnCallback(function () use (&$pdoTransaction) {
            if ($pdoTransaction) {
                throw new \Exception('Transaction already initialised');
            }

            $pdoTransaction = true;

            return true;
        }));

        $this->pdoMock->method('inTransaction')->will($this->returnCallback(function () use (&$pdoTransaction) {
            return $pdoTransaction;
        }));

        $this->pdoMock->method('commit')->will($this->returnCallback(function () use (&$pdoTransaction) {
            if (!$pdoTransaction) {
                throw new \Exception('Transaction is not initialised');
            }

            $pdoTransaction = false;

            return true;
        }));

        $this->pdoMock->method('rollback')->will($this->returnCallback(function () use (&$pdoTransaction) {
            if (!$pdoTransaction) {
                throw new \Exception('Transaction is not initialised');
            }

            $pdoTransaction = false;

            return true;
        }));

        return $this;
    }
}
