<?php

namespace Greg\Orm\Tests\Clause;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Conditions;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\QueryException;
use PHPUnit\Framework\TestCase;

abstract class FromClauseAbstract extends TestCase
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
        $query = $this->newClause()->from('Foo')->inner('Bar');

        $this->assertEquals('FROM `Foo` INNER JOIN `Bar`', $query->toString());
    }

    public function testCanJoinTo()
    {
        $query = $this->newClause()->from('Foo')->innerTo('Foo', 'Bar');

        $this->assertEquals('FROM `Foo` INNER JOIN `Bar`', $query->toString());
    }

    public function testCanNotUseJoinsWithoutFrom()
    {
        $this->expectException(QueryException::class);

        $this->newClause()->inner('Foo')->toString();
    }

    public function testCanTransformToString()
    {
        $query = $this->newClause()->from('Foo');

        $this->assertEquals('FROM `Foo`', (string) $query);
    }

    public function testCanCombineClauses()
    {
        $query = $this->newClause()
            ->from(['t' => $this->newSelectQuery()])
            ->innerOn('Table', function (Conditions $strategy) {
                $strategy->isNull('Column');
            });

        $this->assertEquals('FROM (SELECT *) AS `t` INNER JOIN `Table` ON `Column` IS NULL', $query->toString());
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAlias()
    {
        $this->expectException(QueryException::class);

        $this->newClause()->from($this->newSelectQuery());
    }

    abstract protected function newClause(): FromClause;

    abstract protected function newSelectQuery(): SelectQuery;
}
