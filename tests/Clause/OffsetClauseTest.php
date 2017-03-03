<?php

namespace Greg\Orm\Clause;

use PHPUnit\Framework\TestCase;

class OffsetClauseTest extends TestCase
{
    public function testCanDetermineIfExists()
    {
        $query = $this->newClause();

        $this->assertFalse($query->hasOffset());

        $query->offset(10);

        $this->assertTrue($query->hasOffset());
    }

    public function testCanGet()
    {
        $query = $this->newClause();

        $query->offset(10);

        $this->assertEquals(10, $query->getOffset());
    }

    public function testCanClear()
    {
        $query = $this->newClause();

        $query->offset(10);

        $query->clearOffset();

        $this->assertNull($query->getOffset());
    }

    protected function newClause(): OffsetClause
    {
        return new OffsetClause();
    }
}
