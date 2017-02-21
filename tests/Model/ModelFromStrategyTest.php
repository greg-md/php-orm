<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\QueryException;
use Greg\Orm\Tests\Clause\FromClauseTrait;
use Greg\Orm\Tests\ModelAbstract;

class ModelFromStrategyTest extends ModelAbstract
{
    use FromClauseTrait;

    public function testCanAssignFromAppliers()
    {
        $this->model->setFromApplier(function (FromClause $clause) {
            $clause->from('Table2');
        });

        $query = $this->model->from('Table1');

        $this->assertEquals('FROM `Table2`, `Table1`', $query->toString());
    }

    public function testCanDetermineIfAppliersExists()
    {
        $this->assertFalse($this->model->hasFromAppliers());

        $this->model->setFromApplier(function () {
        });

        $this->assertTrue($this->model->hasFromAppliers());
    }

    public function testCanGetAppliers()
    {
        $this->model->setFromApplier(function () {
        });

        $this->assertCount(1, $this->model->getFromAppliers());
    }

    public function testCanClearAppliers()
    {
        $this->model->setFromApplier(function () {
        });

        $this->model->clearFromAppliers();

        $this->assertFalse($this->model->hasFromAppliers());
    }

    public function testCanDetermineIfFromExists()
    {
        $this->assertFalse($this->model->hasFrom());
    }

    public function testCanGetFrom()
    {
        $this->assertCount(0, $this->model->getFrom());
    }

    public function testCanClearFrom()
    {
        $this->model->clearFrom();

        $this->assertFalse($this->model->hasFrom());
    }

    public function testCanGetStringClause()
    {
        $query = $this->model->from('Table');

        $this->assertEquals('FROM `Table`', $query->fromToString());
    }

    public function testCanGetEmptyClause()
    {
        $this->assertEquals('', $this->model->fromToString());
    }

    public function testCanCombineClauses()
    {
        $this->assertTrue($this->model->select('Column')->from('Table2')->hasFrom());
    }

    public function testCanThrowExceptionIfFromNotExists()
    {
        $this->expectException(QueryException::class);

        $this->model->updateTable('Column')->from('Table2');
    }

    public function testCanJoin()
    {
        $join = $this->newJoinClause()->innerTo('Foo', 'Bar');

        $query = $this->newClause()->from('Foo');

        $this->assertEquals('FROM `Foo` INNER JOIN `Bar`', $query->fromToString($join->joinStrategy()));
    }

    protected function newClause()
    {
        return $this->model->setClause('FROM', $this->model->driver()->from());
    }

    protected function newJoinClause()
    {
        return $this->model->setClause('JOIN', $this->model->driver()->join());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return $this->driver->select();
    }
}
