<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Tests\Clause\OrderByClauseTrait;
use Greg\Orm\Tests\ModelAbstract;

class ModelOrderByStrategyTest extends ModelAbstract
{
    use OrderByClauseTrait;

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

    protected function newClause()
    {
        $this->model->orderByStrategy();

        return $this->model;
    }
}
