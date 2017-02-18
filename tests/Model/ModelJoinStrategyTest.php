<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\JoinClauseTrait;
use Greg\Orm\Tests\ModelAbstract;

class ModelJoinStrategyTest extends ModelAbstract
{
    use JoinClauseTrait;

    public function testCanAssignJoinAppliers()
    {
        $this->model->setJoinApplier(function (JoinClause $clause) {
            $clause->inner('Table1');
        });

        $query = $this->model->inner('Table2');

        $this->assertEquals('INNER JOIN `Table1` INNER JOIN `Table2`', $query->toString());
    }

    public function testCanDetermineIfAppliersExists()
    {
        $this->assertFalse($this->model->hasJoinAppliers());

        $this->model->setJoinApplier(function () {
        });

        $this->assertTrue($this->model->hasJoinAppliers());
    }

    public function testCanGetAppliers()
    {
        $this->model->setJoinApplier(function () {
        });

        $this->assertCount(1, $this->model->getJoinAppliers());
    }

    public function testCanClearAppliers()
    {
        $this->model->setJoinApplier(function () {
        });

        $this->model->clearJoinAppliers();

        $this->assertFalse($this->model->hasJoinAppliers());
    }

    public function testCanDetermineIfJoinExists()
    {
        $this->assertFalse($this->model->hasJoin());
    }

    public function testCanGetJoin()
    {
        $this->assertCount(0, $this->model->getJoin());
    }

    public function testCanClearJoin()
    {
        $this->model->clearJoin();

        $this->assertFalse($this->model->hasJoin());
    }

    public function testCanGetJoinString()
    {
        $this->assertEquals('INNER JOIN `Table`', $this->model->inner('Table')->joinToString());
    }

    public function testCanGetEmptyJoinString()
    {
        $this->assertEquals('', $this->model->joinToString());
    }

    public function testCanCombineClauses()
    {
        $this->assertTrue($this->model->select('Column')->inner('Table2')->hasJoin());
    }

    protected function newClause()
    {
        $this->model->joinStrategy();

        return $this->model;
    }

    protected function newSelectQuery(): SelectQuery
    {
        return $this->driver->select();
    }
}
