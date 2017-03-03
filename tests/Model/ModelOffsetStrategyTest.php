<?php

namespace Greg\Orm\Model;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\ModelTestingAbstract;
use Greg\Orm\SqlException;

class ModelOffsetStrategyTest extends ModelTestingAbstract
{
    public function testCanDetermineIfExists()
    {
        $this->assertFalse($this->model->hasOffset());

        $query = $this->model->offset(10);

        $this->assertTrue($query->hasOffset());
    }

    public function testCanGet()
    {
        $query = $this->model->offset(10);

        $this->assertEquals(10, $query->getOffset());
    }

    public function testCanClear()
    {
        $query = $this->model->offset(10);

        $query->clearOffset();

        $this->assertNull($query->getOffset());
    }

    public function testCanAssignOffsetAppliers()
    {
        $this->model->setOffsetApplier(function (OffsetClause $clause) {
            $clause->offset(1);
        });

        $query = $this->model->where('Column', 'foo');

        $this->assertEquals('WHERE `Column` = ? OFFSET 1', $query->toString());

        $query = $this->model->offset(2);

        $this->assertEquals('OFFSET 2', $query->toString());
    }

    public function testCanDetermineIfAppliersExists()
    {
        $this->assertFalse($this->model->hasOffsetAppliers());

        $this->model->setOffsetApplier(function () {
        });

        $this->assertTrue($this->model->hasOffsetAppliers());
    }

    public function testCanGetAppliers()
    {
        $this->model->setOffsetApplier(function () {
        });

        $this->assertCount(1, $this->model->getOffsetAppliers());
    }

    public function testCanClearAppliers()
    {
        $this->model->setOffsetApplier(function () {
        });

        $this->model->clearOffsetAppliers();

        $this->assertFalse($this->model->hasOffsetAppliers());
    }

    public function testCanDetermineIfOffsetExists()
    {
        $this->assertFalse($this->model->hasOffset());
    }

    public function testCanGetOffset()
    {
        $this->assertNull($this->model->getOffset());
    }

    public function testCanClearOffset()
    {
        $this->model->clearOffset();

        $this->assertFalse($this->model->hasOffset());
    }

    public function testCanCombineClauses()
    {
        $this->assertTrue($this->model->select('Column')->offset(1)->hasOffset());
    }

    public function testCanThrowExceptionIfFromNotExists()
    {
        $this->expectException(SqlException::class);

        $this->model->updateTable('Column')->offset(1);
    }

    public function testCanDetermineIfClauseExists()
    {
        $this->assertFalse($this->model->hasOffsetClause());

        $this->model->intoOffsetStrategy();

        $this->assertTrue($this->model->hasOffsetClause());
    }

    public function testCanCombineClauses2()
    {
        $query = $this->model->limit(10)->offset(10);

        $this->assertEquals('LIMIT 10 OFFSET 10', $query->toString());
    }
}
