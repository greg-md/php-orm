<?php

namespace Greg\Orm\Model;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\ModelTestingAbstract;
use Greg\Orm\SqlException;

class ModelGroupByStrategyTest extends ModelTestingAbstract
{
    public function testCanGroupBy()
    {
        $query = $this->model->groupBy('Foo');

        $this->assertEquals('GROUP BY `Foo`', $query->toString());
    }

    public function testCanGroupByRaw()
    {
        $query = $this->model->groupByRaw('`Foo`');

        $this->assertEquals('GROUP BY `Foo`', $query->toString());
    }

    public function testCanDetermineIfExists()
    {
        $this->assertFalse($this->model->hasGroupBy());

        $query = $this->model->groupBy('Foo');

        $this->assertTrue($query->hasGroupBy());
    }

    public function testCanDetermineIfClauseExists()
    {
        $this->assertFalse($this->model->hasGroupByClause());

        $query = $this->model->groupBy('Foo');

        $this->assertTrue($query->hasGroupByClause());
    }

    public function testCanGet()
    {
        $query = $this->model->groupBy('Foo');

        $this->assertCount(1, $query->getGroupBy());
    }

    public function testCanClear()
    {
        $query = $this->model->groupBy('Foo');

        $query->clearGroupBy();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->model->groupBy('Foo');

        $this->assertEquals('GROUP BY `Foo`', (string) $query);
    }

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

    public function testCanGetStringClause()
    {
        $query = $this->model->groupBy('Column');

        $this->assertEquals('GROUP BY `Column`', $query->groupByToString());
    }

    public function testCanGetEmptyClause()
    {
        $this->assertEquals('', $this->model->groupByToString());
    }

    public function testCanCombineClauses()
    {
        $this->assertTrue($this->model->select('Column')->groupBy('Column')->hasGroupBy());
    }

    public function testCanThrowExceptionIfGroupByNotExists()
    {
        $this->expectException(SqlException::class);

        $this->model->updateTable('Column')->groupBy('Column');
    }
}
