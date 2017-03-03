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
use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;
use PHPUnit\Framework\TestCase;

abstract class ModelTestingAbstract extends TestCase
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var DriverStrategy|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $driverMock;

    public function setUp()
    {
        $driverMock = $this->driverMock = $this->createMock(DriverStrategy::class);

        foreach ($this->driverSql() as $method => $class) {
            $driverMock->method($method)->willReturnCallback(function () use ($class) {
                return new $class();
            });
        }

        $this->driverMock->method('dialect')->willReturn(new SqlDialect());

        $this->model = new class([], $driverMock) extends Model {
            protected $name = 'Table';

            protected $label = 'My Table';

            protected $nameColumn = 'Name';

            protected $unique = [
                'SystemName',
            ];

            protected $casts = [
                'Active' => 'bool',
            ];
        };
    }

    protected function driverSql()
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
