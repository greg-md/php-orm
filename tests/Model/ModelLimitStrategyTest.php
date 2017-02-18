<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\LimitClauseTrait;
use Greg\Orm\Tests\ModelAbstract;

class ModelLimitStrategyTest extends ModelAbstract
{
    use LimitClauseTrait;

    public function testCanAssignLimitAppliers()
    {
        $this->model->setLimitApplier(function (LimitClause $clause) {
            $clause->limit(1);
        });

        $query = $this->model->where('Column', 'foo');

        $this->assertEquals('WHERE `Column` = ? LIMIT 1', $query->toString());

        $query = $this->model->limit(2);

        $this->assertEquals('LIMIT 2', $query->toString());
    }

    public function testCanDetermineIfAppliersExists()
    {
        $this->assertFalse($this->model->hasLimitAppliers());

        $this->model->setLimitApplier(function () {
        });

        $this->assertTrue($this->model->hasLimitAppliers());
    }

    public function testCanGetAppliers()
    {
        $this->model->setLimitApplier(function () {
        });

        $this->assertCount(1, $this->model->getLimitAppliers());
    }

    public function testCanClearAppliers()
    {
        $this->model->setLimitApplier(function () {
        });

        $this->model->clearLimitAppliers();

        $this->assertFalse($this->model->hasLimitAppliers());
    }

    public function testCanDetermineIfLimitExists()
    {
        $this->assertFalse($this->model->hasLimit());
    }

    public function testCanGetLimit()
    {
        $this->assertNull($this->model->getLimit());
    }

    public function testCanClearLimit()
    {
        $this->model->clearLimit();

        $this->assertFalse($this->model->hasLimit());
    }

    public function testCanCombineClauses()
    {
        $this->assertTrue($this->model->select('Column')->limit(1)->hasLimit());
    }

    protected function newClause()
    {
        $this->model->limitStrategy();

        return $this->model;
    }

    protected function newSelectQuery(): SelectQuery
    {
        return $this->driver->select();
    }
}
