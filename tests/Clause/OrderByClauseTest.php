<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlException;
use PHPUnit\Framework\TestCase;

class OrderByClauseTest extends TestCase
{
    public function testCanOrderBy()
    {
        $query = $this->newClause()->orderBy('Foo');

        $this->assertEquals('ORDER BY `Foo`', $query->toString());
    }

    public function testCanOrderByAsc()
    {
        $query = $this->newClause()->orderAsc('Foo');

        $this->assertEquals('ORDER BY `Foo` ASC', $query->toString());
    }

    public function testCanOrderByDesc()
    {
        $query = $this->newClause()->orderDesc('Foo');

        $this->assertEquals('ORDER BY `Foo` DESC', $query->toString());
    }

    public function testCanThrowExceptionIfUndefinedType()
    {
        $this->expectException(SqlException::class);

        $this->newClause()->orderBy('Foo', 'undefined');
    }

    public function testCanGroupByRaw()
    {
        $query = $this->newClause()->orderByRaw('`Foo`');

        $this->assertEquals('ORDER BY `Foo`', $query->toString());
    }

    public function testCanDetermineIfExists()
    {
        $query = $this->newClause();

        $this->assertFalse($query->hasOrderBy());

        $query->orderBy('Foo');

        $this->assertTrue($query->hasOrderBy());
    }

    public function testCanGet()
    {
        $query = $this->newClause();

        $query->orderBy('Foo');

        $this->assertCount(1, $query->getOrderBy());
    }

    public function testCanClear()
    {
        $query = $this->newClause();

        $query->orderBy('Foo');

        $query->clearOrderBy();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newClause()->orderBy('Foo');

        $this->assertEquals('ORDER BY `Foo`', (string) $query);
    }

    protected function newClause(): OrderByClause
    {
        return new OrderByClause();
    }
}
