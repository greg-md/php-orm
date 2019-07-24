<?php

namespace Greg\Orm;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Connection\ConnectionStrategy;
use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Model\TableSchemaTrait;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;
use Greg\Orm\Table\DeleteTableQueryTraitTest;
use Greg\Orm\Table\FromTableClauseTraitTest;
use Greg\Orm\Table\GroupByTableClauseTraitTest;
use Greg\Orm\Table\HavingTableClauseTraitTest;
use Greg\Orm\Table\JoinTableClauseTraitTest;
use Greg\Orm\Table\LimitTableClauseTraitTest;
use Greg\Orm\Table\OffsetTableClauseTraitTest;
use Greg\Orm\Table\OrderByTableClauseTraitTest;
use Greg\Orm\Table\SelectTableQueryTraitTest;
use Greg\Orm\Table\UpdateTableQueryTraitTest;
use Greg\Orm\Table\WhereTableClauseTraitTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MyModel extends Model
{
    use TableSchemaTrait;

    protected $uniqueKeys = [
        'SystemName',
    ];

    protected $casts = [
        'Active' => 'bool',
    ];

    public function name(): string
    {
        return 'Table';
    }

    protected function getActiveAttribute()
    {
        return $this['Active'];
    }

    protected function setActiveAttribute($value)
    {
        $this['Active'] = $value;
    }
}

class ModelTest extends TestCase
{
    use DeleteTableQueryTraitTest,
        FromTableClauseTraitTest,
        GroupByTableClauseTraitTest,
        HavingTableClauseTraitTest,
        JoinTableClauseTraitTest,
        LimitTableClauseTraitTest,
        OffsetTableClauseTraitTest,
        OrderByTableClauseTraitTest,
        SelectTableQueryTraitTest,
        UpdateTableQueryTraitTest,
        WhereTableClauseTraitTest;

    /**
     * @var MyModel
     */
    protected $model;

    /**
     * @var ConnectionStrategy|MockObject
     */
    protected $connectionMock;

    protected function setUp(): void
    {
        $connectionMock = $this->connectionMock = $this->createMock(ConnectionStrategy::class);

        foreach ($this->connectionSql() as $method => $class) {
            $connectionMock->method($method)->willReturnCallback(function () use ($class, $connectionMock) {
                return new $class(null, $connectionMock);
            });
        }

        $this->connectionMock->method('dialect')->willReturn(new SqlDialect());

        $this->mockDescribe();

        $this->model = new MyModel($this->connectionMock);
    }

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
            ->innerJoin('Table2')
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

    public function testCanGetFillable()
    {
        $this->assertEquals('*', $this->model->fillable());
    }

    public function testCanGetPrimary()
    {
        $this->assertEquals(['Id'], $this->model->primaryKey());
    }

    public function testCanGetUnique()
    {
        $this->assertEquals([['SystemName']], $this->model->uniqueKeys());
    }

    public function testCanGetFirstUnique()
    {
        $this->assertEquals(['Id'], $this->model->firstUniqueKey());
    }

    public function testCanGetAutoIncrement()
    {
        $this->assertEquals('Id', $this->model->autoIncrement());
    }

    public function testCanGetFirstByCallable()
    {
        $connectionMock = $this->connectionMock;

        /** @var Model $rows */
        $rows = new class($connectionMock) extends Model {
            public function name(): string
            {
                return 'Table';
            }
        };

        $rows->setPristineRecords([
            ['Id' => 1],
            ['Id' => 2],
        ]);

        $row = $rows->search(function (Model $row) {
            return $row['Id'] === 2;
        });

        $this->assertEquals(2, $row['Id']);

        $row = $rows->search(function (Model $row) {
            return $row['Id'] === 3;
        });

        $this->assertNull($row);
    }

    public function testCanSearchWhere()
    {
        $rows = $this->model->new(['Id' => 1]);

        $rows->addPristineRecord(['Id' => 2]);

        $row = $rows->searchWhere('Id', 2);

        $this->assertEquals(2, $row['Id']);

        $row = $rows->searchWhere('Id', '>', 1);

        $this->assertEquals(2, $row['Id']);

        $row = $rows->searchWhere('Id', '<', 2);

        $this->assertEquals(1, $row['Id']);

        $row = $rows->searchWhere('Id', '!=', 1);

        $this->assertEquals(2, $row['Id']);

        $row = $rows->searchWhere('Id', 'in', [2, 3]);

        $this->assertEquals(2, $row['Id']);
    }

