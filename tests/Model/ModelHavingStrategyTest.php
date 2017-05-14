<?php

namespace Greg\Orm\Model;

use Greg\Orm\Clause\HavingClause;
use Greg\Orm\ConditionsTestingTrait;
use Greg\Orm\ModelTestingAbstract;
use Greg\Orm\SqlException;

class ModelHavingStrategyTest extends ModelTestingAbstract
{
    use ConditionsTestingTrait;

    protected $prefix = 'HAVING ';

    protected $methods = [
        'column'        => 'having',
        'orColumn'      => 'orHaving',
        'columns'       => 'havingMultiple',
        'orColumns'     => 'orHavingMultiple',
        'date'          => 'havingDate',
        'orDate'        => 'orHavingDate',
        'time'          => 'havingTime',
        'orTime'        => 'orHavingTime',
        'year'          => 'havingYear',
        'orYear'        => 'orHavingYear',
        'month'         => 'havingMonth',
        'orMonth'       => 'orHavingMonth',
        'day'           => 'havingDay',
        'orDay'         => 'orHavingDay',
        'relation'      => 'havingRelation',
        'orRelation'    => 'orHavingRelation',
        'relations'     => 'havingRelations',
        'orRelations'   => 'orHavingRelations',
        'is'            => 'havingIs',
        'orIs'          => 'orHavingIs',
        'isNot'         => 'havingIsNot',
        'orIsNot'       => 'orHavingIsNot',
        'isNull'        => 'havingIsNull',
        'orIsNull'      => 'orHavingIsNull',
        'isNotNull'     => 'havingIsNotNull',
        'orIsNotNull'   => 'orHavingIsNotNull',
        'between'       => 'havingBetween',
        'orBetween'     => 'orHavingBetween',
        'notBetween'    => 'havingNotBetween',
        'orNotBetween'  => 'orHavingNotBetween',
        'group'         => 'havingConditions',
        'orGroup'       => 'orHavingConditions',
        'conditions'    => 'havingConditions',
        'orConditions'  => 'orHavingConditions',
        'raw'           => 'havingRaw',
        'orRaw'         => 'orHavingRaw',
        'logic'         => 'havingLogic',
        'has'           => 'hasHaving',
        'get'           => 'getHaving',
        'clear'         => 'clearHaving',
    ];

    protected $disabledTests = [
        'testCanAddLogic',
    ];

    public function testCanAssignHavingAppliers()
    {
        $this->model->setHavingApplier(function (HavingClause $clause) {
            $clause->having('Column', 'bar');
        });

        $query = $this->model->having('Column', 'foo');

        $this->assertEquals('HAVING (`Column` = ?) AND (`Column` = ?)', $query->toString());
    }

    public function testCanDetermineIfAppliersExists()
    {
        $this->assertFalse($this->model->hasHavingAppliers());

        $this->model->setHavingApplier(function () {
        });

        $this->assertTrue($this->model->hasHavingAppliers());
    }

    public function testCanGetAppliers()
    {
        $this->model->setHavingApplier(function () {
        });

        $this->assertCount(1, $this->model->getHavingAppliers());
    }

    public function testCanClearAppliers()
    {
        $this->model->setHavingApplier(function () {
        });

        $this->model->clearHavingAppliers();

        $this->assertFalse($this->model->hasHavingAppliers());
    }

    public function testCanDetermineIfHavingExists()
    {
        $this->assertFalse($this->model->hasHaving());

        $this->assertFalse($this->model->select('Column')->hasHaving());
    }

    public function testCanDetermineIfHavingClauseExists()
    {
        $this->assertFalse($this->model->hasHavingClause());

        $this->assertFalse($this->model->select('Column')->hasHavingClause());
    }

    public function testCanGetHaving()
    {
        $this->assertCount(0, $this->model->getHaving());
    }

    public function testCanClearHaving()
    {
        $this->model->clearHaving();

        $this->assertFalse($this->model->hasHaving());
    }

    public function testCanGetClauseString()
    {
        $query = $this->model->having('Column', 'foo');

        $this->assertEquals('HAVING `Column` = ?', $query->havingToString());
    }

    public function testCanGetEmptyHaving()
    {
        $this->assertEquals('', $this->model->havingToString());
    }

    public function testCanSelectHaving()
    {
        $query = $this->model->select('Column')->having('Column', 'foo');

        $this->assertEquals('SELECT `Column` FROM `Table` HAVING `Column` = ?', $query->toString());
    }

    public function testCanThrowExceptionIfNotHavingStrategy()
    {
        $this->expectException(SqlException::class);

        $this->model->updateTable('Table2')->having('Column', 'foo');
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
        return $this->model->intoHavingStrategy();
    }
}
