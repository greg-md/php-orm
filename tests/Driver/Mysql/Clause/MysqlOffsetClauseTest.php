<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Driver\Mysql\Clause\MysqlOffsetClause;
use PHPUnit\Framework\TestCase;

class MysqlOffsetClauseTest extends TestCase
{
    public function testCanLimit()
    {
        $query = $this->newOffset()->offset(10);

        $sql = 'SELECT `Foo` FROM `Bar`';

        $this->assertEquals('SELECT `Foo` FROM `Bar` OFFSET 10', $query->addOffsetToSql($sql));
    }

    public function testCanDetermineIfExists()
    {
        $query = $this->newOffset();

        $this->assertFalse($query->hasOffset());

        $query->offset(10);

        $this->assertTrue($query->hasOffset());
    }

    public function testCanGet()
    {
        $query = $this->newOffset();

        $query->offset(10);

        $this->assertEquals(10, $query->getOffset());
    }

    public function testCanClear()
    {
        $query = $this->newOffset();

        $query->offset(10);

        $query->clearOffset();

        $this->assertNull($query->getOffset());
    }

    protected function newOffset()
    {
        return new MysqlOffsetClause();
    }
}
