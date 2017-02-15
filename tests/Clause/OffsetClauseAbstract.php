<?php

namespace Greg\Orm\Tests\Clause;

use Greg\Orm\Clause\OffsetClauseStrategy;
use PHPUnit\Framework\TestCase;

abstract class OffsetClauseAbstract extends TestCase
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

    abstract protected function newClause(): OffsetClauseStrategy;
}
