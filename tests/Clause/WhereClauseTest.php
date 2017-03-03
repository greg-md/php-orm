<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Query\SelectQuery;
use Greg\Orm\ConditionsTestingAbstract;

class WhereClauseTest extends ConditionsTestingAbstract
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
        $query = $this->newClause();

        $query->whereStrategy($this->newClause()->where('Foo', 'foo'));

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
        $query->orWhereStrategy($this->newClause()->where('Bar', 'bar'));

        $this->assertEquals([$this->prefix() . '(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetExists()
    {
        $query = $this->newClause()->whereExists(new SelectQuery());

        $this->assertEquals([$this->prefix() . 'EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetNotExists()
    {
        $query = $this->newClause()->whereNotExists(new SelectQuery());

        $this->assertEquals([$this->prefix() . 'NOT EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetExistsRaw()
    {
        $query = $this->newClause()->whereExistsRaw('SELECT 1');

        $this->assertEquals([$this->prefix() . 'EXISTS (SELECT 1)', []], $query->toSql());
    }

    public function testCanSetNotExistsRaw()
    {
        $query = $this->newClause()->whereNotExistsRaw('SELECT 1');

        $this->assertEquals([$this->prefix() . 'NOT EXISTS (SELECT 1)', []], $query->toSql());
    }

    public function testCanDetermineIfExistsExists()
    {
        $query = $this->newClause();

        $this->assertFalse($query->hasExists());

        $query->whereExists(new SelectQuery());

        $this->assertTrue($query->hasExists());
    }

    public function testCanGetExists()
    {
        $query = $this->newClause();

        $query->whereExists(new SelectQuery());

        $this->assertNotEmpty($query->getExists());
    }

    public function testCanClearExists()
    {
        $query = $this->newClause();

        $query->whereExists(new SelectQuery());

        $query->clearExists();

        $this->assertNull($query->getExists());
    }

    public function testCanClone()
    {
        $query = $this->newClause()->where('Foo', 'foo');

        $clone = clone $query;

        $clone->where('Bar', 'bar');

        $this->assertEquals([$this->prefix() . '`Foo` = ?', ['foo']], $query->toSql());

        $this->assertEquals([$this->prefix() . '`Foo` = ? AND `Bar` = ?', ['foo', 'bar']], $clone->toSql());
    }

    protected function newClause()
    {
        return new WhereClause();
    }
}
