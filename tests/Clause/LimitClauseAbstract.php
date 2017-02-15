<?php

namespace Greg\Orm\Tests\Clause;

use Greg\Orm\Clause\LimitClauseStrategy;
use PHPUnit\Framework\TestCase;

abstract class LimitClauseAbstract extends TestCase
{
    public function testCanDetermineIfExists()
    {
        $query = $this->newClause();

        $this->assertFalse($query->hasLimit());

        $query->limit(10);

        $this->assertTrue($query->hasLimit());
    }

    public function testCanGet()
    {
        $query = $this->newClause();

        $query->limit(10);

        $this->assertEquals(10, $query->getLimit());
    }

    public function testCanClear()
    {
        $query = $this->newClause();

        $query->limit(10);

        $query->clearLimit();

        $this->assertNull($query->getLimit());
    }

    abstract protected function newClause(): LimitClauseStrategy;
}
