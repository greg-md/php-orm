<?php

namespace Greg\Orm\Tests\Driver;

use Greg\Orm\Driver\PdoDriverStrategy;
use Greg\Orm\Driver\PdoStatement;
use PHPUnit\Framework\TestCase;

class PdoStatementTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pdoMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pdoStatementMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

    /**
     * @var PdoStatement
     */
    protected $stmt;

    public function setUp()
    {
        parent::setUp();

        $this->pdoMock = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        /* @var \PDOStatement $pdoStatement */
        $this->pdoStatementMock = $pdoStatement = $this->getMockBuilder(\PDOStatement::class)
            ->getMock();

        /* @var PdoDriverStrategy $driver */
        $this->driver = $driver = $this->getMockBuilder(PdoDriverStrategy::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stmt = new PdoStatement($pdoStatement, $driver);
    }

    public function testCanBindParam()
    {
        $this->pdoStatementMock->expects($this->once())->method('bindValue');

        $this->stmt->bind('foo', 'bar');
    }

    public function testCanBindParams()
    {
        $this->pdoStatementMock->expects($this->exactly(2))->method('bindValue');

        $this->stmt->bindMultiple([
            'Foo' => 'foo',
            'Bar' => 'bar',
        ]);
    }

    public function testCanExecute()
    {
        $this->driver->expects($this->once())->method('fire');

        $this->pdoStatementMock->method('execute')->willReturn(true);

        $this->assertTrue($this->stmt->execute());
    }

    public function testCanFetch()
    {
        $this->pdoStatementMock->method('fetch')->willReturn('foo');

        $this->assertEquals('foo', $this->stmt->fetch());
    }

    public function testCanFetchAll()
    {
        $this->pdoStatementMock->method('fetchAll')->willReturn(['foo', 'bar']);

        $this->assertEquals(['foo', 'bar'], $this->stmt->fetchAll());
    }

    public function testCanFetchColumn()
    {
        $this->pdoStatementMock->method('fetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->stmt->column());
    }

    public function testCanFetchColumnAssoc()
    {
        $this->pdoStatementMock->method('fetch')->with()->willReturn(['Id' => 1]);

        $this->assertEquals(1, $this->stmt->column('Id'));
    }

    public function testCanFetchAllColumn()
    {
        $this->pdoStatementMock->method('fetch')->will($this->onConsecutiveCalls(['Id' => 1], ['Id' => 2]));

        $this->assertEquals([1, 2], $this->stmt->columnAll('Id'));
    }

    public function testCanFetchPairs()
    {
        $this->pdoStatementMock->method('fetch')->willReturn(['Id' => 1], ['Id' => 2]);

        $this->assertEquals([1 => 1, 2 => 2], $this->stmt->pairs('Id', 'Id'));
    }

    public function testCanGetRowCount()
    {
        $this->pdoStatementMock->method('rowCount')->willReturn(2);

        $this->assertEquals(2, $this->stmt->affectedRows());
    }

    public function testCanReconnectIfConnectionExpired()
    {
        /** @var \PDOException $e */
        $e = $this->getMockBuilder(\PDOException::class)
            ->disableOriginalConstructor()
            ->getMock();

        $e->errorInfo = ['Error', 2006, 'Expired'];

        $this->pdoStatementMock
            ->expects($this->exactly(2))
            ->method('execute')
            ->will($this->onConsecutiveCalls($this->throwException($e), 1));

        $this->stmt->execute();
    }

    public function testCanThrowPDOException()
    {
        $this->pdoStatementMock->method('execute')->will($this->throwException(new \PDOException()));

        $this->expectException(\PDOException::class);

        $this->stmt->execute();
    }

    public function testCanThrowPDOExceptionIfStatementIsWrong()
    {
        $this->pdoStatementMock->method('execute')->willReturn(false);

        $this->pdoStatementMock->method('errorInfo')->willReturn(['ERROR', 1, 'Something wrong.']);

        $this->expectException(\PDOException::class);

        $this->stmt->execute();
    }
}
