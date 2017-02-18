<?php

namespace Greg\Orm\Tests\Clause;

use Greg\Orm\Clause\GroupByClause;

trait GroupByClauseTrait
{
    public function testCanGroupBy()
    {
        $query = $this->newClause()->groupBy('Foo');

        $this->assertEquals('GROUP BY `Foo`', $query->toString());
    }

    public function testCanGroupByRaw()
    {
        $query = $this->newClause()->groupByRaw('`Foo`');

        $this->assertEquals('GROUP BY `Foo`', $query->toString());
    }

    public function testCanDetermineIfExists()
    {
        $query = $this->newClause();

        $this->assertFalse($query->hasGroupBy());

        $query->groupBy('Foo');

        $this->assertTrue($query->hasGroupBy());
    }

    public function testCanGet()
    {
        $query = $this->newClause();

        $query->groupBy('Foo');

        $this->assertCount(1, $query->getGroupBy());
    }

    public function testCanClear()
    {
        $query = $this->newClause();

        $query->groupBy('Foo');

        $query->clearGroupBy();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newClause()->groupBy('Foo');

        $this->assertEquals('GROUP BY `Foo`', (string) $query);
    }

    abstract protected function newClause(): GroupByClause;
}
