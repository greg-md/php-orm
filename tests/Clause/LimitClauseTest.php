<?php

namespace Greg\Orm\Clause;

use PHPUnit\Framework\TestCase;

class LimitClauseTest extends TestCase
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

    protected function newClause(): LimitClause
    {
        return new LimitClause();
    }
}
