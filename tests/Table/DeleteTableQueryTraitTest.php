<?php

namespace Greg\Orm\Table;

use Greg\Orm\Conditions;
use Greg\Orm\Model;
use Greg\Orm\SqlException;

trait DeleteTableQueryTraitTest
{
    public function testCanSetRowsFrom()
    {
        $query = $this->model()
            ->rowsFrom('t2')
            ->rowsFrom('t3')
            ->from('Table2 as t2', 'Table3 as t3');

        $this->assertEquals('DELETE `t2`, `t3` FROM `Table`, `Table2` AS `t2`, `Table3` AS `t3`', $query->toString());
    }

    public function testCanDetermineIfRowsFromExists()
    {
        $this->assertFalse($this->model()->hasRowsFrom());

        $this->model()->setQuery($this->model()->newDeleteQuery());

        $this->assertFalse($this->model()->hasRowsFrom());

        $this->model()->rowsFrom('Table');

        $this->assertTrue($this->model()->hasRowsFrom());
    }

    public function testCanGetRowsFrom()
    {
        $this->assertCount(0, $this->model()->getRowsFrom());

        $this->model()->setQuery($this->model()->newDeleteQuery());

        $this->model()->rowsFrom('Column');

        $this->assertCount(1, $this->model()->getRowsFrom());
    }

    public function testCanClearRowsFrom()
    {
        $this->model()->setQuery($this->model()->newDeleteQuery());

        $this->model()->rowsFrom('Column');

        $this->model()->clearRowsFrom();

        $this->assertFalse($this->model()->hasRowsFrom());
    }

    public function testCanCombineClausesOnDeleteQuery()
    {
        $query = $this->model()
            ->from('Table2')
            ->innerOn('Table3', function (Conditions $strategy) {
                $strategy->isNull('Column');
            })
            ->where('Foo', 'foo')
            ->limit(1)
            ->orderBy('Foo')
            ->rowsFrom('Table2');

        $sql = 'DELETE `Table2` FROM `Table`, `Table2` INNER JOIN `Table3` ON `Column` IS NULL WHERE `Foo` = ? ORDER BY `Foo` LIMIT 1';

        $this->assertEquals($sql, $query->toString());
    }

    public function testCanThrowExceptionIfDeleteQueryNotSupported()
    {
        $this->expectException(SqlException::class);

        $this->model()->select('Foo', 'foo')->rowsFrom('Table2');
    }

    abstract protected function model(): Model;
}
