<?php

namespace Greg\Orm\Connection;

use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;

trait PdoMockTrait
{
    /**
     * @var Pdo|MockObject
     */
    protected $pdoMock;

    /**
     * @var \PDOStatement|MockObject
     */
    protected $pdoStatementMock;

    protected function initPdoMock()
    {
        $this->pdoMock = $pdoMock = $this->getMockBuilder(Pdo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pdoStatementMock = $this->getMockBuilder(\PDOStatement::class)
            ->getMock();
    }

    protected function mockStatements()
    {
        $this->pdoMock
            ->method('connectionProcess')
            ->willReturnCallback(function ($callable) {
                return $callable($this->pdoMock);
            });

        $this->pdoMock->method('prepare')->willReturn($this->pdoStatementMock);

        $this->pdoMock->method('query')->willReturn($this->pdoStatementMock);

        $this->pdoStatementMock->method('execute')->willReturn(true);

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

    abstract public function getMockBuilder($className): MockBuilder;
}
