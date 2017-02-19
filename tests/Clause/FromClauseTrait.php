<?php

namespace Greg\Orm\Tests\Clause;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\QueryException;

trait FromClauseTrait
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
        $join = $this->newJoinClause()->innerTo('Foo', 'Bar');

        $query = $this->newClause()->from('Foo');

        $this->assertEquals('FROM `Foo` INNER JOIN `Bar`', $query->toString($join));
    }

    public function testCanFromSelect()
    {
        $query = $this->newClause()->from(['t' => $this->newSelectQuery()]);

        $this->assertEquals('FROM (SELECT *) AS `t`', $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->newClause()->from('Foo');

        $this->assertEquals('FROM `Foo`', (string) $query);
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAlias()
    {
        $this->expectException(QueryException::class);

        $this->newClause()->from($this->newSelectQuery());
    }

    /**
     * @return FromClause
     */
    abstract protected function newClause();

    /**
     * @return JoinClause
     */
    abstract protected function newJoinClause();

    abstract protected function newSelectQuery(): SelectQuery;
}
