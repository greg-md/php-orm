<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Model;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;

trait FromTableClauseTraitTest
{
    public function testCanAddFrom()
    {
        $query = $this->model()->from('Foo');

        $this->assertEquals('FROM `Foo`', $query->toString());
    }

    public function testCanAddFromWithAlias()
    {
        $query = $this->model()->from(['f' => 'Foo']);

        $this->assertEquals('FROM `Foo` AS `f`', $query->toString());
    }

    public function testCanAddFromRaw()
    {
        $query = $this->model()->fromRaw('f', '`Foo`');

        $this->assertEquals('FROM `Foo` AS `f`', $query->toString());
    }

    public function testCanDetermineIfExistsFromTables()
    {
        $this->assertFalse($this->model()->hasFrom());

        $query = $this->model()->from('Foo');

        $this->assertTrue($query->hasFrom());
    }

    public function testCanDetermineIfClauseExists()
    {
        $this->assertFalse($this->model()->hasFromClause());

        $query = $this->model()->from('Foo');

        $this->assertTrue($query->hasFromClause());
    }

    public function testCanGetFrom()
    {
        $query = $this->model()->from('Foo');

        $this->assertCount(1, $query->getFrom());
    }

    public function testCanClearFrom()
    {
        $query = $this->model()->from('Foo');

        $query->clearFrom();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanFromSelect()
    {
        $query = $this->model()->from(['t' => new SelectQuery()]);

        $this->assertEquals('FROM (SELECT *) AS `t`', $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->model()->from('Foo');

        $this->assertEquals('FROM `Foo`', (string) $query);
    }

    public function testCanThrowExceptionIfFromDerivedTableNotHaveAlias()
    {
        $this->expectException(SqlException::class);

        $this->model()->from(new SelectQuery());
    }

    public function testCanAssignFromAppliers()
    {
        $this->model()->setFromApplier(function (FromClause $clause) {
            $clause->from('Table2');
        });

        $query = $this->model()->from('Table1');

        $this->assertEquals('FROM `Table2`, `Table1`', $query->toString());
    }

    public function testCanDetermineIfFromAppliersExists()
    {
        $this->assertFalse($this->model()->hasFromAppliers());

        $this->model()->setFromApplier(function () {
        });

        $this->assertTrue($this->model()->hasFromAppliers());
    }

    public function testCanGetFromAppliers()
    {
        $this->model()->setFromApplier(function () {
        });

        $this->assertCount(1, $this->model()->getFromAppliers());
    }

    public function testCanClearFromAppliers()
    {
        $this->model()->setFromApplier(function () {
        });

        $this->model()->clearFromAppliers();

        $this->assertFalse($this->model()->hasFromAppliers());
    }

    public function testCanDetermineIfFromExists()
    {
        $this->assertFalse($this->model()->hasFrom());
    }

    public function testCanGetEmptyFrom()
    {
        $this->assertCount(0, $this->model()->getFrom());
    }

    public function testCanClearEmptyFrom()
    {
        $this->model()->clearFrom();

        $this->assertFalse($this->model()->hasFrom());
    }

    public function testCanGetStringFromClause()
    {
        $query = $this->model()->from('Table');

        $this->assertEquals('FROM `Table`', $query->fromToString());
    }

    public function testCanGetEmptyFromClause()
    {
        $this->assertEquals('', $this->model()->fromToString());
    }

    public function testCanCombineClausesOnFromClause()
    {
        $this->assertTrue($this->model()->select('Column')->from('Table2')->hasFrom());
    }

    public function testCanThrowExceptionIfFromNotExists()
    {
        $this->expectException(SqlException::class);

        $this->model()->updateTable('Column')->from('Table2');
    }

    public function testCanFromJoin()
    {
        $query = $this->model()
            ->innerTo('Foo', 'Bar')
            ->from('Foo');

        $this->assertEquals('FROM `Foo` INNER JOIN `Bar`', $query->fromToString($query->joinStrategy()));
    }

    abstract protected function model(): Model;
}
