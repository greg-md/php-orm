<?php

namespace Greg\Orm\Tests;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Driver\StatementStrategy;
use Greg\Orm\Model;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

class CustomModel extends Model
{
}

class ModelTest extends ModelAbstract
{
    public function testCanManageQuery()
    {
        $this->assertFalse($this->model->hasQuery());

        $this->assertEmpty($this->model->getQuery());

        $this->model->selectQuery();

        $this->assertTrue($this->model->hasQuery());

        $this->assertInstanceOf(QueryStrategy::class, $this->model->getQuery());

        $this->assertInstanceOf(QueryStrategy::class, $this->model->query());

        $this->model->clearQuery();

        $this->assertEmpty($this->model->getQuery());

        $this->expectException(QueryException::class);

        $this->model->query();
    }

    public function testCanUseWhen()
    {
        $this->model->selectQuery();

        $callable = function (Model $model) {
            $model->where('Column', 'foo');
        };

        $this->model->when(false, $callable);

        $this->assertEquals('SELECT * FROM `Table`', $this->model->toString());

        $this->model->when(true, $callable);

        $this->assertEquals('SELECT * FROM `Table` WHERE `Column` = ?', $this->model->toString());
    }

    public function testCanPrepare()
    {
        $this->mockStatements();

        $this->model->selectQuery()->where('Column', 'foo');

        $this->assertInstanceOf(StatementStrategy::class, $this->model->prepare());
    }

    public function testCanExecute()
    {
        $this->mockStatements();

        $this->model->selectQuery();

        $this->assertInstanceOf(StatementStrategy::class, $this->model->execute());
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
        $this->expectException(QueryException::class);

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
        $model = new CustomModel();

        $this->expectException(\Exception::class);

        $model->name();
    }

    public function testCanThrowExceptionWhenDriverNotDefined()
    {
        $model = new CustomModel();

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

        $query = $this->model->selectPairs();

        $this->assertEquals('SELECT `Id` AS `key`, `Name` AS `value` FROM `Table`', $query->toString());
    }

    public function testCanThrowExceptionIfCanNotSelectPairs()
    {
        $this->expectException(\Exception::class);

        (new CustomModel())->selectPairs();
    }

    public function testCanCreateNewRow()
    {
        $this->mockDescribe();

        $row = new MyModel([
            'Id' => 1,
        ], $this->driver);

        $this->assertEquals(1, $row['Id']);
    }

    public function testCanGetFirstByCallable()
    {
        $this->mockDescribe();

        $rows = new MyModel([], $this->driver);

        $rows->appendRecord([
                'Id' => 1,
            ])
            ->appendRecord([
                'Id' => 2,
            ]);

        $row = $rows->first(function (MyModel $row) {
            return $row['Id'] === 2;
        }, false);

        $this->assertEquals(2, $row['Id']);
    }

    public function testCanChunk()
    {
        $this->mockStatements();

        $this->pdoStatementMock->expects($this->exactly(3))->method('fetchAll')->will($this->onConsecutiveCalls(
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
        $this->mockStatements();

        $this->pdoStatementMock->expects($this->exactly(3))->method('fetchAll')->will($this->onConsecutiveCalls(
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
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchAll')->will($this->onConsecutiveCalls(
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
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchAll')->will($this->onConsecutiveCalls(
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
        $this->mockStatements();

        $this->pdoStatementMock->method('fetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetch());
    }

    public function testCanFetchOrFail()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetchOrFail());
    }

    public function testCanThrowExceptionIfFetchFail()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetch')->willReturn(null);

        $this->expectException(\Exception::class);

        $this->model->fetchOrFail();
    }

    public function testCanFetchAll()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchAll')->willReturn([['Id' => 1]]);

        $this->assertCount(1, $this->model->fetchAll());
    }

    public function testCanFetchColumn()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchColumn());
    }

    public function testCanFetchAllColumn()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchColumn')->will($this->onConsecutiveCalls(1, 2));

        $this->assertEquals([1, 2], $this->model->fetchColumnAll());
    }

    public function testCanFetchPairs()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchAll')->willReturn([[1, 1], [2, 2]]);

        $this->assertEquals([1 => 1, 2 => 2], $this->model->fetchPairs());
    }

    public function testCanFetchCount()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchCount());
    }

    public function testCanFetchMax()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchMax('Column'));
    }

    public function testCanFetchMin()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchMin('Column'));
    }

    public function testCanFetchAvg()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchAvg('Column'));
    }

    public function testCanFetchSum()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchSum('Column'));
    }

    public function testCanFetchExists()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('fetchColumn')->willReturn(1);

        $this->assertTrue($this->model->exists());
    }

    public function testCanUpdate()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

        $this->assertEquals(1, $this->model->update(['Column' => 'foo']));
    }

    public function testCanDelete()
    {
        $this->mockStatements();

        $this->pdoStatementMock->method('rowCount')->willReturn(1);

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

    protected function mockDescribe()
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
        ]);
    }
}
