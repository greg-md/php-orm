<?php

namespace Greg\Orm\Model;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\ModelTestingAbstract;
use Greg\Orm\SqlException;

class ModelOrderByStrategyTest extends ModelTestingAbstract
{
    public function testCanOrderBy()
    {
        $query = $this->model->orderBy('Foo');

        $this->assertEquals('ORDER BY `Foo`', $query->toString());
    }

    public function testCanOrderByAsc()
    {
        $query = $this->model->orderAsc('Foo');

        $this->assertEquals('ORDER BY `Foo` ASC', $query->toString());
    }

    public function testCanOrderByDesc()
    {
        $query = $this->model->orderDesc('Foo');

        $this->assertEquals('ORDER BY `Foo` DESC', $query->toString());
    }

    public function testCanThrowExceptionIfUndefinedType()
    {
        $this->expectException(SqlException::class);

        $this->model->orderBy('Foo', 'undefined');
    }

    public function testCanGroupByRaw()
    {
        $query = $this->model->orderByRaw('`Foo`');

        $this->assertEquals('ORDER BY `Foo`', $query->toString());
    }

    public function testCanDetermineIfExists()
    {
        $this->assertFalse($this->model->hasOrderBy());

        $query = $this->model->orderBy('Foo');

        $this->assertTrue($query->hasOrderBy());
    }

    public function testCanGet()
    {
        $query = $this->model->orderBy('Foo');

        $this->assertCount(1, $query->getOrderBy());
    }

    public function testCanClear()
    {
        $query = $this->model->orderBy('Foo');

        $query->clearOrderBy();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->model->orderBy('Foo');

        $this->assertEquals('ORDER BY `Foo`', (string) $query);
    }

    public function testCanAssignOrderByAppliers()
    {
        $this->model->setOrderByApplier(function (OrderByClause $clause) {
            $clause->orderBy('Column1');
        });

        $query = $this->model->orderBy('Column2');

        $this->assertEquals('ORDER BY `Column1`, `Column2`', $query->toString());
    }

    public function testCanDetermineIfAppliersExists()
    {
        $this->assertFalse($this->model->hasOrderByAppliers());

        $this->model->setOrderByApplier(function () {
        });

        $this->assertTrue($this->model->hasOrderByAppliers());
    }

    public function testCanGetAppliers()
    {
        $this->model->setOrderByApplier(function () {
        });

        $this->assertCount(1, $this->model->getOrderByAppliers());
    }

    public function testCanClearAppliers()
    {
        $this->model->setOrderByApplier(function () {
        });

        $this->model->clearOrderByAppliers();

        $this->assertFalse($this->model->hasOrderByAppliers());
    }

    public function testCanDetermineIfOrderByExists()
    {
        $this->assertFalse($this->model->hasOrderBy());
    }

    public function testCanGetOrderBy()
    {
        $this->assertCount(0, $this->model->getOrderBy());
    }

    public function testCanClearOrderBy()
    {
        $this->model->clearOrderBy();

        $this->assertFalse($this->model->hasOrderBy());
    }

    public function testCanCombineClauses()
    {
        $this->assertTrue($this->model->select('Column')->orderBy('Column')->hasOrderBy());
    }

    public function testCanGetOrderByString()
    {
        $this->assertEquals('ORDER BY `Column`', $this->model->orderBy('Column')->orderByToString());
    }

    public function testCanGetEmptyOrderByString()
    {
        $this->assertEquals('', $this->model->orderByToString());
    }

    public function testCanDetermineIfClauseExists()
    {
        $this->assertFalse($this->model->hasOrderByClause());

        $this->model->intoOrderByStrategy();

        $this->assertTrue($this->model->hasOrderByClause());
    }

    public function testCanCombineClauses2()
    {
        $query = $this->model->from('Table')->orderBy('Column');

        $this->assertEquals('FROM `Table` ORDER BY `Column`', $query->toString());
    }
}
