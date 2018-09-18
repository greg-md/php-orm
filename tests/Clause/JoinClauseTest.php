<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;
use PHPUnit\Framework\TestCase;

class JoinClauseTest extends TestCase
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

        $query->{$type . 'Join'}('Foo');

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

        $query->{$type . 'Join'}('Foo', '`Foo`.`Id` = !Bar.Id');

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

        $query->{$type . 'JoinOn'}('Foo', function (Conditions $query) {
            $query->relation('Foo.Id', 'Bar.Id');
        });

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->toString());
    }

    public function testCanCross()
    {
        $query = $this->newClause()->crossJoin('Foo');

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

        $query->{$type . 'JoinTo'}('bar', 'Foo');

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo`', $query->joinToString('bar'));

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

        $query->{$type . 'JoinTo'}('bar', 'Foo', '`Foo`.`Id` = !Bar.Id');

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->joinToString('bar'));
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

        $query->{$type . 'JoinOnTo'}('bar', 'Foo', function (Conditions $query) {
            $query->relation('Foo.Id', 'Bar.Id');
        });

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->joinToString('bar'));
    }

    public function testCanCrossTo()
    {
        $query = $this->newClause()->crossJoinTo('bar', 'Foo');

        $this->assertEquals('CROSS JOIN `Foo`', $query->joinToString('bar'));
    }

    public function testCanDetermineIfJoinExists()
    {
        $query = $this->newClause();

        $this->assertFalse($query->hasJoin());

        $query->innerJoin('Foo');

        $this->assertTrue($query->hasJoin());
    }

    public function testCanGet()
    {
        $query = $this->newClause();

        $query->innerJoin('Foo');

        $this->assertCount(1, $query->getJoin());
    }

    public function testCanClear()
    {
        $query = $this->newClause();

        $query->innerJoin('Foo');

        $query->clearJoin();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanJoinWithAlias()
    {
        $query = $this->newClause();

        $query->innerJoin(['f' => 'Foo']);

        $this->assertEquals('INNER JOIN `Foo` AS `f`', $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->newClause();

        $query->innerJoin(['f' => 'Foo']);

        $this->assertEquals('INNER JOIN `Foo` AS `f`', (string) $query);
    }

    public function testCanCombineClauses()
    {
        $query = $this->newClause()->innerJoin(['t' => new SelectQuery()]);

        $this->assertEquals('INNER JOIN (SELECT *) AS `t`', $query->toString());
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAlias()
    {
        $this->expectException(SqlException::class);

        $this->newClause()->innerJoin(new SelectQuery());
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAliasInSource()
    {
        $this->expectException(SqlException::class);

        $this->newClause()->innerJoinTo(new SelectQuery(), 'Table');
    }

    public function joins()
    {
        yield ['left'];
        yield ['right'];
        yield ['inner'];
    }

    protected function newClause(): JoinClause
    {
        return new JoinClause();
    }
}
