<?php

namespace Greg\Orm\Driver;

use PHPUnit_Framework_MockObject_MockBuilder;

trait PdoMock
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
     * @var \PHPUnit_Framework_MockObject_MockObject|PdoConnectorStrategy
     */
    protected $pdoConnector;

    protected function initPdoMock()
    {
        $this->pdoMock = $pdoMock = $this->getMockBuilder(\PDO::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pdoStatementMock = $this->getMockBuilder(\PDOStatement::class)
            ->getMock();

        $this->pdoConnector = $this->getMockBuilder(PdoConnectorStrategy::class)
            ->getMock();

        $this->pdoConnector->method('connect')->willReturn($this->pdoMock);
    }

    protected function mockStatements()
    {
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

    /**
     * @param $className
     * @return PHPUnit_Framework_MockObject_MockBuilder
     */
    abstract public function getMockBuilder($className);
}