    public function testCanGenerateInChunks()
    {
        $this->connectionMock->expects($this->exactly(3))->method('sqlFetchAll')->will($this->onConsecutiveCalls(
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

        $recordsGenerator = $this->model->generateInChunks(2);

        foreach ($recordsGenerator as $records) {
            $count++;

            $this->assertCount(2, $records);
        }

        $this->assertEquals(2, $count);
    }

    public function testCanGenerateOneByOne()
    {
        $this->connectionMock->expects($this->exactly(3))->method('sqlGenerate')->will($this->onConsecutiveCalls(
            (function () {
                yield ['Id' => 1];
                yield ['Id' => 2];
            })(),
            (function () {
                yield ['Id' => 1];
                yield ['Id' => 2];
            })(),
            (function () {
                if (false) {
                    yield;
                }
            })()
        ));

        $count = 0;

        $recordsGenerator = $this->model->generate(2);

        foreach ($recordsGenerator as $record) {
            $count++;

            $this->assertArrayHasKey('Id', $record);
        }

        $this->assertEquals(4, $count);
    }

    public function testCanGenerateRowsInChunks()
    {
        $this->connectionMock->expects($this->exactly(3))->method('sqlFetchAll')->will($this->onConsecutiveCalls(
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

        $rowsGenerator = $this->model->generateRowsInChunks(2);

        foreach ($rowsGenerator as $rows) {
            $count++;

            $this->assertInstanceOf(Model::class, $rows);

            $this->assertCount(2, $rows);
        }

        $this->assertEquals(2, $count);
    }

    public function testCanGenerateRowsOneByOne()
    {
        $this->connectionMock->expects($this->exactly(3))->method('sqlGenerate')->will($this->onConsecutiveCalls(
            (function () {
                yield ['Id' => 1];
                yield ['Id' => 2];
            })(),
            (function () {
                yield ['Id' => 1];
                yield ['Id' => 2];
            })(),
            (function () {
                if (false) {
                    yield;
                }
            })()
        ));

        $count = 0;

        $rowsGenerator = $this->model->generateRows(2);

        foreach ($rowsGenerator as $row) {
            $count++;

            $this->assertInstanceOf(Model::class, $row);

            $this->assertArrayHasKey('Id', $row);
        }

        $this->assertEquals(4, $count);
    }

    public function testCanFetch()
    {
        $this->connectionMock->method('sqlFetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetch());
    }

    public function testCanFetchOrFail()
    {
        $this->connectionMock->method('sqlFetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetchOrFail());
    }

    public function testCanThrowExceptionIfFetchFail()
    {
        $this->connectionMock->method('sqlFetch')->willReturn(null);

        $this->expectException(\Exception::class);

        $this->model->fetchOrFail();
    }

    public function testCanFetchAll()
    {
        $this->connectionMock->method('sqlFetchAll')->willReturn([['Id' => 1]]);

        $this->assertCount(1, $this->model->fetchAll());
    }

    public function testCanFetchColumn()
    {
        $this->connectionMock->method('sqlFetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchColumn());
    }

    public function testCanFetchAllColumn()
    {
        $this->connectionMock->method('sqlFetchAllColumn')->willReturn([1, 2]);

        $this->assertEquals([1, 2], $this->model->fetchAllColumn());
    }

    public function testCanFetchPairs()
    {
        $this->connectionMock->method('sqlFetchPairs')->willReturn([1 => 1, 2 => 2]);

        $this->assertEquals([1 => 1, 2 => 2], $this->model->fetchPairs());
    }

    public function testCanFetchCount()
    {
        $this->connectionMock->method('sqlFetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchCount());
    }

    public function testCanFetchMax()
    {
        $this->connectionMock->method('sqlFetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchMax('Column'));
    }

    public function testCanFetchMin()
    {
        $this->connectionMock->method('sqlFetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchMin('Column'));
    }

    public function testCanFetchAvg()
    {
        $this->connectionMock->method('sqlFetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchAvg('Column'));
    }

    public function testCanFetchSum()
    {
        $this->connectionMock->method('sqlFetchColumn')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchSum('Column'));
    }

    public function testCanFetchExists()
    {
        $this->connectionMock->method('sqlFetchColumn')->willReturn(1);

        $this->assertTrue($this->model->exists());
    }

    public function testCanUpdate()
    {
        $this->connectionMock->method('sqlExecute')->willReturn(1);

        $this->assertEquals(1, $this->model->update(['Column' => 'foo']));
    }

    public function testCanDelete()
    {
        $this->connectionMock->method('sqlExecute')->willReturn(1);

        $this->assertEquals(1, $this->model->delete());

        $this->assertEquals(1, $this->model->delete('Table2'));
    }

    public function testThrowExceptionIfChunkSizeIsLessThanZero()
    {
        $this->expectException(\Exception::class);

        $this->model->generate(-1)->valid();
    }

    public function testCanGetColumns()
    {
        $this->assertCount(1, $this->model->columns());
    }

    public function testCanGetDetermineIfColumnExists()
    {
        $this->assertTrue($this->model->hasColumn('Id'));

        $this->assertFalse($this->model->hasColumn('Undefined'));
    }

    public function testCanGetColumn()
    {
        $this->assertNotEmpty($this->model->column('Id'));
    }

    public function testCanThrowExceptionIfColumnNotFound()
    {
        $this->expectException(\Exception::class);

        $this->model->column('Undefined');
    }

    public function testCanGetGuarded()
    {
        $this->assertEquals([], $this->model->guarded());
    }

    public function testCanInsert()
    {
        $this->connectionMock->method('sqlExecute')->willReturn(1);

        $this->assertEquals(1, $this->model->insert(['Column' => 'foo']));
    }

    public function testCanInsertSelect()
    {
        $this->connectionMock->method('sqlExecute')->willReturn(1);

        $this->assertEquals(1, $this->model->insertSelect(['Column'], $this->connectionMock->select()->columns('Column')));
    }

    public function testCanInsertForEach()
    {
        $this->connectionMock->expects($this->exactly(2))->method('sqlExecute')->willReturn(1);

        $this->model->insertForEach('Column', ['foo', 'bar']);
    }

    public function testCanFetchPagination()
    {
        $this->connectionMock->method('sqlGenerate')->willReturn((function () {
            yield ['Id' => 1];
            yield ['Id' => 2];
        })());

        $this->connectionMock->method('sqlFetchColumn')->willReturn(20);

        $pagination = $this->model->pagination(10, 10);

        $this->assertEquals(10, $pagination->rowsLimit());

        $this->assertEquals(10, $pagination->rowsOffset());

        $this->assertEquals(20, $pagination->rowsTotal());
    }

    public function testCanFetchPaginationTotalQuery()
    {
        $this->connectionMock->method('sqlGenerate')->willReturn((function () {
            yield ['Id' => 1];
            yield ['Id' => 2];
        })());

        $this->connectionMock->method('sqlFetchColumn')->willReturn(20);

        $pagination = $this->model->pagination(10, 10, function (SelectQuery $query) {
            $query->where('foo', 'bar');
        });

        $this->assertEquals(10, $pagination->rowsLimit());

        $this->assertEquals(10, $pagination->rowsOffset());

        $this->assertEquals(20, $pagination->rowsTotal());
    }

    public function testCanIterateRows()
    {
        $this->model->setPristineRecords([
            ['Id' => 1],
            ['Id' => 2],
        ]);

        $ids = [1, 2];

        foreach ($this->model as $row) {
            $this->assertEquals(array_shift($ids), $row['Id']);
        }
    }

    public function testCanAddPristineRecordReference()
    {
        $record = ['Id' => 1];

        $recordState = [
            'isNew'    => false,
            'modified' => [],
        ];

        $this->model->addPristineRecordRef($record, $recordState);

        $this->assertEquals(1, $this->model['Id']);

        $record['Id'] = 2;

        $this->assertEquals(2, $this->model['Id']);

        $this->model['Id'] = 3;

        $this->assertEquals(3, $recordState['modified']['Id']);
    }

    public function testCanGetUniqueForFirstUnique()
    {
        $this->connectionMock->method('describe')->willReturn([
            'columns' => [
            ],
            'primary' => [
            ],
        ]);

        $connectionMock = $this->connectionMock;

        /** @var Model $model */
        $model = new class($connectionMock) extends Model {
            protected $uniqueKeys = ['Id'];

            public function name(): string
            {
                return 'Table';
            }
        };

        $this->assertEquals(['Id'], $model->firstUniqueKey());
    }

    public function testCanThrowExceptionIfFirstUniqueNotFound()
    {
        $this->connectionMock->method('describe')->willReturn([
            'columns' => [
            ],
            'primary' => [
            ],
        ]);

        $connectionMock = $this->connectionMock;

        /** @var Model $model */
        $model = new class($connectionMock) extends Model {
            public function name(): string
            {
                return 'Table';
            }
        };

        $this->expectException(\Exception::class);

        $model->firstUniqueKey();
    }

    public function testCanCreateNewRowUsingCreateMethod()
    {
        $row = $this->model->new(['Id' => 1]);

        $this->assertEquals(1, $row['Id']);
    }

    public function testCanFetchRow()
    {
        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1]);

        $this->assertEquals($record, $this->model->fetchRow()->record());
    }

    public function testCanFetchEmptyRow()
    {
        $this->assertEmpty($this->model->fetchRow());
    }

    public function testCanFetchRowOrFail()
    {
        $this->connectionMock->method('sqlFetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetchRowOrFail()->record());
    }

    public function testCanThrowExceptionIfFetchRowFail()
    {
        $this->expectException(\Exception::class);

        $this->model->fetchRowOrFail();
    }

    public function testCanFetchNoRows()
    {
        $this->connectionMock->method('sqlGenerate')->willReturn((function () {
            if (false) {
                yield;
            }
        })());

        $this->assertEquals([], $this->model->fetchRows()->records());
    }

    public function testCanFetchRows()
    {
        $this->connectionMock->method('sqlFetchAll')->willReturn([
            ['Id' => 1],
            ['Id' => 2],
        ]);

        $this->assertEquals([['Id' => 1], ['Id' => 2]], $this->model->fetchRows()->records());
    }

    public function testCanGenerateRows()
    {
        $this->connectionMock->method('sqlGenerate')->willReturn((function () {
            yield ['Id' => 1];

            yield ['Id' => 2];
        })());

        $rows = [['Id' => 1], ['Id' => 2]];

        $generator = $this->model->generateRows();

        $this->assertInstanceOf(\Generator::class, $generator);

        foreach ($generator as $row) {
            $this->assertEquals(array_shift($rows), $row->record());
        }

        $this->assertEmpty($rows);
    }

    public function testCanFind()
    {
        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->find(1);

        $this->assertEquals($record, $row->record());
    }

    public function testCanFindOrFail()
    {
        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->findOrFail(1);

        $this->assertEquals($record, $row->record());
    }

    public function testCanThrowExceptionIfFindFail()
    {
        $this->expectException(\Exception::class);

        $this->model->findOrFail(1);
    }

    public function testCanGetFirst()
    {
        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->first($record);

        $this->assertEquals($record, $row->record());
    }

    public function testCanGetFirstOrFail()
    {
        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->firstOrFail($record);

        $this->assertEquals($record, $row->record());
    }

    public function testCanThrowExceptionIfGetFirstFail()
    {
        $this->expectException(\Exception::class);

        $this->model->firstOrFail(['Id' => 1]);
    }

    public function testCanGetFirstFromFirstOrNew()
    {
        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->firstOrNew($record);

        $this->assertFalse($row->isNew());
    }

    public function testCanGetNewFromFirstOrNew()
    {
        $row = $this->model->firstOrNew(['Id' => 1]);

        $this->assertTrue($row->isNew());
    }

    public function testCanGetFirstFromFirstOrCreate()
    {
        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->firstOrCreate($record);

        $this->assertFalse($row->isNew());
    }

    public function testCanGetNewFromFirstOrCreate()
    {
        $row = $this->model->firstOrCreate(['Id' => 1]);

        $this->assertFalse($row->isNew());
    }

    public function testCanErase()
    {
        $this->connectionMock->expects($this->once())->method('sqlExecute')->with('DELETE FROM `Table` WHERE `Id` = ?', [1]);

        $this->model->erase(1);
    }

    public function testCanTruncate()
    {
        $this->connectionMock->expects($this->once())->method('truncate')->with('Table');

        $this->model->truncate();
    }

    public function testCanThrowExceptionIfCanNotCombinePrimaryKeys()
    {
        $this->expectException(\Exception::class);

        $this->model->find([1, 2]);
    }

    public function testCanThrowExceptionIfCanNotFetchRows()
    {
        $this->expectException(\Exception::class);

        $this->model->select(1)->fetchRow();
    }

    public function testCanFetchRowAndTransformToDatetimeValue()
    {
        $this->model = new class($this->connectionMock) extends Model {
            protected $label = 'My Table';

            protected $nameColumn = 'Name';

            protected $unique = [
                'SystemName',
            ];

            protected $casts = [
                'Active' => 'bool',
                'Foo'    => 'datetime',
            ];

            public function name(): string
            {
                return 'Table';
            }

            protected function getActiveAttribute()
            {
                return $this['Active'];
            }

            protected function setActiveAttribute($value)
            {
                $this['Active'] = $value;
            }
        };

        $this->connectionMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'text',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1, 'Foo' => '01.01.2017 18:00:00']);

        $row = $this->model->fetchRow();

        $this->assertEquals('2017-01-01 18:00:00', $row['Foo']);
    }

    public function testCanFetchRowAndTransformToDateValue()
    {
        $this->model = new class($this->connectionMock) extends Model {
            protected $label = 'My Table';

            protected $nameColumn = 'Name';

            protected $unique = [
                'SystemName',
            ];

            protected $casts = [
                'Active' => 'bool',
                'Foo'    => 'date',
            ];

            public function name(): string
            {
                return 'Table';
            }

            protected function getActiveAttribute()
            {
                return $this['Active'];
            }

            protected function setActiveAttribute($value)
            {
                $this['Active'] = $value;
            }
        };

        $this->connectionMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'text',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1, 'Foo' => '01.01.2017']);

        $row = $this->model->fetchRow();

        $this->assertEquals('2017-01-01', $row['Foo']);
    }

    public function testCanFetchRowAndTransformToTimeValue()
    {
        $this->model = new class($this->connectionMock) extends Model {
            protected $label = 'My Table';

            protected $nameColumn = 'Name';

            protected $unique = [
                'SystemName',
            ];

            protected $casts = [
                'Active' => 'bool',
                'Foo'    => 'time',
            ];

            public function name(): string
            {
                return 'Table';
            }

            protected function getActiveAttribute()
            {
                return $this['Active'];
            }

            protected function setActiveAttribute($value)
            {
                $this['Active'] = $value;
            }
        };

        $this->connectionMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'text',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->connectionMock->method('sqlFetch')->willReturn($record = ['Id' => 1, 'Foo' => '18:00']);

        $row = $this->model->fetchRow();

        $this->assertEquals('18:00:00', $row['Foo']);
    }

    public function testCanDetermineIfModelRowsHasColumn()
    {
        $this->mockDescribe();

        $this->assertFalse($this->model->has('Id'));

        $rows = $this->model->new(['Id' => 1]);

        $this->assertTrue($rows->has('Id'));

        $this->assertFalse($rows->has('Undefined'));
    }

    public function testCanDetermineIfModelRowsHasMultipleColumns()
    {
        $this->assertFalse($this->model->hasMultiple(['Id']));

        $rows = $this->model->new(['Id' => 1]);

        $this->assertTrue($rows->hasMultiple(['Id']));

        $this->assertFalse($rows->hasMultiple(['Id', 'Undefined']));

        $this->assertTrue($rows->hasMultiple([]));
    }

    public function testCanSetColumn()
    {
        $rows = $this->model->new(['Id' => 1]);

        $rows->addPristineRecord(['Id' => 2]);

        $rows->set('Id', 3);

        $this->assertEquals(3, $rows->row(0)['Id']);

        $this->assertEquals(3, $rows->row(0)['Id']);
    }

    public function testCanSetMultipleColumns()
    {
        $rows = $this->model->new(['Id' => 1]);

        $rows->addPristineRecord(['Id' => 2]);

        $rows->setMultiple([
            'Id' => 3,
        ]);

        $this->assertEquals(3, $rows->row(0)['Id']);

        $this->assertEquals(3, $rows->row(0)['Id']);
    }

    public function testCanGetColumnsValues()
    {
        $rows = $this->model->new(['Id' => 1]);

        $rows->addPristineRecord(['Id' => 2]);

        $this->assertEquals([1, 2], $rows->get('Id'));
    }

    public function testCanGetModifiedValue()
    {
        $rows = $this->model->create(['Id' => 1]);

        $this->assertEquals(1, $rows['Id']);

        $rows->set('Id', 2);

        $this->assertEquals(2, $rows['Id']);
    }

    public function testCanGetMultipleColumnsValues()
    {
        $rows = $this->model->new(['Id' => 1]);

        $rows->addPristineRecord(['Id' => 2]);

        $this->assertEquals(['Id' => [1, 2]], $rows->getMultiple(['Id']));
    }

    public function testCanSaveValues()
    {
        $this->connectionMock
            ->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(1);

        $rows = $this->model->new(['Id' => null]);

        $rows->save();

        $this->assertFalse($rows->isNew());

        $this->connectionMock
            ->expects($this->once())
            ->method('sqlExecute')
            ->with('UPDATE `Table` SET `Id` = ? WHERE `Id` = ?', ['2', '1']);

        $rows->set('Id', 2);

        $rows->save();
    }

    public function testCanDestroyRows()
    {
        $rows = $this->model->create(['Id' => 1]);

        $rows->addPristineRecord(['Id' => 2], ['isNew' => true, 'modified' => false]);

        $this->connectionMock
            ->expects($this->once())
            ->method('sqlExecute')
            ->with('DELETE FROM `Table` WHERE `Id` = ?', ['1']);

        $rows->destroy();
    }

    public function testCanGetRow()
    {
        $rows = $this->model->new(['Id' => 1]);

        $this->assertNotNull($rows->row(0));

        $this->assertNull($rows->row(1));
    }

    public function testCanGetToArray()
    {
        $rows = $this->model->new(['Id' => 1]);

        $this->assertEquals([['Id' => 1]], $rows->records());
    }

    public function testCanChangeRowStatus()
    {
        $rows = $this->model->new(['Id' => 1]);

        $rows->markAsOld();

        $this->assertFalse($rows->row(0)->isNew());

        $rows->markAsNew();

        $this->assertTrue($rows->row(0)->isNew());
    }

    public function testCanGetMany()
    {
        $rows = $this->model->new(['Id' => 1]);

        $manyRows = $rows->hasMany($this->model, 'Id');

        $this->assertInstanceOf(Model::class, $manyRows);

        $this->assertEquals('SELECT * FROM `Table` WHERE (`Id` = ?)', $manyRows->select('*')->toString());
    }

    public function testCanBelongsTo()
    {
        $rows = $this->model->new(['Id' => 1]);

        $belongsTo = $rows->belongsTo($this->model, 'Id');

        $this->assertInstanceOf(Model::class, $belongsTo);

        $this->assertEquals('SELECT * FROM `Table` WHERE (`Id` = ?)', $belongsTo->select('*')->toString());
    }

    public function testCanSetUnmodified()
    {
        $rows = $this->model->create(['Id' => 1]);

        $this->assertEmpty($rows->originalModified());

        $rows->set('Id', 2);

        $this->assertNotEmpty($rows->originalModified());

        $rows->set('Id', 1);

        $this->assertEquals([], $rows->originalModified());
    }

    public function testCanThrowExceptionIfColumnIsNotFillable()
    {
        $connectionMock = $this->connectionMock;

        /** @var Model $row */
        $row = new class($connectionMock) extends Model {
            protected $fillable = [];

            public function name(): string
            {
                return 'Table';
            }
        };

        $row->addPristineRecord(['Id' => 1]);

        $this->expectException(\Exception::class);

        $row['Id'] = 2;
    }

    protected function model(): Model
    {
        return $this->model;
    }

    protected function connectionMock(): MockObject
    {
        return $this->connectionMock;
    }

    protected function mockDescribe()
    {
        $this->connectionMock
            ->method('describe')
            ->willReturn([
                'columns' => [
                    'Id' => [
                        'name'    => 'Id',
                        'type'    => 'int',
                        'null'    => false,
                        'default' => null,
                        'extra'   => [
                            'isInt'         => true,
                            'isFloat'       => false,
                            'isNumeric'     => false,
                            'autoIncrement' => true,
                        ],
                    ],
                ],
                'primary' => [
                    'Id',
                ],
            ]);
    }

    protected function connectionSql()
    {
        yield 'select' => SelectQuery::class;
        yield 'insert' => InsertQuery::class;
        yield 'delete' => DeleteQuery::class;
        yield 'update' => UpdateQuery::class;
        yield 'from' => FromClause::class;
        yield 'join' => JoinClause::class;
        yield 'where' => WhereClause::class;
        yield 'having' => HavingClause::class;
        yield 'orderBy' => OrderByClause::class;
        yield 'groupBy' => GroupByClause::class;
        yield 'limit' => LimitClause::class;
        yield 'offset' => OffsetClause::class;
    }
}
