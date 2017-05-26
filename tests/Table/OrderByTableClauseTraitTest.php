<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Model;
use Greg\Orm\SqlException;

trait OrderByTableClauseTraitTest
{
    public function testCanOrderBy()
    {
        $query = $this->model()->orderBy('Foo');

        $this->assertEquals('ORDER BY `Foo`', $query->toString());
    }

    public function testCanOrderByAsc()
    {
        $query = $this->model()->orderAsc('Foo');

        $this->assertEquals('ORDER BY `Foo` ASC', $query->toString());
    }

    public function testCanOrderByDesc()
    {
        $query = $this->model()->orderDesc('Foo');

        $this->assertEquals('ORDER BY `Foo` DESC', $query->toString());
    }

    public function testCanThrowExceptionIfUndefinedType()
    {
        $this->expectException(SqlException::class);

        $this->model()->orderBy('Foo', 'undefined');
    }

    public function testCanOrderByRaw()
    {
        $query = $this->model()->orderByRaw('`Foo`');

        $this->assertEquals('ORDER BY `Foo`', $query->toString());
    }

    public function testCanDetermineIfOrderByExists()
    {
        $this->assertFalse($this->model()->hasOrderBy());

        $query = $this->model()->orderBy('Foo');

        $this->assertTrue($query->hasOrderBy());
    }

    public function testCanGetOrderBy()
    {
        $query = $this->model()->orderBy('Foo');

        $this->assertCount(1, $query->getOrderBy());
    }

    public function testCanClearOrderBy()
    {
        $query = $this->model()->orderBy('Foo');

        $query->clearOrderBy();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->model()->orderBy('Foo');

        $this->assertEquals('ORDER BY `Foo`', (string) $query);
    }

    public function testCanAssignOrderByAppliers()
    {
        $this->model()->setOrderByApplier(function (OrderByClause $clause) {
            $clause->orderBy('Column1');
        });

        $query = $this->model()->orderBy('Column2');

        $this->assertEquals('ORDER BY `Column1`, `Column2`', $query->toString());
    }

    public function testCanDetermineIfOrderByAppliersExists()
    {
        $this->assertFalse($this->model()->hasOrderByAppliers());

        $this->model()->setOrderByApplier(function () {
        });

        $this->assertTrue($this->model()->hasOrderByAppliers());
    }

    public function testCanGetOrderByAppliers()
    {
        $this->model()->setOrderByApplier(function () {
        });

        $this->assertCount(1, $this->model()->getOrderByAppliers());
    }

    public function testCanClearOrderByAppliers()
    {
        $this->model()->setOrderByApplier(function () {
        });

        $this->model()->clearOrderByAppliers();

        $this->assertFalse($this->model()->hasOrderByAppliers());
    }

    public function testCanDetermineIfNoOrderByExists()
    {
        $this->assertFalse($this->model()->hasOrderBy());
    }

    public function testCanGetEmptyOrderBy()
    {
        $this->assertCount(0, $this->model()->getOrderBy());
    }

    public function testCanClearEmptyOrderBy()
    {
        $this->model()->clearOrderBy();

        $this->assertFalse($this->model()->hasOrderBy());
    }

    public function testCanCombineClausesWithOrderBy()
    {
        $this->assertTrue($this->model()->select('Column')->orderBy('Column')->hasOrderBy());
    }

    public function testCanGetOrderByString()
    {
        $this->assertEquals('ORDER BY `Column`', $this->model()->orderBy('Column')->orderByToString());
    }

    public function testCanGetEmptyOrderByString()
    {
        $this->assertEquals('', $this->model()->orderByToString());
    }

    public function testCanDetermineIfOrderByClauseExists()
    {
        $this->assertFalse($this->model()->hasOrderByClause());

        $this->model()->intoOrderByStrategy();

        $this->assertTrue($this->model()->hasOrderByClause());
    }

    public function testCanCombineClausesWithOrderBy2()
    {
        $query = $this->model()->from('Table')->orderBy('Column');

        $this->assertEquals('FROM `Table` ORDER BY `Column`', $query->toString());
    }

    abstract protected function model(): Model;
}
