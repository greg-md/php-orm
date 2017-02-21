<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Conditions;
use Greg\Orm\Tests\ConditionsTrait;
use Greg\Orm\Tests\ModelAbstract;

class ModelWhereStrategyTest extends ModelAbstract
{
    use ConditionsTrait;

    protected $prefix = 'WHERE ';

    protected $methods = [
        'column'       => 'where',
        'orColumn'     => 'orWhere',
        'columns'      => 'whereMultiple',
        'orColumns'    => 'orWhereMultiple',
        'date'         => 'whereDate',
        'orDate'       => 'orWhereDate',
        'time'         => 'whereTime',
        'orTime'       => 'orWhereTime',
        'year'         => 'whereYear',
        'orYear'       => 'orWhereYear',
        'month'        => 'whereMonth',
        'orMonth'      => 'orWhereMonth',
        'day'          => 'whereDay',
        'orDay'        => 'orWhereDay',
        'relation'     => 'whereRelation',
        'orRelation'   => 'orWhereRelation',
        'relations'    => 'whereRelations',
        'orRelations'  => 'orWhereRelations',
        'isNull'       => 'whereIsNull',
        'orIsNull'     => 'orWhereIsNull',
        'isNotNull'    => 'whereIsNotNull',
        'orIsNotNull'  => 'orWhereIsNotNull',
        'between'      => 'whereBetween',
        'orBetween'    => 'orWhereBetween',
        'notBetween'   => 'whereNotBetween',
        'orNotBetween' => 'orWhereNotBetween',
        'group'        => 'whereGroup',
        'orGroup'      => 'orWhereGroup',
        'conditions'   => 'whereConditions',
        'orConditions' => 'orWhereConditions',
        'raw'          => 'whereRaw',
        'orRaw'        => 'orWhereRaw',
        'logic'        => 'whereLogic',
        'has'          => 'hasWhere',
        'get'          => 'getWhere',
        'clear'        => 'clearWhere',
    ];

    protected $disabledTests = [
        'testCanAddLogic',
    ];

    public function testCanAssignWhereAppliers()
    {
        $this->model->setWhereApplier(function (WhereClause $clause) {
            $clause->where('Column', 'bar');
        });

        $query = $this->model->where('Column', 'foo');

        $this->assertEquals('WHERE (`Column` = ?) AND (`Column` = ?)', $query->toString());
    }

    public function testCanDetermineIfAppliersExists()
    {
        $this->assertFalse($this->model->hasWhereAppliers());

        $this->model->setWhereApplier(function () {
        });

        $this->assertTrue($this->model->hasWhereAppliers());
    }

    public function testCanGetAppliers()
    {
        $this->model->setWhereApplier(function () {
        });

        $this->assertCount(1, $this->model->getWhereAppliers());
    }

    public function testCanClearAppliers()
    {
        $this->model->setWhereApplier(function () {
        });

        $this->model->clearWhereAppliers();

        $this->assertFalse($this->model->hasWhereAppliers());
    }

    public function testCanDetermineIfWhereExists()
    {
        $this->assertFalse($this->model->hasWhere());

        $this->assertFalse($this->model->select('Column')->hasWhere());
    }

    public function testCanGetWhere()
    {
        $this->assertCount(0, $this->model->getWhere());
    }

    public function testCanClearWhere()
    {
        $this->model->clearWhere();

        $this->assertFalse($this->model->hasWhere());
    }

    public function testCanSetExists()
    {
        $query = $this->model->whereExists($this->driver->select());

        $this->assertEquals($this->prefix() . 'EXISTS (SELECT *)', $query->whereToString());
    }

    public function testCanSetNotExists()
    {
        $query = $this->model->whereNotExists($this->driver->select());

        $this->assertEquals($this->prefix() . 'NOT EXISTS (SELECT *)', $query->whereToString());
    }

    public function testCanSetExistsRaw()
    {
        $query = $this->model->whereExistsRaw('SELECT 1');

        $this->assertEquals($this->prefix() . 'EXISTS (SELECT 1)', $query->whereToString());
    }

    public function testCanSetNotExistsRaw()
    {
        $query = $this->model->whereNotExistsRaw('SELECT 1');

        $this->assertEquals($this->prefix() . 'NOT EXISTS (SELECT 1)', $query->whereToString());
    }

    public function testCanWhereBeNull()
    {
        $this->assertEmpty($this->model->whereToString());
    }

    public function testCanDetermineIfExistsExists()
    {
        $this->assertFalse($this->model->hasExists());

        $query = $this->model->whereExists($this->driver->select());

        $this->assertTrue($query->hasExists());
    }

    public function testCanGetExists()
    {
        $query = $this->model->whereExists($this->driver->select());

        $this->assertNotEmpty($query->getExists());
    }

    public function testCanClearExists()
    {
        $this->model->clearExists();

        $this->assertNull($this->model->getExists());

        $query = $this->model->whereExists($this->driver->select());

        $query->clearExists();

        $this->assertNull($this->model->getExists());
    }

    public function testCanSelectWhere()
    {
        $query = $this->model->select('Column')->where('Column', 'foo');

        $this->assertEquals('SELECT `Column` FROM `Table` WHERE `Column` = ?', $query->toString());
    }

    protected function getMethods(): array
    {
        return $this->methods;
    }

    protected function getPrefix(): string
    {
        return $this->prefix;
    }

    protected function getDisabledTests(): array
    {
        return $this->disabledTests;
    }

    protected function newClause()
    {
        return $this->model->setClause('WHERE', $this->model->driver()->where());
    }

    protected function newConditions()
    {
        return new Conditions($this->driver->dialect());
    }
}
