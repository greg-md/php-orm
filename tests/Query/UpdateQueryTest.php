<?php

namespace Greg\Orm\Query;

use Greg\Orm\Conditions;
use Greg\Orm\SqlException;
use PHPUnit\Framework\TestCase;

class UpdateQueryTest extends TestCase
{
    public function testCanSetColumn()
    {
        $query = $this->newQuery()->table('Table')->set('Column', 'foo');

        $this->assertEquals('UPDATE `Table`', $query->updateToString());

        $this->assertEquals('SET `Column` = ?', $query->setToString());

        $this->assertEquals('UPDATE `Table` SET `Column` = ?', $query->toString());
    }

    public function testCanDetermineIfTablesExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasTables());

        $query->table('Table');

        $this->assertTrue($query->hasTables());
    }

    public function testCanGetTables()
    {
        $query = $this->newQuery();

        $query->table('Table');

        $this->assertCount(1, $query->getTables());
    }

    public function testCanClearTables()
    {
        $query = $this->newQuery();

        $query->table('Table');

        $query->clearTables();

        $this->assertFalse($query->hasTables());
    }

    public function testCanSetColumns()
    {
        $query = $this->newQuery()->table('Table')->setMultiple(['Column' => 'foo']);

        $this->assertEquals(['UPDATE `Table` SET `Column` = ?', ['foo']], $query->toSql());
    }

    public function testCanSetRawColumn()
    {
        $query = $this->newQuery()->table('Table')->setRaw('`Column` = NOW()');

        $this->assertEquals(['UPDATE `Table` SET `Column` = NOW()', []], $query->toSql());
    }

    public function testCanIncrement()
    {
        $query = $this->newQuery()->table('Table')->increment('Column');

        $this->assertEquals(['UPDATE `Table` SET `Column` = `Column` + ?', [1]], $query->toSql());
    }

    public function testCanDecrement()
    {
        $query = $this->newQuery()->table('Table')->decrement('Column');

        $this->assertEquals(['UPDATE `Table` SET `Column` = `Column` - ?', [1]], $query->toSql());
    }

    public function testCanDetermineIfSetExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasSet());

        $query->set('Column', 'foo');

        $this->assertTrue($query->hasSet());
    }

    public function testCanGetSet()
    {
        $query = $this->newQuery();

        $query->set('Column', 'foo');

        $this->assertCount(1, $query->getSet());
    }

    public function testCanClearSet()
    {
        $query = $this->newQuery();

        $query->set('Column', 'foo');

        $query->clearSet();

        $this->assertFalse($query->hasSet());
    }

    public function testCanThrowExceptionIfNoTable()
    {
        $this->expectException(SqlException::class);

        $this->newQuery()->toString();
    }

    public function testCanThrowExceptionIfSetColumns()
    {
        $this->expectException(SqlException::class);

        $this->newQuery()->table('Table')->toString();
    }

    public function testCanCombineClauses()
    {
        $query = $this->newQuery()
            ->table('Table1 as t')
            ->innerJoinOnTo('t', 'Table2', function (Conditions $strategy) {
                $strategy->isNull('Column');
            })
            ->innerJoin('Table3')
            ->limit(1)
            ->where('Column', 'bar')
            ->orderAsc('Column')
            ->set('Column', 'foo');

        $sql = 'UPDATE `Table1` AS `t` INNER JOIN `Table2` ON `Column` IS NULL INNER JOIN `Table3` SET `Column` = ?'
                . ' WHERE `Column` = ? ORDER BY `Column` ASC LIMIT 1';

        $this->assertEquals([$sql, ['foo', 'bar']], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newQuery()->table('Table')->set('Column', 'foo');

        $this->assertEquals('UPDATE `Table` SET `Column` = ?', (string) $query);
    }

    public function testCanThrowExceptionOnDerivedTable()
    {
        $this->expectException(SqlException::class);

        $this->newQuery()->table(new SelectQuery());
    }

    public function testCanClone()
    {
        $query = $this->newQuery()->table('Table')->set('Column', 'bar');

        $query2 = clone $query;

        $query2->where('Column', 'foo');

        $this->assertNotEquals($query->toString(), $query2->toString());
    }

    public function testCanReturnExceptionStringWhenTransformToString()
    {
        $query = $this->newQuery();

        $this->assertEquals('Undefined UPDATE table.', (string) $query);
    }

    protected function newQuery(): UpdateQuery
    {
        return new UpdateQuery();
    }
}
