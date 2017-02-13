<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Driver\Mysql\Clause\MysqlWhereClause;
use Greg\Orm\Driver\Mysql\Query\MysqlSelectQuery;

class MysqlWhereClauseTest extends MysqlConditionsTest
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
        'condition'    => 'whereCondition',
        'orCondition'  => 'orWhereCondition',
        'raw'          => 'whereRaw',
        'orRaw'        => 'orWhereRaw',
        'logic'        => 'whereLogic',
        'has'          => 'hasWhere',
        'get'          => 'getWhere',
        'clear'        => 'clearWhere',
    ];

    public function testCanSetExists()
    {
        $query = $this->newCondition()->whereExists(new MysqlSelectQuery());

        $this->assertEquals(['WHERE EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetNotExists()
    {
        $query = $this->newCondition()->whereNotExists(new MysqlSelectQuery());

        $this->assertEquals(['WHERE NOT EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetExistsRaw()
    {
        $query = $this->newCondition()->whereExistsRaw('SELECT 1');

        $this->assertEquals(['WHERE EXISTS (SELECT 1)', []], $query->toSql());
    }

    public function testCanSetNotExistsRaw()
    {
        $query = $this->newCondition()->whereNotExistsRaw('SELECT 1');

        $this->assertEquals(['WHERE NOT EXISTS (SELECT 1)', []], $query->toSql());
    }

    protected function newCondition()
    {
        return new MysqlWhereClause();
    }
}
