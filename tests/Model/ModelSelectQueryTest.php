<?php

namespace Greg\Orm\Model;

use Greg\Orm\ModelTestingAbstract;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;

class ModelSelectQueryTest extends ModelTestingAbstract
{
    public function testCanSetDistinct()
    {
        $query = $this->model->distinct();

        $this->assertEquals('SELECT DISTINCT * FROM `Table`', $query->toString());
    }

    public function testCanSetColumnsFrom()
    {
        $query = $this->model->selectFrom('Table2', 'Column');

        $this->assertEquals('SELECT `Table2`.`Column` FROM `Table`, `Table2`', $query->toString());
    }

    public function testCanSetColumns()
    {
        $query = $this->model->select('Column1', 'Column2')->from('Table2');

        $this->assertEquals('SELECT `Column1`, `Column2` FROM `Table`, `Table2`', $query->toString());
    }

    public function testCanSetOnlyColumns()
    {
        $query = $this->model->selectOnly('Column');

        $this->assertEquals('SELECT `Table`.`Column` FROM `Table`', $query->toString());
    }

    public function testCanSetColumn()
    {
        $query = $this->model->selectColumn('Column', 'c')->from('Table2');

        $this->assertEquals('SELECT `Column` AS `c` FROM `Table`, `Table2`', $query->toString());
    }

    public function testCanSetColumnConcat()
    {
        $query = $this->model->selectConcat(['Column1', 'Column2'], ':', 'c')->from('Table2');

        $this->assertEquals('SELECT `Column1` + ? + `Column2` AS `c` FROM `Table`, `Table2`', $query->toString());
    }

    public function testCanSetColumnSelect()
    {
        $query = $this->model->selectSelect($this->model->select('Column')->selectQuery(), 'c')->from('Table2');

        $this->assertEquals('SELECT (SELECT `Column` FROM `Table`) AS `c` FROM `Table`, `Table2`', $query->toString());
    }

    public function testCanSetColumnRaw()
    {
        $query = $this->model->selectRaw('`Column`');

        $this->assertEquals('SELECT `Column` FROM `Table`', $query->toString());
    }

    public function testCanSetCount()
    {
        $query = $this->model->selectCount('*', 'all');

        $this->assertEquals('SELECT COUNT(*) AS `all` FROM `Table`', $query->toString());
    }

    public function testCanSetMax()
    {
        $query = $this->model->selectMax('Column', 'all');

        $this->assertEquals('SELECT MAX(`Column`) AS `all` FROM `Table`', $query->toString());
    }

    public function testCanSetMin()
    {
        $query = $this->model->selectMin('Column', 'all');

        $this->assertEquals('SELECT MIN(`Column`) AS `all` FROM `Table`', $query->toString());
    }

    public function testCanSetAvg()
    {
        $query = $this->model->selectAvg('Column', 'all');

        $this->assertEquals('SELECT AVG(`Column`) AS `all` FROM `Table`', $query->toString());
    }

    public function testCanSetSum()
    {
        $query = $this->model->selectSum('Column', 'all');

        $this->assertEquals('SELECT SUM(`Column`) AS `all` FROM `Table`', $query->toString());
    }

    public function testCanDetermineIfColumnsExists()
    {
        $this->assertFalse($this->model->hasSelect());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->assertFalse($this->model->hasSelect());

        $this->model->select('Column');

        $this->assertTrue($this->model->hasSelect());
    }

    public function testCanGetColumns()
    {
        $this->assertCount(0, $this->model->getSelect());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->model->select('Column');

        $this->assertCount(1, $this->model->getSelect());
    }

    public function testCanClearColumns()
    {
        $this->assertFalse($this->model->hasSelect());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->model->select('Column');

        $this->model->clearSelect();

        $this->assertFalse($this->model->hasSelect());
    }

    public function testCanSetUnion()
    {
        $query = $this->model->union($this->model->select('Column')->selectQuery());

        $this->assertEquals('(SELECT * FROM `Table`) UNION (SELECT `Column` FROM `Table`)', $query->toString());
    }

