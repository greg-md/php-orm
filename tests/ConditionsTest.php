<?php

namespace Greg\Orm;

use PHPUnit\Framework\TestCase;

class ConditionsTest extends TestCase
{
    public function testCanSetColumn()
    {
        $query = $this->newQuery();

        $query->column('Foo', 'foo');

        $this->assertEquals(['`Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    public function testCanTransformArrayColumnValueToScalar()
    {
        $query = $this->newQuery();

        $query->column(['Foo'], ['foo']);

        $this->assertEquals(['`Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanTransformColumnInToEquals()
    {
        $query = $this->newQuery();

        $query->column('Foo', 'IN', ['foo']);

        $this->assertEquals(['`Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanSetColumnWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->column('Foo', ['foo1', 'foo2']);

        $this->assertEquals(['`Foo` IN (?, ?)', ['foo1', 'foo2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenColumnOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->column('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetColumnRow()
    {
        $query = $this->newQuery();

        $query->column(['Foo', 'Bar'], ['foo', 'bar']);

        $this->assertEquals(['(`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanTransformColumnRowInToEquals()
    {
        $query = $this->newQuery();

        $query->column(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        $this->assertEquals(['(`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetColumnRowWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->column(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        $this->assertEquals(['(`Foo`, `Bar`) IN ((?, ?), (?, ?))', ['foo1', 'bar1', 'foo2', 'bar2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenColumnRowOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->column(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenColumnRowInValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->column(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenColumnRowValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->column(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    public function testCanWorkWithNullInRow()
    {
        $query = $this->newQuery();

        $query->column(['Foo', 'Bar'], ['foo', null]);

        $this->assertEquals(['(`Foo`, `Bar`) = (?, ?)', ['foo', '']], $query->toSql());
    }

    /**
     * @test
     *
     * @depends testCanSetColumn
     *
     * @param Conditions $query
     */
    public function testCanSetOrColumn(Conditions $query)
    {
        $query->orColumn('Bar', 'bar');

        $this->assertEquals(['`Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetColumns()
    {
        $query = $this->newQuery();

        $query->columns([
            'Foo' => 'foo',
        ]);

        $this->assertEquals(['`Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetColumns
     *
     * @param Conditions $query
     */
    public function testCanSetOrColumns(Conditions $query)
    {
        $query->orColumns([
            'Bar' => 'bar',
        ]);

        $this->assertEquals(['`Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRelation()
    {
        $query = $this->newQuery();

        $query->relation('Foo', 'foo');

        $this->assertEquals('`Foo` = `foo`', $query->toString());

        return $query;
    }

    public function testCanTransformArrayRelationValueToScalar()
    {
        $query = $this->newQuery();

        $query->relation(['Foo'], ['foo']);

        $this->assertEquals('`Foo` = `foo`', $query->toString());
    }

    public function testCanTransformRelationInToEquals()
    {
        $query = $this->newQuery();

        $query->relation('Foo', 'IN', ['foo']);

        $this->assertEquals('`Foo` = `foo`', $query->toString());
    }

    public function testCanSetRelationWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->relation('Foo', ['foo1', 'foo2']);

        $this->assertEquals('`Foo` IN (`foo1`, `foo2`)', $query->toString());
    }

    public function testCanThrowExceptionWhenRelationOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->relation('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetRelationRow()
    {
        $query = $this->newQuery();

        $query->relation(['Foo', 'Bar'], ['foo', 'bar']);

        $this->assertEquals('(`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
    }

    public function testCanTransformRelationRowInToEquals()
    {
        $query = $this->newQuery();

        $query->relation(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        $this->assertEquals('(`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
    }

    public function testCanSetRelationRowWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->relation(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        $this->assertEquals('(`Foo`, `Bar`) IN ((`foo1`, `bar1`), (`foo2`, `bar2`))', $query->toString());
    }

    public function testCanThrowExceptionWhenRelationRowOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->relation(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenRelationRowInValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->relation(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenRelationRowValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->relation(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    /**
     * @test
     *
     * @depends testCanSetRelation
     *
     * @param Conditions $query
     */
    public function testCanSetOrRelation(Conditions $query)
    {
        $query->orRelation('Bar', 'bar');

        $this->assertEquals('`Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetRelations()
    {
        $query = $this->newQuery();

        $query->relations([
            'Foo' => 'foo',
        ]);

        $this->assertEquals('`Foo` = `foo`', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRelations
     *
     * @param Conditions $query
     */
    public function testCanSetOrRelations(Conditions $query)
    {
        $query->orRelations([
            'Bar' => 'bar',
        ]);

        $this->assertEquals('`Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetIs()
    {
        $query = $this->newQuery();

        $query->is('Foo');

        $this->assertEquals('`Foo` = 1', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIs
     *
     * @param Conditions $query
     */
    public function testCanSetOrIs(Conditions $query)
    {
        $query->orIs('Bar');

        $this->assertEquals('`Foo` = 1 OR `Bar` = 1', $query->toString());
    }

    public function testCanSetIsNot()
    {
        $query = $this->newQuery();

        $query->isNot('Foo');

        $this->assertEquals('`Foo` = 0', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNot
     *
     * @param Conditions $query
     */
    public function testCanSetOrIsNot(Conditions $query)
    {
        $query->orIsNot('Bar');

        $this->assertEquals('`Foo` = 0 OR `Bar` = 0', $query->toString());
    }

    public function testCanSetIsNull()
    {
        $query = $this->newQuery();

        $query->isNull('Foo');

        $this->assertEquals('`Foo` IS NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNull
     *
     * @param Conditions $query
     */
    public function testCanSetOrIsNull(Conditions $query)
    {
        $query->orIsNull('Bar');

        $this->assertEquals('`Foo` IS NULL OR `Bar` IS NULL', $query->toString());
    }

    public function testCanSetIsNotNull()
    {
        $query = $this->newQuery();

        $query->isNotNull('Foo');

        $this->assertEquals('`Foo` IS NOT NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNotNull
     *
     * @param Conditions $query
     */
    public function testCanSetOrIsNotNull(Conditions $query)
    {
        $query->orIsNotNull('Bar');

        $this->assertEquals('`Foo` IS NOT NULL OR `Bar` IS NOT NULL', $query->toString());
    }

    public function testCanSetBetween()
    {
        $query = $this->newQuery();

        $query->between('Foo', 1, 10);

        $this->assertEquals(['`Foo` BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetBetween
     *
     * @param Conditions $query
     */
    public function testCanSetOrBetween(Conditions $query)
    {
        $query->orBetween('Bar', 1, 10);

        $this->assertEquals(['`Foo` BETWEEN ? AND ? OR `Bar` BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetNotBetween()
    {
        $query = $this->newQuery();

        $query->notBetween('Foo', 1, 10);

        $this->assertEquals(['`Foo` NOT BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetNotBetween
     *
     * @param Conditions $query
     */
    public function testCanSetOrNotBetween(Conditions $query)
    {
        $query->orNotBetween('Bar', 1, 10);

        $this->assertEquals(['`Foo` NOT BETWEEN ? AND ? OR `Bar` NOT BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetDate()
    {
        $query = $this->newQuery();

        $query->date('Foo', '1990-07-15');

        $this->assertEquals(['DATE(`Foo`) = ?', ['1990-07-15']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDate
     *
     * @param Conditions $query
     */
    public function testCanSetOrDate(Conditions $query)
    {
        $query->orDate('Bar', '1990-07-15');

        $this->assertEquals(['DATE(`Foo`) = ? OR DATE(`Bar`) = ?', ['1990-07-15', '1990-07-15']], $query->toSql());
    }

    public function testCanSetMultipleDates()
    {
        $query = $this->newQuery();

        $query->date('Foo', [$date = date('Y-m-d'), $date]);

        $this->assertEquals(['DATE(`Foo`) IN (?, ?)', [$date, $date]], $query->toSql());
    }

    public function testCanSetDateRow()
    {
        $query = $this->newQuery();

        $query->date(['Foo', 'Bar'], [date('Y-m-d'), date('Y-m-d')]);

        $this->assertEquals(['(DATE(`Foo`), DATE(`Bar`)) = (?, ?)', [date('Y-m-d'), date('Y-m-d')]], $query->toSql());
    }

    public function testCanSetTime()
    {
        $query = $this->newQuery();

        $query->time('Foo', '19:00:00');

        $this->assertEquals(['TIME(`Foo`) = ?', ['19:00:00']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetTime
     *
     * @param Conditions $query
     */
    public function testCanSetOrTime(Conditions $query)
    {
        $query->orTime('Bar', '19:00:00');

        $this->assertEquals(['TIME(`Foo`) = ? OR TIME(`Bar`) = ?', ['19:00:00', '19:00:00']], $query->toSql());
    }

    public function testCanSetYear()
    {
        $query = $this->newQuery();

        $query->year('Foo', 2016);

        $this->assertEquals(['YEAR(`Foo`) = ?', [2016]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetYear
     *
     * @param Conditions $query
     */
    public function testCanSetOrYear(Conditions $query)
    {
        $query->orYear('Bar', 2016);

        $this->assertEquals(['YEAR(`Foo`) = ? OR YEAR(`Bar`) = ?', [2016, 2016]], $query->toSql());
    }

    public function testCanSetMonth()
    {
        $query = $this->newQuery();

        $query->month('Foo', '01');

        $this->assertEquals(['MONTH(`Foo`) = ?', ['01']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetMonth
     *
     * @param Conditions $query
     */
    public function testCanSetOrMonth(Conditions $query)
    {
        $query->orMonth('Bar', '01');

        $this->assertEquals(['MONTH(`Foo`) = ? OR MONTH(`Bar`) = ?', ['01', '01']], $query->toSql());
    }

    public function testCanSetDay()
    {
        $query = $this->newQuery();

        $query->day('Foo', '01');

        $this->assertEquals(['DAY(`Foo`) = ?', ['01']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDay
     *
     * @param Conditions $query
     */
    public function testCanSetOrDay(Conditions $query)
    {
        $query->orDay('Bar', '01');

        $this->assertEquals(['DAY(`Foo`) = ? OR DAY(`Bar`) = ?', ['01', '01']], $query->toSql());
    }

    public function testCanSetConditionsCallable()
    {
        $query = $this->newQuery();

        $query->conditions(function (Conditions $query) {
            $query->column('Foo', 'foo');
        });

        $this->assertEquals(['(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetConditionsCallable
     *
     * @param Conditions $query
     */
    public function testCanSetOrConditionsCallable(Conditions $query)
    {
        $query->orConditions(function (Conditions $query) {
            $query->column('Bar', 'bar');
        });

        $this->assertEquals(['(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetConditions()
    {
        $query = $this->newQuery();

        $query->conditions($this->newQuery()->column('Foo', 'foo'));

        $this->assertEquals(['(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetConditions
     *
     * @param Conditions $query
     */
    public function testCanSetOrConditions(Conditions $query)
    {
        $query->orConditions($this->newQuery()->column('Bar', 'bar'));

        $this->assertEquals(['(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRaw()
    {
        $query = $this->newQuery();

        $query->raw('`Foo` = ?', 'foo');

        $this->assertEquals(['(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRaw
     *
     * @param Conditions $query
     */
    public function testCanSetOrRaw(Conditions $query)
    {
        $query->orRaw('`Bar` = ?', 'bar');

        $this->assertEquals(['(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanDetermineIfConditionsExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->has());

        $query->raw('`Foo` = ?', 'foo');

        $this->assertTrue($query->has());
    }

    public function testCanGet()
    {
        $query = $this->newQuery();

        $query->raw('`Foo` = ?', 'foo');

        $this->assertCount(1, $query->get());
    }

    public function testCanClear()
    {
        $query = $this->newQuery();

        $query->raw('`Foo` = ?', 'foo');

        $query->clear();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newQuery();

        $query->column('Foo', 'foo');

        $this->assertEquals('`Foo` = ?', (string) $query);
    }

    public function testCanAddLogic()
    {
        $query = $this->newQuery();

        $query->addCondition('and', '`Foo` = ?', ['foo']);

        $this->assertEquals('`Foo` = ?', (string) $query);
    }

    protected function newQuery(): Conditions
    {
        return new Conditions();
    }
}
