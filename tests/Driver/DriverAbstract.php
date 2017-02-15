<?php

namespace Greg\Orm\Tests\Driver;

use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Query\DeleteQueryStrategy;
use Greg\Orm\Query\InsertQueryStrategy;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Query\UpdateQueryStrategy;
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
        yield ['select', SelectQueryStrategy::class];
        yield ['insert', InsertQueryStrategy::class];
        yield ['delete', DeleteQueryStrategy::class];
        yield ['update', UpdateQueryStrategy::class];
        yield ['from', FromClauseStrategy::class];
        yield ['join', JoinClauseStrategy::class];
        yield ['where', WhereClauseStrategy::class];
        yield ['having', HavingClauseStrategy::class];
        yield ['orderBy', OrderByClauseStrategy::class];
        yield ['groupBy', GroupByClauseStrategy::class];
        yield ['limit', LimitClauseStrategy::class];
    }
}
