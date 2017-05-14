<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Model;

trait LimitTableClauseTraitTest
{
    public function testCanDetermineIfLimitExists()
    {
        $this->assertFalse($this->model()->hasLimit());

        $query = $this->model()->limit(10);

        $this->assertTrue($query->hasLimit());
    }

    public function testCanGetLimit()
    {
        $query = $this->model()->limit(10);

        $this->assertEquals(10, $query->getLimit());
    }

    public function testCanClearLimit()
    {
        $query = $this->model()->limit(10);

        $query->clearLimit();

        $this->assertNull($query->getLimit());
    }

    public function testCanAssignLimitAppliers()
    {
        $this->model()->setLimitApplier(function (LimitClause $clause) {
            $clause->limit(1);
        });

        $query = $this->model()->where('Column', 'foo');

        $this->assertEquals('WHERE `Column` = ? LIMIT 1', $query->toString());

        $query = $this->model()->limit(2);

        $this->assertEquals('LIMIT 2', $query->toString());
    }

    public function testCanDetermineIfLimitAppliersExists()
    {
        $this->assertFalse($this->model()->hasLimitAppliers());

        $this->model()->setLimitApplier(function () {
        });

        $this->assertTrue($this->model()->hasLimitAppliers());
    }

    public function testCanGetLimitAppliers()
    {
        $this->model()->setLimitApplier(function () {
        });

        $this->assertCount(1, $this->model()->getLimitAppliers());
    }

    public function testCanClearLimitAppliers()
    {
        $this->model()->setLimitApplier(function () {
        });

        $this->model()->clearLimitAppliers();

        $this->assertFalse($this->model()->hasLimitAppliers());
    }

    public function testCanDetermineIfNoLimitExists()
    {
        $this->assertFalse($this->model()->hasLimit());
    }

    public function testCanGetEmptyLimit()
    {
        $this->assertNull($this->model()->getLimit());
    }

    public function testCanClearEmptyLimit()
    {
        $this->model()->clearLimit();

        $this->assertFalse($this->model()->hasLimit());
    }

    public function testCanCombineClausesWithLimit()
    {
        $this->assertTrue($this->model()->select('Column')->limit(1)->hasLimit());
    }

    public function testCanDetermineIfClauseExists()
    {
        $this->assertFalse($this->model()->hasLimitClause());

        $this->model()->intoLimitStrategy();

        $this->assertTrue($this->model()->hasLimitClause());
    }

    public function testCanCombineClausesWithLimit2()
    {
        $query = $this->model()->offset(10)->limit(10);

        $this->assertEquals('LIMIT 10 OFFSET 10', $query->toString());
    }

    abstract protected function model(): Model;
}
