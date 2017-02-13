<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Mysql\Clause\MysqlFromClause;
use Greg\Orm\Driver\Mysql\Query\MysqlSelectQuery;
use Greg\Orm\QueryException;
use PHPUnit\Framework\TestCase;

class MysqlFromClauseTest extends TestCase
{
    public function testCanAddFrom()
    {
        $query = $this->newFrom()->from('Foo');

        $this->assertEquals('FROM `Foo`', $query->toString());
    }

    public function testCanAddFromWithAlias()
    {
        $query = $this->newFrom()->from(['f' => 'Foo']);

        $this->assertEquals('FROM `Foo` AS `f`', $query->toString());
    }

    public function testCanAddFromRaw()
    {
        $query = $this->newFrom()->fromRaw('f', '`Foo`');

        $this->assertEquals('FROM `Foo` AS `f`', $query->toString());
    }

    public function testCanDetermineIfExists()
    {
        $query = $this->newFrom();

        $this->assertFalse($query->hasFrom());

        $query->from('Foo');

        $this->assertTrue($query->hasFrom());
    }

    public function testCanGet()
    {
        $query = $this->newFrom();

        $query->from('Foo');

        $this->assertCount(1, $query->getFrom());
    }

    public function testCanClear()
    {
        $query = $this->newFrom();

        $query->from('Foo');

        $query->clearFrom();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanJoin()
    {
        $query = $this->newFrom()->from('Foo')->inner('Bar');

        $this->assertEquals('FROM `Foo` INNER JOIN `Bar`', $query->toString());
    }

    public function testCanJoinTo()
    {
        $query = $this->newFrom()->from('Foo')->innerTo('Foo', 'Bar');

        $this->assertEquals('FROM `Foo` INNER JOIN `Bar`', $query->toString());
    }

    public function testCanNotUseJoinsWithoutFrom()
    {
        $this->expectException(QueryException::class);

        $this->newFrom()->inner('Foo')->toString();
    }

    public function testCanTransformToString()
    {
        $query = $this->newFrom()->from('Foo');

        $this->assertEquals('FROM `Foo`', (string) $query);
    }

    public function testCanCombineClauses()
    {
        $query = $this->newFrom()
            ->from(['t' => new MysqlSelectQuery()])
            ->innerOn('Table', function (ConditionsStrategy $strategy) {
                $strategy->isNull('Column');
            });

        $this->assertEquals('FROM (SELECT *) AS `t` INNER JOIN `Table` ON `Column` IS NULL', $query->toString());
    }

    public function testCanThrowExceptionIfDerivedTableNotHaveAlias()
    {
        $this->expectException(QueryException::class);

        $this->newFrom()->from(new MysqlSelectQuery());
    }

    protected function newFrom()
    {
        return new MysqlFromClause();
    }
}
