<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;
use PHPUnit\Framework\TestCase;

class WhereClauseTest extends TestCase
{
    public function testCanSetColumn()
    {
        $query = $this->newQuery();

        $query->where('Foo', 'foo');

        $this->assertEquals(['WHERE `Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    public function testCanTransformArrayColumnValueToScalar()
    {
        $query = $this->newQuery();

        $query->where(['Foo'], ['foo']);

        $this->assertEquals(['WHERE `Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanTransformColumnInToEquals()
    {
        $query = $this->newQuery();

        $query->where('Foo', 'IN', ['foo']);

        $this->assertEquals(['WHERE `Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanSetColumnWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->where('Foo', ['foo1', 'foo2']);

        $this->assertEquals(['WHERE `Foo` IN (?, ?)', ['foo1', 'foo2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenColumnOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->where('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetColumnRow()
    {
        $query = $this->newQuery();

        $query->where(['Foo', 'Bar'], ['foo', 'bar']);

        $this->assertEquals(['WHERE (`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanTransformColumnRowInToEquals()
    {
        $query = $this->newQuery();

        $query->where(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        $this->assertEquals(['WHERE (`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetColumnRowWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->where(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        $this->assertEquals(['WHERE (`Foo`, `Bar`) IN ((?, ?), (?, ?))', ['foo1', 'bar1', 'foo2', 'bar2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenColumnRowOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->where(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenColumnRowInValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->where(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenColumnRowValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->where(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    public function testCanWorkWithNullInRow()
    {
        $query = $this->newQuery();

        $query->where(['Foo', 'Bar'], ['foo', null]);

        $this->assertEquals(['WHERE (`Foo`, `Bar`) = (?, ?)', ['foo', '']], $query->toSql());
    }

    /**
     * @test
     *
     * @depends testCanSetColumn
     *
     * @param WhereClause $query
     */
    public function testCanSetOrColumn(WhereClause $query)
    {
        $query->orWhere('Bar', 'bar');

        $this->assertEquals(['WHERE `Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetColumns()
    {
        $query = $this->newQuery();

        $query->whereMultiple([
            'Foo' => 'foo',
        ]);

        $this->assertEquals(['WHERE `Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetColumns
     *
     * @param WhereClause $query
     */
    public function testCanSetOrColumns(WhereClause $query)
    {
        $query->orWhereMultiple([
            'Bar' => 'bar',
        ]);

        $this->assertEquals(['WHERE `Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRelation()
    {
        $query = $this->newQuery();

        $query->whereRelation('Foo', 'foo');

        $this->assertEquals('WHERE `Foo` = `foo`', $query->toString());

        return $query;
    }

    public function testCanTransformArrayRelationValueToScalar()
    {
        $query = $this->newQuery();

        $query->whereRelation(['Foo'], ['foo']);

        $this->assertEquals('WHERE `Foo` = `foo`', $query->toString());
    }

    public function testCanTransformRelationInToEquals()
    {
        $query = $this->newQuery();

        $query->whereRelation('Foo', 'IN', ['foo']);

        $this->assertEquals('WHERE `Foo` = `foo`', $query->toString());
    }

    public function testCanSetRelationWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->whereRelation('Foo', ['foo1', 'foo2']);

        $this->assertEquals('WHERE `Foo` IN (`foo1`, `foo2`)', $query->toString());
    }

    public function testCanThrowExceptionWhenRelationOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->whereRelation('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetRelationRow()
    {
        $query = $this->newQuery();

        $query->whereRelation(['Foo', 'Bar'], ['foo', 'bar']);

        $this->assertEquals('WHERE (`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
    }

    public function testCanTransformRelationRowInToEquals()
    {
        $query = $this->newQuery();

        $query->whereRelation(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        $this->assertEquals('WHERE (`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
    }

    public function testCanSetRelationRowWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->whereRelation(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        $this->assertEquals('WHERE (`Foo`, `Bar`) IN ((`foo1`, `bar1`), (`foo2`, `bar2`))', $query->toString());
    }

    public function testCanThrowExceptionWhenRelationRowOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->whereRelation(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenRelationRowInValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->whereRelation(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenRelationRowValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->whereRelation(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    /**
     * @test
     *
     * @depends testCanSetRelation
     *
     * @param WhereClause $query
     */
    public function testCanSetOrRelation(WhereClause $query)
    {
        $query->orWhereRelation('Bar', 'bar');

        $this->assertEquals('WHERE `Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetRelations()
    {
        $query = $this->newQuery();

        $query->whereRelations([
            'Foo' => 'foo',
        ]);

        $this->assertEquals('WHERE `Foo` = `foo`', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRelations
     *
     * @param WhereClause $query
     */
    public function testCanSetOrRelations(WhereClause $query)
    {
        $query->orWhereRelations([
            'Bar' => 'bar',
        ]);

        $this->assertEquals('WHERE `Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetIs()
    {
        $query = $this->newQuery();

        $query->whereIs('Foo');

        $this->assertEquals('WHERE `Foo` = 1', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIs
     *
     * @param WhereClause $query
     */
    public function testCanSetOrIs(WhereClause $query)
    {
        $query->orWhereIs('Bar');

        $this->assertEquals('WHERE `Foo` = 1 OR `Bar` = 1', $query->toString());
    }

    public function testCanSetIsNot()
    {
        $query = $this->newQuery();

        $query->whereIsNot('Foo');

        $this->assertEquals('WHERE `Foo` = 0', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNot
     *
     * @param WhereClause $query
     */
    public function testCanSetOrIsNot(WhereClause $query)
    {
        $query->orWhereIsNot('Bar');

        $this->assertEquals('WHERE `Foo` = 0 OR `Bar` = 0', $query->toString());
    }

    public function testCanSetIsNull()
    {
        $query = $this->newQuery();

        $query->whereIsNull('Foo');

        $this->assertEquals('WHERE `Foo` IS NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNull
     *
     * @param WhereClause $query
     */
    public function testCanSetOrIsNull(WhereClause $query)
    {
        $query->orWhereIsNull('Bar');

        $this->assertEquals('WHERE `Foo` IS NULL OR `Bar` IS NULL', $query->toString());
    }

    public function testCanSetIsNotNull()
    {
        $query = $this->newQuery();

        $query->whereIsNotNull('Foo');

        $this->assertEquals('WHERE `Foo` IS NOT NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNotNull
     *
     * @param WhereClause $query
     */
    public function testCanSetOrIsNotNull(WhereClause $query)
    {
        $query->orWhereIsNotNull('Bar');

        $this->assertEquals('WHERE `Foo` IS NOT NULL OR `Bar` IS NOT NULL', $query->toString());
    }

    public function testCanSetBetween()
    {
        $query = $this->newQuery();

        $query->whereBetween('Foo', 1, 10);

        $this->assertEquals(['WHERE `Foo` BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetBetween
     *
     * @param WhereClause $query
     */
    public function testCanSetOrBetween(WhereClause $query)
    {
        $query->orWhereBetween('Bar', 1, 10);

        $this->assertEquals(['WHERE `Foo` BETWEEN ? AND ? OR `Bar` BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetNotBetween()
    {
        $query = $this->newQuery();

        $query->whereNotBetween('Foo', 1, 10);

        $this->assertEquals(['WHERE `Foo` NOT BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetNotBetween
     *
     * @param WhereClause $query
     */
    public function testCanSetOrNotBetween(WhereClause $query)
    {
        $query->orWhereNotBetween('Bar', 1, 10);

        $this->assertEquals(['WHERE `Foo` NOT BETWEEN ? AND ? OR `Bar` NOT BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetDate()
    {
        $query = $this->newQuery();

        $query->whereDate('Foo', '1990-07-15');

        $this->assertEquals(['WHERE DATE(`Foo`) = ?', ['1990-07-15']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDate
     *
     * @param WhereClause $query
     */
    public function testCanSetOrDate(WhereClause $query)
    {
        $query->orWhereDate('Bar', '1990-07-15');

        $this->assertEquals(['WHERE DATE(`Foo`) = ? OR DATE(`Bar`) = ?', ['1990-07-15', '1990-07-15']], $query->toSql());
    }

    public function testCanSetMultipleDates()
    {
        $query = $this->newQuery();

        $query->whereDate('Foo', [$date = date('Y-m-d'), $date]);

        $this->assertEquals(['WHERE DATE(`Foo`) IN (?, ?)', [$date, $date]], $query->toSql());
    }

    public function testCanSetDateRow()
    {
        $query = $this->newQuery();

        $query->whereDate(['Foo', 'Bar'], [date('Y-m-d'), date('Y-m-d')]);

        $this->assertEquals(['WHERE (DATE(`Foo`), DATE(`Bar`)) = (?, ?)', [date('Y-m-d'), date('Y-m-d')]], $query->toSql());
    }

    public function testCanSetTime()
    {
        $query = $this->newQuery();

        $query->whereTime('Foo', '19:00:00');

        $this->assertEquals(['WHERE TIME(`Foo`) = ?', ['19:00:00']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetTime
     *
     * @param WhereClause $query
     */
    public function testCanSetOrTime(WhereClause $query)
    {
        $query->orWhereTime('Bar', '19:00:00');

        $this->assertEquals(['WHERE TIME(`Foo`) = ? OR TIME(`Bar`) = ?', ['19:00:00', '19:00:00']], $query->toSql());
    }

    public function testCanSetYear()
    {
        $query = $this->newQuery();

        $query->whereYear('Foo', 2016);

        $this->assertEquals(['WHERE YEAR(`Foo`) = ?', [2016]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetYear
     *
     * @param WhereClause $query
     */
    public function testCanSetOrYear(WhereClause $query)
    {
        $query->orWhereYear('Bar', 2016);

        $this->assertEquals(['WHERE YEAR(`Foo`) = ? OR YEAR(`Bar`) = ?', [2016, 2016]], $query->toSql());
    }

    public function testCanSetMonth()
    {
        $query = $this->newQuery();

        $query->whereMonth('Foo', '01');

        $this->assertEquals(['WHERE MONTH(`Foo`) = ?', [1]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetMonth
     *
     * @param WhereClause $query
     */
    public function testCanSetOrMonth(WhereClause $query)
    {
        $query->orWhereMonth('Bar', '01');

        $this->assertEquals(['WHERE MONTH(`Foo`) = ? OR MONTH(`Bar`) = ?', [1, 1]], $query->toSql());
    }

    public function testCanSetDay()
    {
        $query = $this->newQuery();

        $query->whereDay('Foo', '01');

        $this->assertEquals(['WHERE DAY(`Foo`) = ?', [1]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDay
     *
     * @param WhereClause $query
     */
    public function testCanSetOrDay(WhereClause $query)
    {
        $query->orWhereDay('Bar', '01');

        $this->assertEquals(['WHERE DAY(`Foo`) = ? OR DAY(`Bar`) = ?', [1, 1]], $query->toSql());
    }

    public function testCanSetConditionsCallable()
    {
        $query = $this->newQuery();

        $query->whereConditions(function (Conditions $query) {
            $query->column('Foo', 'foo');
        });

        $this->assertEquals(['WHERE (`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetConditionsCallable
     *
     * @param WhereClause $query
     */
    public function testCanSetOrConditionsCallable(WhereClause $query)
    {
        $query->orWhereConditions(function (Conditions $query) {
            $query->column('Bar', 'bar');
        });

        $this->assertEquals(['WHERE (`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetConditions()
    {
        $query = $this->newQuery();

        $query->whereConditions($this->newQuery()->where('Foo', 'foo'));

        $this->assertEquals(['WHERE (`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetConditions
     *
     * @param WhereClause $query
     */
    public function testCanSetOrConditions(WhereClause $query)
    {
        $query->orWhereConditions($this->newQuery()->where('Bar', 'bar'));

        $this->assertEquals(['WHERE (`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRaw()
    {
        $query = $this->newQuery();

        $query->whereRaw('`Foo` = ?', 'foo');

        $this->assertEquals(['WHERE (`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRaw
     *
     * @param WhereClause $query
     */
    public function testCanSetOrRaw(WhereClause $query)
    {
        $query->orWhereRaw('`Bar` = ?', 'bar');

        $this->assertEquals(['WHERE (`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanDetermineIfConditionsExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasWhere());

        $query->whereRaw('`Foo` = ?', 'foo');

        $this->assertTrue($query->hasWhere());
    }

    public function testCanGet()
    {
        $query = $this->newQuery();

        $query->whereRaw('`Foo` = ?', 'foo');

        $this->assertCount(1, $query->getWhere());
    }

    public function testCanClear()
    {
        $query = $this->newQuery();

        $query->whereRaw('`Foo` = ?', 'foo');

        $query->clearWhere();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newQuery();

        $query->where('Foo', 'foo');

        $this->assertEquals('WHERE `Foo` = ?', (string) $query);
    }

    public function testCanSetExists()
    {
        $query = $this->newQuery()->whereExists(new SelectQuery());

        $this->assertEquals(['WHERE EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetNotExists()
    {
        $query = $this->newQuery()->whereNotExists(new SelectQuery());

        $this->assertEquals(['WHERE NOT EXISTS (SELECT *)', []], $query->toSql());
    }

    public function testCanSetExistsRaw()
    {
        $query = $this->newQuery()->whereExistsRaw('SELECT 1');

        $this->assertEquals(['WHERE EXISTS (SELECT 1)', []], $query->toSql());
    }

    public function testCanSetNotExistsRaw()
    {
        $query = $this->newQuery()->whereNotExistsRaw('SELECT 1');

        $this->assertEquals(['WHERE NOT EXISTS (SELECT 1)', []], $query->toSql());
    }

    public function testCanDetermineIfExistsExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasExists());

        $query->whereExists(new SelectQuery());

        $this->assertTrue($query->hasExists());
    }

    public function testCanGetExists()
    {
        $query = $this->newQuery();

        $query->whereExists(new SelectQuery());

        $this->assertNotEmpty($query->getExists());
    }

    public function testCanClearExists()
    {
        $query = $this->newQuery();

        $query->whereExists(new SelectQuery());

        $query->clearExists();

        $this->assertNull($query->getExists());
    }

    public function testCanAddLogic()
    {
        $query = $this->newQuery();

        $query->addWhere('and', '`Foo` = ?', ['foo']);

        $this->assertEquals('WHERE `Foo` = ?', (string) $query);
    }

    public function testCanClone()
    {
        $query = $this->newQuery()->where('Foo', 'foo');

        $clone = clone $query;

        $clone->where('Bar', 'bar');

        $this->assertEquals(['WHERE `Foo` = ?', ['foo']], $query->toSql());

        $this->assertEquals(['WHERE `Foo` = ? AND `Bar` = ?', ['foo', 'bar']], $clone->toSql());
    }

    protected function newQuery(): WhereClause
    {
        return new WhereClause();
    }
}
