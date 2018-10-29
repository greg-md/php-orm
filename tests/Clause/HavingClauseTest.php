<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\SqlException;
use PHPUnit\Framework\TestCase;

class HavingClauseTest extends TestCase
{
    public function testCanSetColumn()
    {
        $query = $this->newQuery();

        $query->having('Foo', 'foo');

        $this->assertEquals(['HAVING `Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    public function testCanTransformArrayColumnValueToScalar()
    {
        $query = $this->newQuery();

        $query->having(['Foo'], ['foo']);

        $this->assertEquals(['HAVING `Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanTransformColumnInToEquals()
    {
        $query = $this->newQuery();

        $query->having('Foo', 'IN', ['foo']);

        $this->assertEquals(['HAVING `Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanSetColumnWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->having('Foo', ['foo1', 'foo2']);

        $this->assertEquals(['HAVING `Foo` IN (?, ?)', ['foo1', 'foo2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenColumnOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->having('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetColumnRow()
    {
        $query = $this->newQuery();

        $query->having(['Foo', 'Bar'], ['foo', 'bar']);

        $this->assertEquals(['HAVING (`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanTransformColumnRowInToEquals()
    {
        $query = $this->newQuery();

        $query->having(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        $this->assertEquals(['HAVING (`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetColumnRowWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->having(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        $this->assertEquals(['HAVING (`Foo`, `Bar`) IN ((?, ?), (?, ?))', ['foo1', 'bar1', 'foo2', 'bar2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenColumnRowOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->having(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenColumnRowInValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->having(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenColumnRowValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->having(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    public function testCanWorkWithNullInRow()
    {
        $query = $this->newQuery();

        $query->having(['Foo', 'Bar'], ['foo', null]);

        $this->assertEquals(['HAVING (`Foo`, `Bar`) = (?, ?)', ['foo', '']], $query->toSql());
    }

    /**
     * @test
     *
     * @depends testCanSetColumn
     *
     * @param HavingClause $query
     */
    public function testCanSetOrColumn(HavingClause $query)
    {
        $query->orHaving('Bar', 'bar');

        $this->assertEquals(['HAVING `Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetColumns()
    {
        $query = $this->newQuery();

        $query->havingMultiple([
            'Foo' => 'foo',
        ]);

        $this->assertEquals(['HAVING `Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetColumns
     *
     * @param HavingClause $query
     */
    public function testCanSetOrColumns(HavingClause $query)
    {
        $query->orHavingMultiple([
            'Bar' => 'bar',
        ]);

        $this->assertEquals(['HAVING `Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRelation()
    {
        $query = $this->newQuery();

        $query->havingRelation('Foo', 'foo');

        $this->assertEquals('HAVING `Foo` = `foo`', $query->toString());

        return $query;
    }

    public function testCanTransformArrayRelationValueToScalar()
    {
        $query = $this->newQuery();

        $query->havingRelation(['Foo'], ['foo']);

        $this->assertEquals('HAVING `Foo` = `foo`', $query->toString());
    }

    public function testCanTransformRelationInToEquals()
    {
        $query = $this->newQuery();

        $query->havingRelation('Foo', 'IN', ['foo']);

        $this->assertEquals('HAVING `Foo` = `foo`', $query->toString());
    }

    public function testCanSetRelationWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->havingRelation('Foo', ['foo1', 'foo2']);

        $this->assertEquals('HAVING `Foo` IN (`foo1`, `foo2`)', $query->toString());
    }

    public function testCanThrowExceptionWhenRelationOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->havingRelation('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetRelationRow()
    {
        $query = $this->newQuery();

        $query->havingRelation(['Foo', 'Bar'], ['foo', 'bar']);

        $this->assertEquals('HAVING (`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
    }

    public function testCanTransformRelationRowInToEquals()
    {
        $query = $this->newQuery();

        $query->havingRelation(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        $this->assertEquals('HAVING (`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
    }

    public function testCanSetRelationRowWithMultipleValues()
    {
        $query = $this->newQuery();

        $query->havingRelation(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        $this->assertEquals('HAVING (`Foo`, `Bar`) IN ((`foo1`, `bar1`), (`foo2`, `bar2`))', $query->toString());
    }

    public function testCanThrowExceptionWhenRelationRowOperatorDoesNotAllowArrays()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->havingRelation(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenRelationRowInValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->havingRelation(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenRelationRowValuesAreNotTheSameLength()
    {
        $query = $this->newQuery();

        $this->expectException(SqlException::class);

        $query->havingRelation(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    /**
     * @test
     *
     * @depends testCanSetRelation
     *
     * @param HavingClause $query
     */
    public function testCanSetOrRelation(HavingClause $query)
    {
        $query->orHavingRelation('Bar', 'bar');

        $this->assertEquals('HAVING `Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetRelations()
    {
        $query = $this->newQuery();

        $query->havingRelations([
            'Foo' => 'foo',
        ]);

        $this->assertEquals('HAVING `Foo` = `foo`', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRelations
     *
     * @param HavingClause $query
     */
    public function testCanSetOrRelations(HavingClause $query)
    {
        $query->orHavingRelations([
            'Bar' => 'bar',
        ]);

        $this->assertEquals('HAVING `Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetIs()
    {
        $query = $this->newQuery();

        $query->havingIs('Foo');

        $this->assertEquals('HAVING `Foo` = 1', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIs
     *
     * @param HavingClause $query
     */
    public function testCanSetOrIs(HavingClause $query)
    {
        $query->orHavingIs('Bar');

        $this->assertEquals('HAVING `Foo` = 1 OR `Bar` = 1', $query->toString());
    }

    public function testCanSetIsNot()
    {
        $query = $this->newQuery();

        $query->havingIsNot('Foo');

        $this->assertEquals('HAVING `Foo` = 0', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNot
     *
     * @param HavingClause $query
     */
    public function testCanSetOrIsNot(HavingClause $query)
    {
        $query->orHavingIsNot('Bar');

        $this->assertEquals('HAVING `Foo` = 0 OR `Bar` = 0', $query->toString());
    }

    public function testCanSetIsNull()
    {
        $query = $this->newQuery();

        $query->havingIsNull('Foo');

        $this->assertEquals('HAVING `Foo` IS NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNull
     *
     * @param HavingClause $query
     */
    public function testCanSetOrIsNull(HavingClause $query)
    {
        $query->orHavingIsNull('Bar');

        $this->assertEquals('HAVING `Foo` IS NULL OR `Bar` IS NULL', $query->toString());
    }

    public function testCanSetIsNotNull()
    {
        $query = $this->newQuery();

        $query->havingIsNotNull('Foo');

        $this->assertEquals('HAVING `Foo` IS NOT NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNotNull
     *
     * @param HavingClause $query
     */
    public function testCanSetOrIsNotNull(HavingClause $query)
    {
        $query->orHavingIsNotNull('Bar');

        $this->assertEquals('HAVING `Foo` IS NOT NULL OR `Bar` IS NOT NULL', $query->toString());
    }

    public function testCanSetBetween()
    {
        $query = $this->newQuery();

        $query->havingBetween('Foo', 1, 10);

        $this->assertEquals(['HAVING `Foo` BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetBetween
     *
     * @param HavingClause $query
     */
    public function testCanSetOrBetween(HavingClause $query)
    {
        $query->orHavingBetween('Bar', 1, 10);

        $this->assertEquals(['HAVING `Foo` BETWEEN ? AND ? OR `Bar` BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetNotBetween()
    {
        $query = $this->newQuery();

        $query->havingNotBetween('Foo', 1, 10);

        $this->assertEquals(['HAVING `Foo` NOT BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetNotBetween
     *
     * @param HavingClause $query
     */
    public function testCanSetOrNotBetween(HavingClause $query)
    {
        $query->orHavingNotBetween('Bar', 1, 10);

        $this->assertEquals(['HAVING `Foo` NOT BETWEEN ? AND ? OR `Bar` NOT BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetDate()
    {
        $query = $this->newQuery();

        $query->havingDate('Foo', '1990-07-15');

        $this->assertEquals(['HAVING DATE(`Foo`) = ?', ['1990-07-15']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDate
     *
     * @param HavingClause $query
     */
    public function testCanSetOrDate(HavingClause $query)
    {
        $query->orHavingDate('Bar', '1990-07-15');

        $this->assertEquals(['HAVING DATE(`Foo`) = ? OR DATE(`Bar`) = ?', ['1990-07-15', '1990-07-15']], $query->toSql());
    }

    public function testCanSetMultipleDates()
    {
        $query = $this->newQuery();

        $query->havingDate('Foo', [$date = date('Y-m-d'), $date]);

        $this->assertEquals(['HAVING DATE(`Foo`) IN (?, ?)', [$date, $date]], $query->toSql());
    }

    public function testCanSetDateRow()
    {
        $query = $this->newQuery();

        $query->havingDate(['Foo', 'Bar'], [date('Y-m-d'), date('Y-m-d')]);

        $this->assertEquals(['HAVING (DATE(`Foo`), DATE(`Bar`)) = (?, ?)', [date('Y-m-d'), date('Y-m-d')]], $query->toSql());
    }

    public function testCanSetTime()
    {
        $query = $this->newQuery();

        $query->havingTime('Foo', '19:00:00');

        $this->assertEquals(['HAVING TIME(`Foo`) = ?', ['19:00:00']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetTime
     *
     * @param HavingClause $query
     */
    public function testCanSetOrTime(HavingClause $query)
    {
        $query->orHavingTime('Bar', '19:00:00');

        $this->assertEquals(['HAVING TIME(`Foo`) = ? OR TIME(`Bar`) = ?', ['19:00:00', '19:00:00']], $query->toSql());
    }

    public function testCanSetYear()
    {
        $query = $this->newQuery();

        $query->havingYear('Foo', 2016);

        $this->assertEquals(['HAVING YEAR(`Foo`) = ?', [2016]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetYear
     *
     * @param HavingClause $query
     */
    public function testCanSetOrYear(HavingClause $query)
    {
        $query->orHavingYear('Bar', 2016);

        $this->assertEquals(['HAVING YEAR(`Foo`) = ? OR YEAR(`Bar`) = ?', [2016, 2016]], $query->toSql());
    }

    public function testCanSetMonth()
    {
        $query = $this->newQuery();

        $query->havingMonth('Foo', '01');

        $this->assertEquals(['HAVING MONTH(`Foo`) = ?', [1]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetMonth
     *
     * @param HavingClause $query
     */
    public function testCanSetOrMonth(HavingClause $query)
    {
        $query->orHavingMonth('Bar', '01');

        $this->assertEquals(['HAVING MONTH(`Foo`) = ? OR MONTH(`Bar`) = ?', [1, 1]], $query->toSql());
    }

    public function testCanSetDay()
    {
        $query = $this->newQuery();

        $query->havingDay('Foo', '01');

        $this->assertEquals(['HAVING DAY(`Foo`) = ?', [1]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDay
     *
     * @param HavingClause $query
     */
    public function testCanSetOrDay(HavingClause $query)
    {
        $query->orHavingDay('Bar', '01');

        $this->assertEquals(['HAVING DAY(`Foo`) = ? OR DAY(`Bar`) = ?', [1, 1]], $query->toSql());
    }

    public function testCanSetConditionsCallable()
    {
        $query = $this->newQuery();

        $query->havingConditions(function (Conditions $query) {
            $query->column('Foo', 'foo');
        });

        $this->assertEquals(['HAVING (`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetConditionsCallable
     *
     * @param HavingClause $query
     */
    public function testCanSetOrConditionsCallable(HavingClause $query)
    {
        $query->orHavingConditions(function (Conditions $query) {
            $query->column('Bar', 'bar');
        });

        $this->assertEquals(['HAVING (`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetConditions()
    {
        $query = $this->newQuery();

        $query->havingConditions($this->newQuery()->having('Foo', 'foo'));

        $this->assertEquals(['HAVING (`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetConditions
     *
     * @param HavingClause $query
     */
    public function testCanSetOrConditions(HavingClause $query)
    {
        $query->orHavingConditions($this->newQuery()->having('Bar', 'bar'));

        $this->assertEquals(['HAVING (`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRaw()
    {
        $query = $this->newQuery();

        $query->havingRaw('`Foo` = ?', 'foo');

        $this->assertEquals(['HAVING (`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRaw
     *
     * @param HavingClause $query
     */
    public function testCanSetOrRaw(HavingClause $query)
    {
        $query->orHavingRaw('`Bar` = ?', 'bar');

        $this->assertEquals(['HAVING (`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanDetermineIfConditionsExists()
    {
        $query = $this->newQuery();

        $this->assertFalse($query->hasHaving());

        $query->havingRaw('`Foo` = ?', 'foo');

        $this->assertTrue($query->hasHaving());
    }

    public function testCanGet()
    {
        $query = $this->newQuery();

        $query->havingRaw('`Foo` = ?', 'foo');

        $this->assertCount(1, $query->getHaving());
    }

    public function testCanClear()
    {
        $query = $this->newQuery();

        $query->havingRaw('`Foo` = ?', 'foo');

        $query->clearHaving();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newQuery();

        $query->having('Foo', 'foo');

        $this->assertEquals('HAVING `Foo` = ?', (string) $query);
    }

    public function testCanAddLogic()
    {
        $query = $this->newQuery();

        $query->addHaving('and', '`Foo` = ?', ['foo']);

        $this->assertEquals('HAVING `Foo` = ?', (string) $query);
    }

    public function testCanClone()
    {
        $query = $this->newQuery()->having('Foo', 'foo');

        $clone = clone $query;

        $clone->having('Bar', 'bar');

        $this->assertEquals(['HAVING `Foo` = ?', ['foo']], $query->toSql());

        $this->assertEquals(['HAVING `Foo` = ? AND `Bar` = ?', ['foo', 'bar']], $clone->toSql());
    }

    protected function newQuery(): HavingClause
    {
        return new HavingClause();
    }
}
