<?php

namespace Greg\Orm\Tests\Driver\Mysql\Query;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Mysql\Query\MysqlSelectQuery;
use Greg\Orm\Driver\Mysql\Query\MysqlUpdateQuery;
use Greg\Orm\QueryException;
use PHPUnit\Framework\TestCase;

class MysqlUpdateQueryTest extends TestCase
{
    public function testCanSetColumn()
    {
        $query = $this->newQuery()->table('Table')->set('Column', 'foo');

        $this->assertEquals(['UPDATE `Table` SET `Column` = ?', ['foo']], $query->toSql());
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
        $this->expectException(QueryException::class);

        $this->newQuery()->toString();
    }

    public function testCanThrowExceptionIfSetColumns()
    {
        $this->expectException(QueryException::class);

        $this->newQuery()->table('Table')->toString();
    }

    public function testCanCombineClauses()
    {
        $query = $this->newQuery()
            ->table('Table1 as t')
            ->innerToOn('t', 'Table2', function (ConditionsStrategy $strategy) {
                $strategy->isNull('Column');
            })
            ->inner('Table3')
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
        $this->expectException(QueryException::class);

        $this->newQuery()->table(new MysqlSelectQuery());
    }

    protected function newQuery()
    {
        return new MysqlUpdateQuery();
    }
}
