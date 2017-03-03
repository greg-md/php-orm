<?php

namespace Greg\Orm\Tests;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Model;
use Greg\Orm\ModelTestingAbstract;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

class ModelTest extends ModelTestingAbstract
{
    public function testCanManageQuery()
    {
        $this->assertFalse($this->model->hasQuery());

        $this->assertEmpty($this->model->getQuery());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->assertTrue($this->model->hasQuery());

        $this->assertInstanceOf(QueryStrategy::class, $this->model->getQuery());

        $this->assertInstanceOf(QueryStrategy::class, $this->model->query());

        $this->model->clearQuery();

        $this->assertEmpty($this->model->getQuery());

        $this->expectException(SqlException::class);

        $this->model->query();
    }

    public function testCanUseWhen()
    {
        $this->model->setQuery($this->model->newSelectQuery());

        $callable = function (Model $model) {
            $model->where('Column', 'foo');
        };

        $this->model->when(false, $callable);

        $this->assertEquals('SELECT * FROM `Table`', $this->model->toString());

        $this->model->when(true, $callable);

        $this->assertEquals('SELECT * FROM `Table` WHERE `Column` = ?', $this->model->toString());
    }

    public function testCanGetClausesSql()
    {
        $query = $this->model
            ->from('Table1')
            ->inner('Table2')
            ->where('Column', 'foo')
            ->having('Column', 'foo')
            ->orderBy('Column')
            ->groupBy('Column')
            ->limit(10)
            ->offset(10);

        $sql = 'FROM `Table1` INNER JOIN `Table2` WHERE `Column` = ?'
                . ' GROUP BY `Column` HAVING `Column` = ? ORDER BY `Column` LIMIT 10 OFFSET 10';

        $this->assertEquals($sql, $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->model->where('Column', 'foo');

        $this->assertEquals('WHERE `Column` = ?', (string) $query);
    }

    public function testCanGetClause()
    {
        $query = $this->model->where('Column', 'foo');

        $this->assertInstanceOf(WhereClause::class, $query->clause('WHERE'));
    }

    public function testCanThrowExceptionIfClauseNotExists()
    {
        $this->expectException(SqlException::class);

        $this->model->clause('FROM');
    }

    public function testCanDetermineIfClauseExists()
    {
        $this->assertFalse($this->model->hasClause('WHERE'));

        $query = $this->model->where('Column', 'foo');

        $this->assertTrue($query->hasClause('WHERE'));
    }

    public function testCanClearClause()
    {
        $query = $this->model->where('Column', 'foo');

        $query->clearClause('WHERE');

        $this->assertFalse($query->hasClause('WHERE'));
    }

    public function testCanThrowExceptionIfNameNotDefined()
    {
        /** @var Model $model */
        $model = new class extends Model {};

        $this->expectException(\Exception::class);

        $model->name();
    }

    public function testCanThrowExceptionWhenDriverNotDefined()
    {
        /** @var Model $model */
        $model = new class extends Model {};

        $this->expectException(\Exception::class);

        $model->driver();
    }

    public function testCanGetLabel()
    {
        $this->assertEquals('My Table', $this->model->label());
    }

    public function testCanGetFillable()
    {
        $this->assertEquals('*', $this->model->fillable());
    }

    public function testCanGetPrimary()
    {
        $this->mockDescribe();

        $this->assertEquals(['Id'], $this->model->primary());
    }

    public function testCanGetUnique()
    {
        $this->assertEquals([['SystemName']], $this->model->unique());
    }

    public function testCanGetFirstUnique()
    {
        $this->mockDescribe();

        $this->assertEquals(['Id'], $this->model->firstUnique());
    }

    public function testCanGetAutoIncrement()
    {
        $this->mockDescribe();

        $this->assertEquals('Id', $this->model->autoIncrement());
    }

    public function testCanGetNameColumn()
    {
        $this->assertEquals('Name', $this->model->nameColumn());
    }

    public function testCanGetCasts()
    {
        $this->assertEquals(['Active' => 'bool'], $this->model->casts());
    }

    public function testCanGetCast()
    {
        $this->assertEquals('bool', $this->model->cast('Active'));
    }

    public function testCanSelectPairs()
    {
        $this->mockDescribe();

        $this->driverMock->method('pairs')->willReturn([1 => 1, 2 => 2]);

        $this->assertEquals([1 => 1, 2 => 2], $this->model->pairs());
    }

    public function testCanThrowExceptionIfCanNotSelectPairs()
    {
        $this->expectException(\Exception::class);

        /** @var Model $model */
        $model = new class extends Model {};

        $model->pairs();
    }

    public function testCanCreateNewRow()
    {
        $this->mockDescribe();

        $driver = $this->driverMock;

        /** @var Model $row */
        $row = new class(['Id' => 1], $driver) extends Model {
            protected $name = 'Table';
        };

        $this->assertEquals(1, $row['Id']);
    }

    public function testCanGetFirstByCallable()
    {
        $this->mockDescribe();

        $driver = $this->driverMock;

        /** @var Model $rows */
        $rows = new class([], $driver) extends Model {
            protected $name = 'Table';
        };

        $rows->appendRecord([
                'Id' => 1,
            ])
            ->appendRecord([
                'Id' => 2,
            ]);

        $row = $rows->search(function (Model $row) {
            return $row['Id'] === 2;
        }, false);

        $this->assertEquals(2, $row['Id']);
    }

    public function testCanChunk()
    {
        $this->driverMock->expects($this->exactly(3))->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ],
            [
                ['Id' => 3],
                ['Id' => 4],
            ],
            [

            ]
        ));

        $count = 0;

        $this->model->chunk(2, function ($records) use (&$count) {
            ++$count;

            $this->assertCount(2, $records);
        });

        $this->assertEquals(2, $count);
    }

    public function testCanChunkOneByOne()
    {
        $this->driverMock->expects($this->exactly(3))->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ],
            [
                ['Id' => 3],
                ['Id' => 4],
            ],
            [

            ]
        ));

        $count = 0;

        $this->model->chunk(2, function ($records) use (&$count) {
            ++$count;

            $this->assertCount(1, $records);
        }, true, false);

        $this->assertEquals(4, $count);
    }

    public function testCanStopChunk()
    {
        $this->driverMock->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ]
        ));

        $count = 0;

        $this->model->chunk(2, function () use (&$count) {
            ++$count;

            return false;
        });

        $this->assertEquals(1, $count);
    }

    public function testCanStopChunk1By1()
    {
        $this->driverMock->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ]
        ));

        $count = 0;

        $this->model->chunk(2, function () use (&$count) {
            ++$count;

            return false;
        }, true, false);

        $this->assertEquals(1, $count);
    }

    public function testCanFetch()
    {
        $this->driverMock->method('fetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetch());
    }

    public function testCanFetchOrFail()
    {
        $this->driverMock->method('fetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetchOrFail());
    }

    public function testCanThrowExceptionIfFetchFail()
    {
        $this->driverMock->method('fetch')->willReturn(null);

        $this->expectException(\Exception::class);

        $this->model->fetchOrFail();
    }

    public function testCanFetchAll()
    {
        $this->driverMock->method('fetchAll')->willReturn([['Id' => 1]]);

        $this->assertCount(1, $this->model->fetchAll());
    }

    public function testCanFetchColumn()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchColumn());
    }

    public function testCanFetchAllColumn()
    {
        $this->driverMock->method('columnAll')->willReturn([1, 2]);

        $this->assertEquals([1, 2], $this->model->fetchColumnAll());
    }

    public function testCanFetchPairs()
    {
        $this->driverMock->method('pairs')->willReturn([1 => 1, 2 => 2]);

        $this->assertEquals([1 => 1, 2 => 2], $this->model->fetchPairs());
    }

    public function testCanFetchCount()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchCount());
    }

    public function testCanFetchMax()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchMax('Column'));
    }

    public function testCanFetchMin()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchMin('Column'));
    }

    public function testCanFetchAvg()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchAvg('Column'));
    }

    public function testCanFetchSum()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchSum('Column'));
    }

    public function testCanFetchExists()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertTrue($this->model->exists());
    }

    public function testCanUpdate()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->update(['Column' => 'foo']));
    }

    public function testCanDelete()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->delete());

        $this->assertEquals(1, $this->model->delete('Table2'));
    }

    public function testThrowExceptionIfChunkCountIsLessThanZero()
    {
        $this->expectException(\Exception::class);

        $this->model->chunk(-1, function () {
        });
    }

    public function testCanGetColumns()
    {
        $this->mockDescribe();

        $this->assertCount(1, $this->model->columns());
    }

    public function testCanGetDetermineIfColumnExists()
    {
        $this->mockDescribe();

        $this->assertTrue($this->model->hasColumn('Id'));

        $this->assertFalse($this->model->hasColumn('Undefined'));
    }

    public function testCanGetColumn()
    {
        $this->mockDescribe();

        $this->assertNotEmpty($this->model->column('Id'));
    }

    public function testCanThrowExceptionIfColumnNotFound()
    {
        $this->mockDescribe();

        $this->expectException(\Exception::class);

        $this->model->column('Undefined');
    }

    public function testCanGetGuarded()
    {
        $this->assertEquals([], $this->model->guarded());
    }

    public function testCanSetDefaults()
    {
        $this->assertCount(0, $this->model->getDefaults());

        $this->model->setDefaults(['Active' => 1]);

        $this->assertCount(1, $this->model->getDefaults());
    }

    public function testCanInsert()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->insert(['Column' => 'foo']));
    }

    public function testCanInsertSelectWithDefaults()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->model->setDefaults(['Foo' => 'bar']);

        $this->assertEquals(1, $this->model->insertSelect(['Column'], $this->driverMock->select()->columns('Column')));
    }

    public function testCanInsertSelect()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->insertSelect(['Column'], $this->driverMock->select()->columns('Column')));
    }

    public function testCanInsertSelectRaw()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->insertSelectRaw(['Column'], $this->driverMock->select()->columns('Column')));
    }

    public function testCanInsertForEach()
    {
        $this->driverMock->expects($this->exactly(2))->method('execute')->willReturn(1);

        $this->model->insertForEach('Column', ['foo', 'bar']);
    }

    public function testCanFetchPagination()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetchYield')->willReturnOnConsecutiveCalls([
            ['Id' => 1],
            ['Id' => 2],
        ]);

        $this->driverMock->method('column')->willReturn(20);

        $pagination = $this->model->pagination(10, 10);

        $this->assertEquals(10, $pagination->rowsLimit());

        $this->assertEquals(10, $pagination->rowsOffset());

        $this->assertEquals(20, $pagination->rowsTotal());
    }

    public function testCanIterateRows()
    {
        $this->mockDescribe();

        $this->model->appendRecord(['Id' => 1]);

        $this->model->appendRecord(['Id' => 2]);

        $ids = [1, 2];

        foreach ($this->model as $row) {
            $this->assertEquals(array_shift($ids), $row['Id']);
        }
    }

    public function testCanAppendRecordReference()
    {
        $this->mockDescribe();

        $record = ['Id' => 1];

        $isNew = false;

        $modified = [];

        $this->model->appendRecordRef($record, $isNew, $modified);

        $this->assertEquals(1, $this->model['Id']);

        $record['Id'] = 2;

        $this->assertEquals(2, $this->model['Id']);

        $this->model['Id'] = 3;

        $this->assertEquals(3, $modified['Id']);
    }

    public function testCanGetUniqueForFirstUnique()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
            ],
            'primary' => [
            ],
        ]);

        $driver = $this->driverMock;

        /** @var Model $model */
        $model = new class([], $driver) extends Model {
            protected $name = 'Table';

            protected $unique = ['Id'];
        };

        $this->assertEquals(['Id'], $model->firstUnique());
    }

    public function testCanThrowExceptionIfFirstUniqueNotFound()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
            ],
            'primary' => [
            ],
        ]);

        $driver = $this->driverMock;

        /** @var Model $model */
        $model = new class([], $driver) extends Model {
            protected $name = 'Table';
        };

        $this->expectException(\Exception::class);

        $model->firstUnique();
    }

    public function testCanCreateNewRowUsingCreateMethod()
    {
        $this->mockDescribe();

        $row = $this->model->create(['Id' => 1]);

        $this->assertEquals(1, $row['Id']);
    }

    protected function mockDescribe()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra' => [
                        'isInt' => true,
                        'isFloat' => false,
                        'isNumeric' => false,
                        'autoIncrement' => true,
                    ]
                ]
            ],
            'primary' => [
                'Id',
            ],
        ]);
    }
}
