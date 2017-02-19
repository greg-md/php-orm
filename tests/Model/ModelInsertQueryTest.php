<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\Tests\ModelAbstract;

class ModelInsertQueryTest extends ModelAbstract
{
    public function testCanSetDefaults()
    {
        $this->assertCount(0, $this->model->getDefaults());

        $this->model->setDefaults(['Active' => 1]);

        $this->assertCount(1, $this->model->getDefaults());
    }

    public function testCanInsert()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->assertEquals(1, $this->model->insert(['Column' => 'foo']));
    }

    public function testCanInsertColumns()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->assertEquals(1, $this->model->insertValues(['Column'], ['Column' => 'foo']));
    }

    public function testCanInsertSelectWithDefaults()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->model->setDefaults(['Foo' => 'bar']);

        $this->assertEquals(1, $this->model->insertSelect(['Column'], $this->driver->select()->columns('Column')));
    }

    public function testCanInsertSelect()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->assertEquals(1, $this->model->insertSelect(['Column'], $this->driver->select()->columns('Column')));
    }

    public function testCanInsertSelectRaw()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->assertEquals(1, $this->model->insertSelectRaw(['Column'], $this->driver->select()->columns('Column')));
    }

    public function testCanInsertForEach()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->pdoStatementMock->expects($this->exactly(2))->method('execute');

        $this->model->insertForEach('Column', ['foo', 'bar']);
    }

    public function testCanInsertAndGetId()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->pdoMock->method('lastInsertId')->willReturn(1);

        $this->assertEquals(1, $this->model->insertAndGetId(['Column' => 'foo']));
    }
}
