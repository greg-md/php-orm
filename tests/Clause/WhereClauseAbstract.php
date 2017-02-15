<?php

namespace Greg\Orm\Tests\Clause;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\ConditionsAbstract;

abstract class WhereClauseAbstract extends ConditionsAbstract
{
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

    public function testCanSetExists()
    {
        $query = $this->newWhereClause()->whereExists($this->newSelectQuery());

        $this->assertEquals(['WHERE EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetNotExists()
    {
        $query = $this->newWhereClause()->whereNotExists($this->newSelectQuery());

        $this->assertEquals(['WHERE NOT EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetExistsRaw()
    {
        $query = $this->newWhereClause()->whereExistsRaw('SELECT 1');

        $this->assertEquals(['WHERE EXISTS (SELECT 1)', []], $query->toSql());
    }

    public function testCanSetNotExistsRaw()
    {
        $query = $this->newWhereClause()->whereNotExistsRaw('SELECT 1');

        $this->assertEquals(['WHERE NOT EXISTS (SELECT 1)', []], $query->toSql());
    }

    /**
     * @return WhereClause|\Greg\Orm\ConditionsStrategy
     */
    protected function newWhereClause()
    {
        return $this->newClause();
    }

    abstract protected function newSelectQuery(): SelectQuery;
}
