<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\QueryException;
use Greg\Orm\Tests\Clause\OffsetClauseTrait;
use Greg\Orm\Tests\ModelAbstract;

class ModelOffsetStrategyTest extends ModelAbstract
{
    use OffsetClauseTrait;

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
        $this->expectException(QueryException::class);

        $this->model->updateTable('Column')->offset(1);
    }

    protected function newClause()
    {
        return $this->model->setClause('OFFSET', $this->model->driver()->offset());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return $this->driver->select();
    }
}
