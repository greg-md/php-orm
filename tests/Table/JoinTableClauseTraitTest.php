<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Conditions;
use Greg\Orm\Model;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;

trait JoinTableClauseTraitTest
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
        /** @var Model $query */
        $query = $this->model()->{$type}('Foo');

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
        /** @var Model $query */
        $query = $this->model()->{$type}('Foo', '`Foo`.`Id` = !Bar.Id');

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
        /** @var Model $query */
        $query = $this->model()->{$type . 'On'}('Foo', function (Conditions $query) {
            $query->relation('Foo.Id', 'Bar.Id');
        });

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->toString());
    }

    public function testCanCross()
    {
        $query = $this->model()->cross('Foo');

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
        /** @var Model $query */
        $query = $this->model()->{$type . 'To'}('bar', 'Foo');

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
        /** @var Model $query */
        $query = $this->model()->{$type . 'To'}('bar', 'Foo', '`Foo`.`Id` = !Bar.Id');

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
        /** @var Model $query */
        $query = $this->model()->{$type . 'ToOn'}('bar', 'Foo', function (Conditions $query) {
            $query->relation('Foo.Id', 'Bar.Id');
        });

        $this->assertEquals(strtoupper($type) . ' JOIN `Foo` ON `Foo`.`Id` = `Bar`.`Id`', $query->joinToString('bar'));
    }

    public function testCanCrossTo()
    {
        $query = $this->model()->crossTo('bar', 'Foo');

        $this->assertEquals('CROSS JOIN `Foo`', $query->joinToString('bar'));
    }

    public function testCanDetermineIfJoinExists()
    {
        $this->assertFalse($this->model()->hasJoin());

        $query = $this->model()->inner('Foo');

        $this->assertTrue($query->hasJoin());
    }

    public function testCanDetermineIfJoinClauseExists()
    {
        $this->assertFalse($this->model()->hasJoinClause());

        $query = $this->model()->inner('Foo');

        $this->assertTrue($query->hasJoinClause());
    }

    public function testCanGetJoin()
    {
        $query = $this->model()->inner('Foo');

        $this->assertCount(1, $query->getJoin());
    }

    public function testCanClearJoin()
    {
        $query = $this->model()->inner('Foo');

        $query->clearJoin();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanJoinWithAlias()
    {
        $query = $this->model()->inner(['f' => 'Foo']);

        $this->assertEquals('INNER JOIN `Foo` AS `f`', $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->model()->inner(['f' => 'Foo']);

        $this->assertEquals('INNER JOIN `Foo` AS `f`', (string) $query);
    }

    public function testCanCombineClausesWithJoin()
    {
        $query = $this->model()->inner(['t' => new SelectQuery()]);

        $this->assertEquals('INNER JOIN (SELECT *) AS `t`', $query->toString());
    }

    public function testCanThrowExceptionIfJoinDerivedTableNotHaveAlias()
    {
        $this->expectException(SqlException::class);

        $this->model()->inner(new SelectQuery());
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAliasInSource()
    {
        $this->expectException(SqlException::class);

        $this->model()->innerTo(new SelectQuery(), 'Table');
    }

    public function joins()
    {
        yield ['left'];
        yield ['right'];
        yield ['inner'];
    }

    public function testCanAssignJoinAppliers()
    {
        $this->model()->setJoinApplier(function (JoinClause $clause) {
            $clause->innerJoin('Table1');
        });

        $query = $this->model()->inner('Table2');

        $this->assertEquals('INNER JOIN `Table1` INNER JOIN `Table2`', $query->toString());
    }

    public function testCanDetermineIfJoinAppliersExists()
    {
        $this->assertFalse($this->model()->hasJoinAppliers());

        $this->model()->setJoinApplier(function () {
        });

        $this->assertTrue($this->model()->hasJoinAppliers());
    }

    public function testCanGetJoinAppliers()
    {
        $this->model()->setJoinApplier(function () {
        });

        $this->assertCount(1, $this->model()->getJoinAppliers());
    }

    public function testCanClearJoinAppliers()
    {
        $this->model()->setJoinApplier(function () {
        });

        $this->model()->clearJoinAppliers();

        $this->assertFalse($this->model()->hasJoinAppliers());
    }

    public function testCanDetermineIfJoinExists2()
    {
        $this->assertFalse($this->model()->hasJoin());
    }

    public function testCanGetEmptyJoin()
    {
        $this->assertCount(0, $this->model()->getJoin());
    }

    public function testCanClearEmptyJoin()
    {
        $this->model()->clearJoin();

        $this->assertFalse($this->model()->hasJoin());
    }

    public function testCanGetJoinString()
    {
        $this->assertEquals('INNER JOIN `Table`', $this->model()->inner('Table')->joinToString());
    }

    public function testCanGetEmptyJoinString()
    {
        $this->assertEquals('', $this->model()->joinToString());
    }

    public function testCanCombineClausesWithJoin2()
    {
        $this->assertTrue($this->model()->select('Column')->inner('Table2')->hasJoin());
    }

    public function testCanTransformIntoJoin()
    {
        $this->model()->intoJoinStrategy();

        $this->assertTrue($this->model()->hasJoinClause());
    }

    public function testCanCombineClauses3()
    {
        $this->assertEquals('FROM `Table1` INNER JOIN `Table2`', $this->model()->from('Table1')->inner('Table2')->toString());
    }

    abstract protected function model(): Model;
}
