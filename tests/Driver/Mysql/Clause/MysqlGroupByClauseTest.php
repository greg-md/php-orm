<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Driver\Mysql\Clause\MysqlGroupByClause;
use PHPUnit\Framework\TestCase;

class MysqlGroupByClauseTest extends TestCase
{
    public function testCanGroupBy()
    {
        $query = $this->newGroupBy()->groupBy('Foo');

        $this->assertEquals('GROUP BY `Foo`', $query->toString());
    }

    public function testCanGroupByRaw()
    {
        $query = $this->newGroupBy()->groupByRaw('`Foo`');

        $this->assertEquals('GROUP BY `Foo`', $query->toString());
    }

    public function testCanDetermineIfExists()
    {
        $query = $this->newGroupBy();

        $this->assertFalse($query->hasGroupBy());

        $query->groupBy('Foo');

        $this->assertTrue($query->hasGroupBy());
    }

    public function testCanGet()
    {
        $query = $this->newGroupBy();

        $query->groupBy('Foo');

        $this->assertCount(1, $query->getGroupBy());
    }

    public function testCanClear()
    {
        $query = $this->newGroupBy();

        $query->groupBy('Foo');

        $query->clearGroupBy();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newGroupBy()->groupBy('Foo');

        $this->assertEquals('GROUP BY `Foo`', (string) $query);
    }

    protected function newGroupBy()
    {
        return new MysqlGroupByClause();
    }
}
