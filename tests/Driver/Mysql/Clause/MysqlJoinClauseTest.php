<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Mysql\Clause\MysqlJoinClause;
use Greg\Orm\Driver\Mysql\Query\MysqlSelectQuery;
use Greg\Orm\QueryException;
use PHPUnit\Framework\TestCase;

class MysqlJoinClauseTest extends TestCase
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
        $query = $this->newJoin();

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
        $query = $this->newJoin();

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
        $query = $this->newJoin();

        $query->{$type . 'On'}('Foo', function(ConditionsStrategy $query) {
            $query->relation('Foo.Id', 'Bar.Id');
        });

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->toString());
    }

    public function testCanCross()
    {
        $query = $this->newJoin()->cross('Foo');

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
        $query = $this->newJoin();

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
        $query = $this->newJoin();

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
        $query = $this->newJoin();

        $query->{$type . 'ToOn'}('bar', 'Foo', function(ConditionsStrategy $query) {
            $query->relation('Foo.Id', 'Bar.Id');
        });

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->toString('bar'));
    }

    public function testCanCrossTo()
    {
        $query = $this->newJoin()->crossTo('bar', 'Foo');

        $this->assertEquals('CROSS JOIN `Foo`', $query->toString('bar'));
    }

    public function testCanDetermineIfJoinsExists()
    {
        $query = $this->newJoin();

        $this->assertFalse($query->hasJoins());

        $query->inner('Foo');

        $this->assertTrue($query->hasJoins());
    }

    public function testCanGet()
    {
        $query = $this->newJoin();

        $query->inner('Foo');

        $this->assertCount(1, $query->getJoins());
    }

    public function testCanClear()
    {
        $query = $this->newJoin();

        $query->inner('Foo');

        $query->clearJoins();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanJoinWithAlias()
    {
        $query = $this->newJoin();

        $query->inner(['f' => 'Foo']);

        $this->assertEquals('INNER JOIN `Foo` AS `f`', $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->newJoin();

        $query->inner(['f' => 'Foo']);

        $this->assertEquals('INNER JOIN `Foo` AS `f`', (string) $query);
    }

    public function testCanCombineClauses()
    {
        $query = $this->newJoin()->inner(['t' => new MysqlSelectQuery()]);

        $this->assertEquals('INNER JOIN (SELECT *) AS `t`', $query->toString());
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAlias()
    {
        $this->expectException(QueryException::class);

        $this->newJoin()->inner(new MysqlSelectQuery());
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAliasInSource()
    {
        $this->expectException(QueryException::class);

        $this->newJoin()->innerTo(new MysqlSelectQuery(), 'Table');
    }

    public function joins()
    {
        yield ['left'];
        yield ['right'];
        yield ['inner'];
    }

    protected function newJoin()
    {
        return new MysqlJoinClause();
    }
}