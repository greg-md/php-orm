<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Model;
use Greg\Orm\SqlException;

trait OffsetTableClauseTraitTest
{
    public function testCanDetermineIfOffsetExists()
    {
        $this->assertFalse($this->model()->hasOffset());

        $query = $this->model()->offset(10);

        $this->assertTrue($query->hasOffset());
    }

    public function testCanGetOffset()
    {
        $query = $this->model()->offset(10);

        $this->assertEquals(10, $query->getOffset());
    }

    public function testCanClearOffset()
    {
        $query = $this->model()->offset(10);

        $query->clearOffset();

        $this->assertNull($query->getOffset());
    }

    public function testCanAssignOffsetAppliers()
    {
        $this->model()->setOffsetApplier(function (OffsetClause $clause) {
            $clause->offset(1);
        });

        $query = $this->model()->where('Column', 'foo');

        $this->assertEquals('WHERE `Column` = ? OFFSET 1', $query->toString());

        $query = $this->model()->offset(2);

        $this->assertEquals('OFFSET 2', $query->toString());
    }

    public function testCanDetermineIfOffsetAppliersExists()
    {
        $this->assertFalse($this->model()->hasOffsetAppliers());

        $this->model()->setOffsetApplier(function () {
        });

        $this->assertTrue($this->model()->hasOffsetAppliers());
    }

    public function testCanGetOffsetAppliers()
    {
        $this->model()->setOffsetApplier(function () {
        });

        $this->assertCount(1, $this->model()->getOffsetAppliers());
    }

    public function testCanClearOffsetAppliers()
    {
        $this->model()->setOffsetApplier(function () {
        });

        $this->model()->clearOffsetAppliers();

        $this->assertFalse($this->model()->hasOffsetAppliers());
    }

    public function testCanDetermineIfNoOffsetExists()
    {
        $this->assertFalse($this->model()->hasOffset());
    }

    public function testCanGetEmptyOffset()
    {
        $this->assertNull($this->model()->getOffset());
    }

    public function testCanClearEmptyOffset()
    {
        $this->model()->clearOffset();

        $this->assertFalse($this->model()->hasOffset());
    }

    public function testCanCombineClausesWithOffset()
    {
        $this->assertTrue($this->model()->select('Column')->offset(1)->hasOffset());
    }

    public function testCanThrowExceptionIfOffsetNotExists()
    {
        $this->expectException(SqlException::class);

        $this->model()->updateTable('Column')->offset(1);
    }

    public function testCanDetermineIfClauseExists()
    {
        $this->assertFalse($this->model()->hasOffsetClause());

        $this->model()->intoOffsetStrategy();

        $this->assertTrue($this->model()->hasOffsetClause());
    }

    public function testCanCombineClausesWithOffset2()
    {
        $query = $this->model()->limit(10)->offset(10);

        $this->assertEquals('LIMIT 10 OFFSET 10', $query->toString());
    }

    abstract protected function model(): Model;
}
