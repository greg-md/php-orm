<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\ConditionsTestingAbstract;

class HavingClauseTest extends ConditionsTestingAbstract
{
    protected $prefix = 'HAVING ';

    protected $methods = [
        'column'       => 'having',
        'orColumn'     => 'orHaving',
        'columns'      => 'havingMultiple',
        'orColumns'    => 'orHavingMultiple',
        'date'         => 'havingDate',
        'orDate'       => 'orHavingDate',
        'time'         => 'havingTime',
        'orTime'       => 'orHavingTime',
        'year'         => 'havingYear',
        'orYear'       => 'orHavingYear',
        'month'        => 'havingMonth',
        'orMonth'      => 'orHavingMonth',
        'day'          => 'havingDay',
        'orDay'        => 'orHavingDay',
        'relation'     => 'havingRelation',
        'orRelation'   => 'orHavingRelation',
        'relations'    => 'havingRelations',
        'orRelations'  => 'orHavingRelations',
        'isNull'       => 'havingIsNull',
        'orIsNull'     => 'orHavingIsNull',
        'isNotNull'    => 'havingIsNotNull',
        'orIsNotNull'  => 'orHavingIsNotNull',
        'between'      => 'havingBetween',
        'orBetween'    => 'orHavingBetween',
        'notBetween'   => 'havingNotBetween',
        'orNotBetween' => 'orHavingNotBetween',
        'group'        => 'havingConditions',
        'orGroup'      => 'orHavingConditions',
        'conditions'   => 'havingConditions',
        'orConditions' => 'orHavingConditions',
        'raw'          => 'havingRaw',
        'orRaw'        => 'orHavingRaw',
        'logic'        => 'havingLogic',
        'has'          => 'hasHaving',
        'get'          => 'getHaving',
        'clear'        => 'clearHaving',
    ];

    public function testCanSetStrategy()
    {
        $query = $this->newClause();

        $query->havingConditions($this->newClause()->having('Foo', 'foo'));

        $this->assertEquals([$this->prefix() . '(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetStrategy
     *
     * @param HavingClause $query
     */
    public function testCanSetOrStrategy(HavingClause $query)
    {
        $query->orHavingConditions($this->newClause()->having('Bar', 'bar'));

        $this->assertEquals([$this->prefix() . '(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanClone()
    {
        $query = $this->newClause()->having('Foo', 'foo');

        $clone = clone $query;

        $clone->having('Bar', 'bar');

        $this->assertEquals([$this->prefix() . '`Foo` = ?', ['foo']], $query->toSql());

        $this->assertEquals([$this->prefix() . '`Foo` = ? AND `Bar` = ?', ['foo', 'bar']], $clone->toSql());
    }

    protected function newClause()
    {
        return new HavingClause();
    }

    protected function newConditions()
    {
        return new Conditions();
    }
}
