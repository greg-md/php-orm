<?php

namespace Greg\Orm\Query;

use Greg\Orm\SqlException;
use PHPUnit\Framework\TestCase;

class InsertQueryTest extends TestCase
{
    public function testCanSetInto()
    {
        $query = $this->newQuery()->into('Table')->data(['Column' => 'foo']);

        $this->assertEquals(['INSERT INTO `Table` (`Column`) VALUES (?)', ['foo']], $query->toSql());
    }

    public function testCanClearData()
    {
        $query = $this->newQuery();

        $query->values(['Column' => 'foo'])->data([
            'Column' => 'foo',
        ]);

        $query->clearData();

        $this->assertFalse($query->hasColumns());

        $this->assertFalse($query->hasValues());
    }

    public function testCanDetermineIfIntoExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasInto());

        $query->into('Table');

        $this->assertTrue($query->hasInto());
    }

    public function testCanGetInto()
    {
        $query = $this->newQuery();

        $query->into('Table');

        $this->assertEquals('`Table`', $query->getInto());
    }

    public function testCanClearInto()
    {
        $query = $this->newQuery();

        $query->into('Table');

        $query->clearInto();

        $this->assertFalse($query->hasInto());
    }

    public function testCanSetColumnsValues()
    {
        $query = $this->newQuery()->into('Table')->columns([
            'Column',
        ])->values([
            'Column' => 'foo',
        ]);

        $this->assertEquals(['INSERT INTO `Table` (`Column`) VALUES (?)', ['foo']], $query->toSql());
    }

    public function testCanDetermineIfColumnsExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasColumns());

        $query->columns(['Column']);

        $this->assertTrue($query->hasColumns());
    }

    public function testCanGetColumns()
    {
        $query = $this->newQuery();

        $query->columns(['Column']);

        $this->assertCount(1, $query->getColumns());
    }

    public function testCanClearColumns()
    {
        $query = $this->newQuery();

        $query->columns(['Column']);

        $query->clearColumns();

        $this->assertFalse($query->hasColumns());
    }

    public function testCanDetermineIfValuesExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasValues());

        $query->values(['Column' => 'foo']);

        $this->assertTrue($query->hasValues());
    }

    public function testCanGetValues()
    {
        $query = $this->newQuery();

        $query->values(['Column' => 'foo']);

        $this->assertCount(1, $query->getValues());
    }

    public function testCanClearValues()
    {
        $query = $this->newQuery();

        $query->values(['Column' => 'foo']);

        $query->clearValues();

        $this->assertFalse($query->hasValues());
    }

    public function testCanSelect()
    {
        $query = $this->newQuery()->into('Table')->columns(['Column']);

        $query->select((new SelectQuery())->column('Column'));

        $this->assertEquals(['INSERT INTO `Table` (`Column`) SELECT `Column`', []], $query->toSql());
    }

    public function testCanSelectRaw()
    {
        $query = $this->newQuery()->into('Table')->columns(['Column']);

        $query->selectRaw((string) (new SelectQuery())->column('Column'));

        $this->assertEquals(['INSERT INTO `Table` (`Column`) SELECT `Column`', []], $query->toSql());
    }

    public function testCanDetermineIfSelectExists()
    {
        $query = $this->newQuery()->into('Table')->columns(['Column']);

        $this->assertFalse($query->hasSelect());

        $query->select((new SelectQuery())->column('Column'));

        $this->assertTrue($query->hasSelect());
    }

    public function testCanGetSelect()
    {
        $query = $this->newQuery()->into('Table')->columns(['Column']);

        $query->select((new SelectQuery())->column('Column'));

        $this->assertNotEmpty($query->getSelect());
    }

    public function testCanClearSelect()
    {
        $query = $this->newQuery()->into('Table')->columns(['Column']);

        $query->select((new SelectQuery())->column('Column'));

        $query->clearSelect();

        $this->assertFalse($query->hasSelect());
    }

    public function testCanTransformToString()
    {
        $query = $this->newQuery()->into('Table')->data(['Column' => 'foo']);

        $this->assertEquals('INSERT INTO `Table` (`Column`) VALUES (?)', (string) $query);
    }

    public function testCanThrowExceptionIfIntoNotDefined()
    {
        $this->expectException(SqlException::class);

        $this->newQuery()->toSql();
    }

    public function testCanThrowExceptionIfColumnsNotDefined()
    {
        $this->expectException(SqlException::class);

        $this->newQuery()->into('Table')->toSql();
    }

    public function testCanThrowExceptionIfSelectColumnsNotMatch()
    {
        $this->expectException(SqlException::class);

        $this->newQuery()
            ->into('Table')
            ->columns(['Column'])
            ->select((new SelectQuery())->columns('Column1', 'Column2'))
            ->toSql();
    }

    public function testCanThrowExceptionIfColumnsNotMatch()
    {
        $this->expectException(SqlException::class);

        $this->newQuery()
            ->into('Table')
            ->columns(['Column'])
            ->values([1, 2])
            ->toSql();
    }

    public function testCanReturnExceptionStringWhenTransformToString()
    {
        $query = $this->newQuery();

        $this->assertEquals('Undefined INSERT table.', (string) $query);
    }

    protected function newQuery(): InsertQuery
    {
        return new InsertQuery();
    }
}
