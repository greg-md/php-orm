<?php

namespace Greg\Orm\Tests\Driver;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;
use PHPUnit\Framework\TestCase;

class DriverAbstract extends TestCase
{
    /**
     * @var DriverStrategy
     */
    protected $db;

    /**
     * @test
     *
     * @dataProvider queries
     *
     * @param $name
     * @param $class
     */
    public function testCanInstanceAQuery($name, $class)
    {
        $this->assertInstanceOf($class, $this->db->{$name}());
    }

    public function queries()
    {
        yield ['select', SelectQuery::class];
        yield ['insert', InsertQuery::class];
        yield ['delete', DeleteQuery::class];
        yield ['update', UpdateQuery::class];
        yield ['from', FromClause::class];
        yield ['join', JoinClause::class];
        yield ['where', WhereClause::class];
        yield ['having', HavingClause::class];
        yield ['orderBy', OrderByClause::class];
        yield ['groupBy', GroupByClause::class];
        yield ['limit', LimitClause::class];
    }
}
