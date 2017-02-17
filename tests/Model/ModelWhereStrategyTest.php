<?php

namespace Greg\Orm\Tests\Model;

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
        $this->model->whereStrategy();

        return $this->model;
    }

    protected function newConditions()
    {
        return new Conditions($this->driver->dialect());
    }
}
