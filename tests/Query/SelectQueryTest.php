<?php

namespace Greg\Orm\Query;

use Greg\Orm\Conditions;
use PHPUnit\Framework\TestCase;

class SelectQueryTest extends TestCase
{
    public function testCanSetDistinct()
    {
        $query = $this->newQuery()->distinct();

        $this->assertEquals(['SELECT DISTINCT *', []], $query->toSql());
    }

    public function testCanSetColumnsFrom()
    {
        $query = $this->newQuery()->columnsFrom('Table', 'Column');

        $this->assertEquals(['SELECT `Table`.`Column` FROM `Table`', []], $query->toSql());
    }

    public function testCanSetColumns()
    {
        $query = $this->newQuery()->columns('Column1', 'Column2')->from('Table');

        $this->assertEquals(['SELECT `Column1`, `Column2` FROM `Table`', []], $query->toSql());
    }

    public function testCanSetColumn()
    {
        $query = $this->newQuery()->column('Column', 'c')->from('Table');

        $this->assertEquals(['SELECT `Column` AS `c` FROM `Table`', []], $query->toSql());
    }

    public function testCanSetConcatColumn()
    {
        $query = $this->newQuery()->columnConcat(['Column1', 'Column2'], ':', 'c')->from('Table');

        $this->assertEquals(['SELECT `Column1` + ? + `Column2` AS `c` FROM `Table`', [':']], $query->toSql());
    }

    public function testCanSetColumnSelect()
    {
        $query = $this->newQuery()->columnSelect($this->newQuery()->column('Column'), 'c')->from('Table');

        $this->assertEquals(['SELECT (SELECT `Column`) AS `c` FROM `Table`', []], $query->toSql());
    }

    public function testCanSetColumnRaw()
    {
        $query = $this->newQuery()->columnRaw('`Column`');

        $this->assertEquals(['SELECT `Column`', []], $query->toSql());
    }

    public function testCanSetCount()
    {
        $query = $this->newQuery()->count('*', 'all');

        $this->assertEquals(['SELECT COUNT(*) AS `all`', []], $query->toSql());
    }

    public function testCanSetMax()
    {
        $query = $this->newQuery()->max('Column', 'all');

        $this->assertEquals(['SELECT MAX(`Column`) AS `all`', []], $query->toSql());
    }

    public function testCanSetMin()
    {
        $query = $this->newQuery()->min('Column', 'all');

        $this->assertEquals(['SELECT MIN(`Column`) AS `all`', []], $query->toSql());
    }

    public function testCanSetAvg()
    {
        $query = $this->newQuery()->avg('Column', 'all');

        $this->assertEquals(['SELECT AVG(`Column`) AS `all`', []], $query->toSql());
    }

    public function testCanSetSum()
    {
        $query = $this->newQuery()->sum('Column', 'all');

        $this->assertEquals(['SELECT SUM(`Column`) AS `all`', []], $query->toSql());
    }

    public function testCanDetermineIfColumnsExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasColumns());

        $query->column('Column');

        $this->assertTrue($query->hasColumns());
    }

    public function testCanGetColumns()
    {
        $query = $this->newQuery();

        $query->column('Column');

        $this->assertCount(1, $query->getColumns());
    }

    public function testCanClearColumns()
    {
        $query = $this->newQuery();

        $query->column('Column');

        $query->clearColumns();

        $this->assertFalse($query->hasColumns());
    }

    public function testCanSetUnion()
    {
        $query = $this->newQuery()->union($this->newQuery()->column('Column'));

        $this->assertEquals(['(SELECT *) UNION (SELECT `Column`)', []], $query->toSql());
    }

    public function testCanSetUnionAll()
    {
        $query = $this->newQuery()->unionAll($this->newQuery()->column('Column'));

        $this->assertEquals(['(SELECT *) UNION ALL (SELECT `Column`)', []], $query->toSql());
    }

    public function testCanSetUnionDistinct()
    {
        $query = $this->newQuery()->unionDistinct($this->newQuery()->column('Column'));

        $this->assertEquals(['(SELECT *) UNION DISTINCT (SELECT `Column`)', []], $query->toSql());
    }

    public function testCanSetUnionRaw()
    {
        $query = $this->newQuery()->unionRaw((string) $this->newQuery()->column('Column'));

        $this->assertEquals(['(SELECT *) UNION (SELECT `Column`)', []], $query->toSql());
    }

    public function testCanSetUnionAllRaw()
    {
        $query = $this->newQuery()->unionAllRaw((string) $this->newQuery()->column('Column'));

        $this->assertEquals(['(SELECT *) UNION ALL (SELECT `Column`)', []], $query->toSql());
    }

    public function testCanSetUnionDistinctRaw()
    {
        $query = $this->newQuery()->unionDistinctRaw((string) $this->newQuery()->column('Column'));

        $this->assertEquals(['(SELECT *) UNION DISTINCT (SELECT `Column`)', []], $query->toSql());
    }

    public function testCanDetermineIfUnionsExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasUnions());

        $query->union($this->newQuery());

        $this->assertTrue($query->hasUnions());
    }

    public function testCanGetUnions()
    {
        $query = $this->newQuery();

        $query->union($this->newQuery());

        $this->assertCount(1, $query->getUnions());
    }

    public function testCanClearUnions()
    {
        $query = $this->newQuery();

        $query->union($this->newQuery());

        $query->clearUnions();

        $this->assertFalse($query->hasUnions());
    }

    public function testCanSetLimits()
    {
        $query = $this->newQuery()->limit(10)->offset(10);

        $this->assertEquals(['SELECT * LIMIT 10 OFFSET 10', []], $query->toSql());
    }

    public function testCanLockForUpdate()
    {
        $query = $this->newQuery()->lockForUpdate();

        $this->assertEquals('SELECT *', $query->toString());
    }

    public function testCanLockInShareMode()
    {
        $query = $this->newQuery()->lockInShareMode();

        $this->assertEquals('SELECT *', $query->toString());
    }

    public function testCanClone()
    {
        $query = $this->newQuery();

        $clone = clone $query;

        $clone->column('Foo');

        $this->assertEquals('SELECT *', $query->toString());

        $this->assertEquals('SELECT `Foo`', $clone->toString());
    }

    public function testCanDetermineIfLockExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasLock());

        $query->lockForUpdate();

        $this->assertTrue($query->hasLock());
    }

    public function testCanGetLock()
    {
        $query = $this->newQuery();

        $query->lockForUpdate();

        $this->assertNotEmpty($query->getLock());
    }

    public function testCanClearLock()
    {
        $query = $this->newQuery();

        $query->lockForUpdate();

        $query->clearLock();

        $this->assertFalse($query->hasLock());
    }

    public function testCanCombineClauses()
    {
        $query = $this->newQuery()
            ->from('Table1')
            ->innerOn('Table2', function (Conditions $conditions) {
                $conditions->isNull('Column');
            })
            ->when(true, function (SelectQuery $query) {
                $query->where('Foo', 'foo');
            })
            ->when(false, function (SelectQuery $query) {
                $query->where('Bar', 'bar');
            })
            ->having('Bar', 'bar')
            ->groupBy('Foo')
            ->limit(1)
            ->orderBy('Foo');

        $sql = 'SELECT * FROM `Table1` INNER JOIN `Table2` ON `Column` IS NULL WHERE `Foo` = ?'
                . ' GROUP BY `Foo` HAVING `Bar` = ? ORDER BY `Foo` LIMIT 1';

        $this->assertEquals([$sql, ['foo', 'bar']], $query->toSql());
    }

    protected function newQuery(): SelectQuery
    {
        return new SelectQuery();
    }
}
