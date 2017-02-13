<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Driver\Mysql\Clause\MysqlLimitClause;
use PHPUnit\Framework\TestCase;

class MysqlLimitClauseTest extends TestCase
{
    public function testCanLimit()
    {
        $query = $this->newLimit()->limit(10);

        $sql = 'SELECT `Foo` FROM `Bar`';

        $this->assertEquals('SELECT `Foo` FROM `Bar` LIMIT 10', $query->addLimitToSql($sql));
    }
    
    public function testCanDetermineIfExists()
    {
        $query = $this->newLimit();

        $this->assertFalse($query->hasLimit());

        $query->limit(10);

        $this->assertTrue($query->hasLimit());
    }

    public function testCanGet()
    {
        $query = $this->newLimit();

        $query->limit(10);

        $this->assertEquals(10, $query->getLimit());
    }

    public function testCanClear()
    {
        $query = $this->newLimit();

        $query->limit(10);

        $query->clearLimit();

        $this->assertNull($query->getLimit());
    }

    protected function newLimit()
    {
        return new MysqlLimitClause();
    }
}