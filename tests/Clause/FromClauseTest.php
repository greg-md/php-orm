<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;
use PHPUnit\Framework\TestCase;

class FromClauseTest extends TestCase
{
    public function testCanAddFrom()
    {
        $query = $this->newClause()->from('Foo');

        $this->assertEquals('FROM `Foo`', $query->toString());
    }

    public function testCanAddFromWithAlias()
    {
        $query = $this->newClause()->from(['f' => 'Foo']);

        $this->assertEquals('FROM `Foo` AS `f`', $query->toString());
    }

    public function testCanAddFromRaw()
    {
        $query = $this->newClause()->fromRaw('f', '`Foo`');

        $this->assertEquals('FROM `Foo` AS `f`', $query->toString());
    }

    public function testCanDetermineIfExists()
    {
        $query = $this->newClause();

        $this->assertFalse($query->hasFrom());

        $query->from('Foo');

        $this->assertTrue($query->hasFrom());
    }

    public function testCanGet()
    {
        $query = $this->newClause();

        $query->from('Foo');

        $this->assertCount(1, $query->getFrom());
    }

    public function testCanClear()
    {
        $query = $this->newClause();

        $query->from('Foo');

        $query->clearFrom();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanJoin()
    {
        $join = (new JoinClause())->innerJoinTo('Foo', 'Bar');

        $query = $this->newClause()->from('Foo');

        $this->assertEquals('FROM `Foo` INNER JOIN `Bar`', $query->toString($join));
    }

    public function testCanFromSelect()
    {
        $query = $this->newClause()->from(['t' => new SelectQuery()]);

        $this->assertEquals('FROM (SELECT *) AS `t`', $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->newClause()->from('Foo');

        $this->assertEquals('FROM `Foo`', (string) $query);
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAlias()
    {
        $this->expectException(SqlException::class);

        $this->newClause()->from(new SelectQuery());
    }

    private function newClause(): FromClause
    {
        return new FromClause();
    }
}
