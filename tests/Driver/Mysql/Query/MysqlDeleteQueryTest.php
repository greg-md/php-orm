<?php

namespace Greg\Orm\Tests\Driver\Mysql\Query;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Mysql\Query\MysqlDeleteQuery;
use Greg\Orm\QueryException;
use PHPUnit\Framework\TestCase;

class MysqlDeleteQueryTest extends TestCase
{
    public function testCanSetRowsFrom()
    {
        $query = $this->newQuery()
            ->rowsFrom('t1')
            ->from('Table1 as t1', 'Table2');

        $this->assertEquals('DELETE `t1` FROM `Table1` AS `t1`, `Table2`', $query->toString());
    }

    public function testCanDetermineIfExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasRowsFrom());

        $query->rowsFrom('Column');

        $this->assertTrue($query->hasRowsFrom());
    }

    public function testCanGetColumns()
    {
        $query = $this->newQuery();

        $query->rowsFrom('Column');

        $this->assertCount(1, $query->getRowsFrom());
    }

    public function testCanClearColumns()
    {
        $query = $this->newQuery();

        $query->rowsFrom('Column');

        $query->clearRowsFrom();

        $this->assertFalse($query->hasRowsFrom());
    }

    public function testCanCombineClauses()
    {
        $query = $this->newQuery()
            ->from('Table1')
            ->innerOn('Table2', function(ConditionsStrategy $strategy) {
                $strategy->isNull('Column');
            })
            ->where('Foo', 'foo')
            ->limit(1)
            ->orderBy('Foo');

        $sql = 'DELETE FROM `Table1` INNER JOIN `Table2` ON `Column` IS NULL WHERE `Foo` = ? ORDER BY `Foo` LIMIT 1';

        $this->assertEquals([$sql, ['foo']], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newQuery()
            ->rowsFrom('t1')
            ->from('Table1 as t1', 'Table2');

        $this->assertEquals('DELETE `t1` FROM `Table1` AS `t1`, `Table2`', (string) $query);
    }

    public function testCanThrowExceptionIfFromWasNotDefined()
    {
        $this->expectException(QueryException::class);

        $query = $this->newQuery()->where('Foo', 'foo');

        $query->toSql();
    }

    protected function newQuery()
    {
        return new MysqlDeleteQuery();
    }
}