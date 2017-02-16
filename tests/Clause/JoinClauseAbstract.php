<?php

namespace Greg\Orm\Tests\Clause;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Conditions;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\QueryException;
use PHPUnit\Framework\TestCase;

abstract class JoinClauseAbstract extends TestCase
{
    /**
     * @test
     *
     * @dataProvider joins
     *
     * @param string $type
     */
    public function testCanJoin(string $type)
    {
        $query = $this->newClause();

        $query->{$type}('Foo');

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo`', $query->toString());
    }

    /**
     * @test
     *
     * @dataProvider joins
     *
     * @param string $type
     */
    public function testCanJoinOn(string $type)
    {
        $query = $this->newClause();

        $query->{$type}('Foo', '`Foo`.`Id` = !Bar.Id');

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->toString());
    }

    /**
     * @test
     *
     * @dataProvider joins
     *
     * @param string $type
     */
    public function testCanJoinOnCallable(string $type)
    {
        $query = $this->newClause();

        $query->{$type . 'On'}('Foo', function (Conditions $query) {
            $query->relation('Foo.Id', 'Bar.Id');
        });

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->toString());
    }

    public function testCanCross()
    {
        $query = $this->newClause()->cross('Foo');

        $this->assertEquals('CROSS JOIN `Foo`', $query->toString());
    }

    /**
     * @test
     *
     * @dataProvider joins
     *
     * @param string $type
     */
    public function testCanJoinTo(string $type)
    {
        $query = $this->newClause();

        $query->{$type . 'To'}('bar', 'Foo');

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo`', $query->toString('bar'));

        $this->assertEquals('', $query->toString());
    }

    /**
     * @test
     *
     * @dataProvider joins
     *
     * @param string $type
     */
    public function testCanJoinToOn(string $type)
    {
        $query = $this->newClause();

        $query->{$type . 'To'}('bar', 'Foo', '`Foo`.`Id` = !Bar.Id');

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->toString('bar'));
    }

    /**
     * @test
     *
     * @dataProvider joins
     *
     * @param string $type
     */
    public function testCanJoinToCallable(string $type)
    {
        $query = $this->newClause();

        $query->{$type . 'ToOn'}('bar', 'Foo', function (Conditions $query) {
            $query->relation('Foo.Id', 'Bar.Id');
        });

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->toString('bar'));
    }

    public function testCanCrossTo()
    {
        $query = $this->newClause()->crossTo('bar', 'Foo');

        $this->assertEquals('CROSS JOIN `Foo`', $query->toString('bar'));
    }

    public function testCanDetermineIfJoinsExists()
    {
        $query = $this->newClause();

        $this->assertFalse($query->hasJoins());

        $query->inner('Foo');

        $this->assertTrue($query->hasJoins());
    }

    public function testCanGet()
    {
        $query = $this->newClause();

        $query->inner('Foo');

        $this->assertCount(1, $query->getJoins());
    }

    public function testCanClear()
    {
        $query = $this->newClause();

        $query->inner('Foo');

        $query->clearJoins();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanJoinWithAlias()
    {
        $query = $this->newClause();

        $query->inner(['f' => 'Foo']);

        $this->assertEquals('INNER JOIN `Foo` AS `f`', $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->newClause();

        $query->inner(['f' => 'Foo']);

        $this->assertEquals('INNER JOIN `Foo` AS `f`', (string) $query);
    }

    public function testCanCombineClauses()
    {
        $query = $this->newClause()->inner(['t' => $this->newSelectQuery()]);

        $this->assertEquals('INNER JOIN (SELECT *) AS `t`', $query->toString());
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAlias()
    {
        $this->expectException(QueryException::class);

        $this->newClause()->inner($this->newSelectQuery());
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAliasInSource()
    {
        $this->expectException(QueryException::class);

        $this->newClause()->innerTo($this->newSelectQuery(), 'Table');
    }

    public function joins()
    {
        yield ['left'];
        yield ['right'];
        yield ['inner'];
    }

    abstract protected function newClause(): JoinClause;

    abstract protected function newSelectQuery(): SelectQuery;
}