    public function testCanSetUnionAll()
    {
        $query = $this->model->unionAll($this->model->select('Column')->selectQuery());

        $this->assertEquals('(SELECT * FROM `Table`) UNION ALL (SELECT `Column` FROM `Table`)', $query->toString());
    }

    public function testCanSetUnionDistinct()
    {
        $query = $this->model->unionDistinct($this->model->select('Column')->selectQuery());

        $this->assertEquals('(SELECT * FROM `Table`) UNION DISTINCT (SELECT `Column` FROM `Table`)', $query->toString());
    }

    public function testCanSetUnionRaw()
    {
        $query = $this->model->unionRaw((string) $this->model->select('Column')->selectQuery());

        $this->assertEquals('(SELECT * FROM `Table`) UNION (SELECT `Column` FROM `Table`)', $query->toString());
    }

    public function testCanSetUnionAllRaw()
    {
        $query = $this->model->unionAllRaw((string) $this->model->select('Column')->selectQuery());

        $this->assertEquals('(SELECT * FROM `Table`) UNION ALL (SELECT `Column` FROM `Table`)', $query->toString());
    }

    public function testCanSetUnionDistinctRaw()
    {
        $query = $this->model->unionDistinctRaw((string) $this->model->select('Column')->selectQuery());

        $this->assertEquals('(SELECT * FROM `Table`) UNION DISTINCT (SELECT `Column` FROM `Table`)', $query->toString());
    }

    public function testCanDetermineIfUnionsExists()
    {
        $this->assertFalse($this->model->hasUnions());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->assertFalse($this->model->hasUnions());

        $this->model->union($this->model->selectQuery());

        $this->assertTrue($this->model->hasUnions());
    }

    public function testCanGetUnions()
    {
        $this->assertCount(0, $this->model->getUnions());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->model->union($this->model->selectQuery());

        $this->assertCount(1, $this->model->getUnions());
    }

    public function testCanClearUnions()
    {
        $this->assertFalse($this->model->hasUnions());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->model->union($this->model->selectQuery());

        $this->model->clearUnions();

        $this->assertFalse($this->model->hasUnions());
    }

    public function testCanLockForUpdate()
    {
        $query = $this->model->lockForUpdate();

        $this->assertEquals('SELECT * FROM `Table`', $query->toString());
    }

    public function testCanLockInSharedMode()
    {
        $query = $this->model->lockInShareMode();

        $this->assertEquals('SELECT * FROM `Table`', $query->toString());
    }

    public function testCanDetermineIfLockExists()
    {
        $this->assertFalse($this->model->hasLock());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->assertFalse($this->model->hasLock());

        $this->model->lockForUpdate();

        $this->assertTrue($this->model->hasLock());
    }

    public function testCanGetLock()
    {
        $this->assertEmpty($this->model->getLock());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->model->lockForUpdate();

        $this->assertNotEmpty($this->model->getLock());
    }

    public function testCanClearLock()
    {
        $this->assertFalse($this->model->hasLock());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->model->lockForUpdate();

        $this->model->clearLock();

        $this->assertFalse($this->model->hasLock());
    }

    public function testCanThrowExceptionIfNotHavingStrategy()
    {
        $this->expectException(SqlException::class);

        $this->model->updateTable('Table2')->select('Column');
    }

    public function testCanCombineClauses()
    {
        $query = $this->model
            ->from('Table2')
            ->inner('Table3')
            ->where('Column', 'foo')
            ->groupBy('Column')
            ->having('Column', 'foo')
            ->orderAsc('Column')
            ->limit(10)
            ->offset(10)
            ->selectOnly('Column');

        $sql = 'SELECT `Table`.`Column` FROM `Table`, `Table2` INNER JOIN `Table3` WHERE `Column` = ? GROUP BY `Column` HAVING `Column` = ? ORDER BY `Column` ASC LIMIT 10 OFFSET 10';

        $this->assertEquals($sql, $query->toString());
    }

    public function testCanTransformIntoSelectQuery()
    {
        $this->model->intoSelectQuery();

        $this->assertInstanceOf(SelectQuery::class, $query = $this->model->getQuery());

        // Can get the same query
        $this->model->intoSelectQuery();

        $this->assertEquals($query, $this->model->getQuery());
    }
}
