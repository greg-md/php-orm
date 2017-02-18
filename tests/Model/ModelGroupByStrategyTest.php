<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\QueryException;
use Greg\Orm\Tests\Clause\GroupByClauseTrait;
use Greg\Orm\Tests\ModelAbstract;

class ModelGroupByStrategyTest extends ModelAbstract
{
    use GroupByClauseTrait;

    public function testCanAssignGroupByAppliers()
    {
        $this->model->setGroupByApplier(function (GroupByClause $clause) {
            $clause->groupBy('Column1');
        });

        $query = $this->model->groupBy('Column2');

        $this->assertEquals('GROUP BY `Column1`, `Column2`', $query->toString());
    }

    public function testCanDetermineIfAppliersExists()
    {
        $this->assertFalse($this->model->hasGroupByAppliers());

        $this->model->setGroupByApplier(function () {
        });

        $this->assertTrue($this->model->hasGroupByAppliers());
    }

    public function testCanGetAppliers()
    {
        $this->model->setGroupByApplier(function () {
        });

        $this->assertCount(1, $this->model->getGroupByAppliers());
    }

    public function testCanClearAppliers()
    {
        $this->model->setGroupByApplier(function () {
        });

        $this->model->clearGroupByAppliers();

        $this->assertFalse($this->model->hasGroupByAppliers());
    }

    public function testCanDetermineIfGroupByExists()
    {
        $this->assertFalse($this->model->hasGroupBy());
    }

    public function testCanGetGroupBy()
    {
        $this->assertCount(0, $this->model->getGroupBy());
    }

    public function testCanClearGroupBy()
    {
        $this->model->clearGroupBy();

        $this->assertFalse($this->model->hasGroupBy());
    }

    public function testCanCombineClauses()
    {
        $this->assertTrue($this->model->select('Column')->groupBy('Table2')->hasGroupBy());
    }

    public function testCanThrowExceptionIfGroupByNotExists()
    {
        $this->expectException(QueryException::class);

        $this->model->updateTable('Column')->groupBy('Table2');
    }

    protected function newClause()
    {
        $this->model->groupByStrategy();

        return $this->model;
    }
}
