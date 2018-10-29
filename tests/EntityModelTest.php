<?php

namespace Greg\Orm;

use Greg\Orm\Connection\ConnectionStrategy;
use PHPUnit\Framework\TestCase;

class TableEntity
{
    private $Id;

    public function getId()
    {
        return $this->Id;
    }
}

class TableEntityModel extends EntityModel {
    protected $name = 'TableEntity';

    protected $entityClass = TableEntity::class;

    public function new(array $record = []): TableEntity
    {
        return parent::new($record);
    }
}

class EntityModelTest extends TestCase
{
    public function testCanInstantiate()
    {
        $connection = $this->mockConnection();

        $model = $this->newEntityModel($connection);

        $this->assertInstanceOf(EntityModel::class, $model);
    }

    public function testCanGetEntityClassName()
    {
        $connection = $this->mockConnection();

        $model = $this->newEntityModel($connection);

        $this->assertEquals(TableEntity::class, $model->entityClass());
    }

    public function testCanFetchEntity()
    {
        $connection = $this->mockConnection();

        $model = $this->newEntityModel($connection);

        $item = $model->create(['Id' => 1]);

        $this->assertInstanceOf(TableEntity::class, $item);

        $this->assertEquals(1, $item->getId());
    }

    private function newEntityModel(ConnectionStrategy $connection)
    {
        return new TableEntityModel($connection);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ConnectionStrategy
     */
    private function mockConnection()
    {
        return $this->getMockBuilder(ConnectionStrategy::class)
            ->getMock();
    }
}
