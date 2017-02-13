<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Driver\Mysql\Clause\MysqlOrderByClause;
use Greg\Orm\QueryException;
use PHPUnit\Framework\TestCase;

class MysqlOrderByClauseTest extends TestCase
{
    public function testCanOrderBy()
    {
        $query = $this->newOrderBy()->orderBy('Foo');

        $this->assertEquals('ORDER BY `Foo`', $query->toString());
    }

    public function testCanOrderByAsc()
    {
        $query = $this->newOrderBy()->orderAsc('Foo');

        $this->assertEquals('ORDER BY `Foo` ASC', $query->toString());
    }

    public function testCanOrderByDesc()
    {
        $query = $this->newOrderBy()->orderDesc('Foo');

        $this->assertEquals('ORDER BY `Foo` DESC', $query->toString());
    }

    public function testCanThrowExceptionIfUndefinedType()
    {
        $this->expectException(QueryException::class);

        $this->newOrderBy()->orderBy('Foo', 'undefined');
    }

    public function testCanGroupByRaw()
    {
        $query = $this->newOrderBy()->orderByRaw('`Foo`');

        $this->assertEquals('ORDER BY `Foo`', $query->toString());
    }

    public function testCanDetermineIfExists()
    {
        $query = $this->newOrderBy();

        $this->assertFalse($query->hasOrderBy());

        $query->orderBy('Foo');

        $this->assertTrue($query->hasOrderBy());
    }

    public function testCanGet()
    {
        $query = $this->newOrderBy();

        $query->orderBy('Foo');

        $this->assertCount(1, $query->getOrderBy());
    }

    public function testCanClear()
    {
        $query = $this->newOrderBy();

        $query->orderBy('Foo');

        $query->clearOrderBy();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newOrderBy()->orderBy('Foo');

        $this->assertEquals('ORDER BY `Foo`', (string) $query);
    }

    protected function newOrderBy()
    {
        return new MysqlOrderByClause();
    }
}