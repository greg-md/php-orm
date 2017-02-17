<?php

namespace Greg\Orm\Tests\Clause;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Conditions;
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

    public function testCanSetStrategy()
    {
        $query = $this->newWhereClause();

        $query->whereStrategy($this->newWhereClause()->where('Foo', 'foo'));

        $this->assertEquals([$this->prefix() . '(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetStrategy
     *
     * @param WhereClause $query
     */
    public function testCanSetOrStrategy(WhereClause $query)
    {
        $query->orWhereStrategy($this->newWhereClause()->where('Bar', 'bar'));

        $this->assertEquals([$this->prefix() . '(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetExists()
    {
        $query = $this->newWhereClause()->whereExists($this->newSelectQuery());

        $this->assertEquals([$this->prefix() . 'EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetNotExists()
    {
        $query = $this->newWhereClause()->whereNotExists($this->newSelectQuery());

        $this->assertEquals([$this->prefix() . 'NOT EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetExistsRaw()
    {
        $query = $this->newWhereClause()->whereExistsRaw('SELECT 1');

        $this->assertEquals([$this->prefix() . 'EXISTS (SELECT 1)', []], $query->toSql());
    }

    public function testCanSetNotExistsRaw()
    {
        $query = $this->newWhereClause()->whereNotExistsRaw('SELECT 1');

        $this->assertEquals([$this->prefix() . 'NOT EXISTS (SELECT 1)', []], $query->toSql());
    }

    public function testCanDetermineIfExistsExists()
    {
        $query = $this->newWhereClause();

        $this->assertFalse($query->hasExists());

        $query->whereExists($this->newSelectQuery());

        $this->assertTrue($query->hasExists());
    }

    public function testCanGet()
    {
        $query = $this->newWhereClause();

        $query->whereExists($this->newSelectQuery());

        $this->assertNotEmpty($query->getExists());
    }

    public function testCanClear()
    {
        $query = $this->newWhereClause();

        $query->whereExists($this->newSelectQuery());

        $query->clearExists();

        $this->assertNull($query->getExists());
    }

    /**
     * @return WhereClause|Conditions
     */
    protected function newWhereClause()
    {
        return $this->newClause();
    }

    abstract protected function newSelectQuery(): SelectQuery;
}
