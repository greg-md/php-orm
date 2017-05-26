<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Model;
use Greg\Orm\SqlException;

trait GroupByTableClauseTraitTest
{
    public function testCanGroupBy()
    {
        $query = $this->model()->groupBy('Foo');

        $this->assertEquals('GROUP BY `Foo`', $query->toString());
    }

    public function testCanGroupByRaw()
    {
        $query = $this->model()->groupByRaw('`Foo`');

        $this->assertEquals('GROUP BY `Foo`', $query->toString());
    }

    public function testCanDetermineIfExistsGroupBy()
    {
        $this->assertFalse($this->model()->hasGroupBy());

        $query = $this->model()->groupBy('Foo');

        $this->assertTrue($query->hasGroupBy());
    }

    public function testCanDetermineIfGroupByClauseExists()
    {
        $this->assertFalse($this->model()->hasGroupByClause());

        $query = $this->model()->groupBy('Foo');

        $this->assertTrue($query->hasGroupByClause());
    }

    public function testCanGetGroupBy()
    {
        $query = $this->model()->groupBy('Foo');

        $this->assertCount(1, $query->getGroupBy());
    }

    public function testCanClearGroupBy()
    {
        $query = $this->model()->groupBy('Foo');

        $query->clearGroupBy();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->model()->groupBy('Foo');

        $this->assertEquals('GROUP BY `Foo`', (string) $query);
    }

    public function testCanAssignGroupByAppliers()
    {
        $this->model()->setGroupByApplier(function (GroupByClause $clause) {
            $clause->groupBy('Column1');
        });

        $query = $this->model()->groupBy('Column2');

        $this->assertEquals('GROUP BY `Column1`, `Column2`', $query->toString());
    }

    public function testCanDetermineIfGroupByAppliersExists()
    {
        $this->assertFalse($this->model()->hasGroupByAppliers());

        $this->model()->setGroupByApplier(function () {
        });

        $this->assertTrue($this->model()->hasGroupByAppliers());
    }

    public function testCanGetGroupByAppliers()
    {
        $this->model()->setGroupByApplier(function () {
        });

        $this->assertCount(1, $this->model()->getGroupByAppliers());
    }

    public function testCanClearGroupByAppliers()
    {
        $this->model()->setGroupByApplier(function () {
        });

        $this->model()->clearGroupByAppliers();

        $this->assertFalse($this->model()->hasGroupByAppliers());
    }

    public function testCanDetermineIfGroupByExists()
    {
        $this->assertFalse($this->model()->hasGroupBy());
    }

    public function testCanGetEmptyGroupBy()
    {
        $this->assertCount(0, $this->model()->getGroupBy());
    }

    public function testCanClearEmptyGroupBy()
    {
        $this->model()->clearGroupBy();

        $this->assertFalse($this->model()->hasGroupBy());
    }

    public function testCanGetStringGroupByClause()
    {
        $query = $this->model()->groupBy('Column');

        $this->assertEquals('GROUP BY `Column`', $query->groupByToString());
    }

    public function testCanGetEmptyGroupByClause()
    {
        $this->assertEquals('', $this->model()->groupByToString());
    }

    public function testCanCombineClausesWithGroupBy()
    {
        $this->assertTrue($this->model()->select('Column')->groupBy('Column')->hasGroupBy());
    }

    public function testCanThrowExceptionIfGroupByNotExists()
    {
        $this->expectException(SqlException::class);

        $this->model()->updateTable('Column')->groupBy('Column');
    }

    abstract protected function model(): Model;
}
