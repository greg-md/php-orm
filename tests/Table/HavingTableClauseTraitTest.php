<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Conditions;
use Greg\Orm\Model;
use Greg\Orm\SqlException;

trait HavingTableClauseTraitTest
{
    public function testCanSetHavingColumn()
    {
        $query = $this->newHavingQuery();

        $query->having('Foo', 'foo');

        $this->assertEquals(['HAVING `Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    public function testCanTransformHavingArrayColumnValueToScalar()
    {
        $query = $this->newHavingQuery();

        $query->having(['Foo'], ['foo']);

        $this->assertEquals(['HAVING `Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanTransformHavingColumnInToEquals()
    {
        $query = $this->newHavingQuery();

        $query->having('Foo', 'IN', ['foo']);

        $this->assertEquals(['HAVING `Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanSetHavingColumnWithMultipleValues()
    {
        $query = $this->newHavingQuery();

        $query->having('Foo', ['foo1', 'foo2']);

        $this->assertEquals(['HAVING `Foo` IN (?, ?)', ['foo1', 'foo2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenHavingColumnOperatorDoesNotAllowArrays()
    {
        $query = $this->newHavingQuery();

        $this->expectException(SqlException::class);

        $query->having('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetHavingColumnRow()
    {
        $query = $this->newHavingQuery();

        $query->having(['Foo', 'Bar'], ['foo', 'bar']);

        $this->assertEquals(['HAVING (`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanTransformHavingColumnRowInToEquals()
    {
        $query = $this->newHavingQuery();

        $query->having(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        $this->assertEquals(['HAVING (`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetHavingColumnRowWithMultipleValues()
    {
        $query = $this->newHavingQuery();

        $query->having(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        $this->assertEquals(['HAVING (`Foo`, `Bar`) IN ((?, ?), (?, ?))', ['foo1', 'bar1', 'foo2', 'bar2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenHavingColumnRowOperatorDoesNotAllowArrays()
    {
        $query = $this->newHavingQuery();

        $this->expectException(SqlException::class);

        $query->having(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenHavingColumnRowInValuesAreNotTheSameLength()
    {
        $query = $this->newHavingQuery();

        $this->expectException(SqlException::class);

        $query->having(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenHavingColumnRowValuesAreNotTheSameLength()
    {
        $query = $this->newHavingQuery();

        $this->expectException(SqlException::class);

        $query->having(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    public function testCanWorkWithHavingNullInRow()
    {
        $query = $this->newHavingQuery();

        $query->having(['Foo', 'Bar'], ['foo', null]);

        $this->assertEquals(['HAVING (`Foo`, `Bar`) = (?, ?)', ['foo', '']], $query->toSql());
    }

    /**
     * @test
     *
     * @depends testCanSetHavingColumn
     *
     * @param Model $query
     */
    public function testCanSetHavingOrColumn(Model $query)
    {
        $query->orHaving('Bar', 'bar');

        $this->assertEquals(['HAVING `Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetHavingColumns()
    {
        $query = $this->newHavingQuery();

        $query->havingMultiple([
            'Foo' => 'foo',
        ]);

        $this->assertEquals(['HAVING `Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingColumns
     *
     * @param Model $query
     */
    public function testCanSetHavingOrColumns(Model $query)
    {
        $query->orHavingMultiple([
            'Bar' => 'bar',
        ]);

        $this->assertEquals(['HAVING `Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetHavingRelation()
    {
        $query = $this->newHavingQuery();

        $query->havingRelation('Foo', 'foo');

        $this->assertEquals('HAVING `Foo` = `foo`', $query->toString());

        return $query;
    }

    public function testCanTransformHavingArrayRelationValueToScalar()
    {
        $query = $this->newHavingQuery();

        $query->havingRelation(['Foo'], ['foo']);

        $this->assertEquals('HAVING `Foo` = `foo`', $query->toString());
    }

    public function testCanTransformHavingRelationInToEquals()
    {
        $query = $this->newHavingQuery();

        $query->havingRelation('Foo', 'IN', ['foo']);

        $this->assertEquals('HAVING `Foo` = `foo`', $query->toString());
    }

    public function testCanSetHavingRelationWithMultipleValues()
    {
        $query = $this->newHavingQuery();

        $query->havingRelation('Foo', ['foo1', 'foo2']);

        $this->assertEquals('HAVING `Foo` IN (`foo1`, `foo2`)', $query->toString());
    }

    public function testCanThrowExceptionWhenHavingRelationOperatorDoesNotAllowArrays()
    {
        $query = $this->newHavingQuery();

        $this->expectException(SqlException::class);

        $query->havingRelation('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetHavingRelationRow()
    {
        $query = $this->newHavingQuery();

        $query->havingRelation(['Foo', 'Bar'], ['foo', 'bar']);

        $this->assertEquals('HAVING (`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
    }

    public function testCanTransformHavingRelationRowInToEquals()
    {
        $query = $this->newHavingQuery();

        $query->havingRelation(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        $this->assertEquals('HAVING (`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
    }

    public function testCanSetHavingRelationRowWithMultipleValues()
    {
        $query = $this->newHavingQuery();

        $query->havingRelation(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        $this->assertEquals('HAVING (`Foo`, `Bar`) IN ((`foo1`, `bar1`), (`foo2`, `bar2`))', $query->toString());
    }

    public function testCanThrowExceptionWhenHavingRelationRowOperatorDoesNotAllowArrays()
    {
        $query = $this->newHavingQuery();

        $this->expectException(SqlException::class);

        $query->havingRelation(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenHavingRelationRowInValuesAreNotTheSameLength()
    {
        $query = $this->newHavingQuery();

        $this->expectException(SqlException::class);

        $query->havingRelation(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenHavingRelationRowValuesAreNotTheSameLength()
    {
        $query = $this->newHavingQuery();

        $this->expectException(SqlException::class);

        $query->havingRelation(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    /**
     * @test
     *
     * @depends testCanSetHavingRelation
     *
     * @param Model $query
     */
    public function testCanSetOrHavingRelation(Model $query)
    {
        $query->orHavingRelation('Bar', 'bar');

        $this->assertEquals('HAVING `Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetHavingRelations()
    {
        $query = $this->newHavingQuery();

        $query->havingRelations([
            'Foo' => 'foo',
        ]);

        $this->assertEquals('HAVING `Foo` = `foo`', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingRelations
     *
     * @param Model $query
     */
    public function testCanSetOrHavingRelations(Model $query)
    {
        $query->orHavingRelations([
            'Bar' => 'bar',
        ]);

        $this->assertEquals('HAVING `Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetHavingIs()
    {
        $query = $this->newHavingQuery();

        $query->havingIs('Foo');

        $this->assertEquals('HAVING `Foo` = 1', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingIs
     *
     * @param Model $query
     */
    public function testCanSetOrHavingIs(Model $query)
    {
        $query->orHavingIs('Bar');

        $this->assertEquals('HAVING `Foo` = 1 OR `Bar` = 1', $query->toString());
    }

    public function testCanSetHavingIsNot()
    {
        $query = $this->newHavingQuery();

        $query->havingIsNot('Foo');

        $this->assertEquals('HAVING `Foo` = 0', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingIsNot
     *
     * @param Model $query
     */
    public function testCanSetOrHavingIsNot(Model $query)
    {
        $query->orHavingIsNot('Bar');

        $this->assertEquals('HAVING `Foo` = 0 OR `Bar` = 0', $query->toString());
    }

    public function testCanSetHavingIsNull()
    {
        $query = $this->newHavingQuery();

        $query->havingIsNull('Foo');

        $this->assertEquals('HAVING `Foo` IS NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingIsNull
     *
     * @param Model $query
     */
    public function testCanSetOrHavingIsNull(Model $query)
    {
        $query->orHavingIsNull('Bar');

        $this->assertEquals('HAVING `Foo` IS NULL OR `Bar` IS NULL', $query->toString());
    }

    public function testCanSetHavingIsNotNull()
    {
        $query = $this->newHavingQuery();

        $query->havingIsNotNull('Foo');

        $this->assertEquals('HAVING `Foo` IS NOT NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingIsNotNull
     *
     * @param Model $query
     */
    public function testCanSetOrHavingIsNotNull(Model $query)
    {
        $query->orHavingIsNotNull('Bar');

        $this->assertEquals('HAVING `Foo` IS NOT NULL OR `Bar` IS NOT NULL', $query->toString());
    }

    public function testCanSetHavingBetween()
    {
        $query = $this->newHavingQuery();

        $query->havingBetween('Foo', 1, 10);

        $this->assertEquals(['HAVING `Foo` BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingBetween
     *
     * @param Model $query
     */
    public function testCanSetOrHavingBetween(Model $query)
    {
        $query->orHavingBetween('Bar', 1, 10);

        $this->assertEquals(['HAVING `Foo` BETWEEN ? AND ? OR `Bar` BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetHavingNotBetween()
    {
        $query = $this->newHavingQuery();

        $query->havingNotBetween('Foo', 1, 10);

        $this->assertEquals(['HAVING `Foo` NOT BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingNotBetween
     *
     * @param Model $query
     */
    public function testCanSetOrHavingNotBetween(Model $query)
    {
        $query->orHavingNotBetween('Bar', 1, 10);

        $this->assertEquals(['HAVING `Foo` NOT BETWEEN ? AND ? OR `Bar` NOT BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetHavingDate()
    {
        $query = $this->newHavingQuery();

        $query->havingDate('Foo', '1990-07-15');

        $this->assertEquals(['HAVING DATE(`Foo`) = ?', ['1990-07-15']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingDate
     *
     * @param Model $query
     */
    public function testCanSetOrHavingDate(Model $query)
    {
        $query->orHavingDate('Bar', '1990-07-15');

        $this->assertEquals(['HAVING DATE(`Foo`) = ? OR DATE(`Bar`) = ?', ['1990-07-15', '1990-07-15']], $query->toSql());
    }

    public function testCanSetHavingMultipleDates()
    {
        $query = $this->newHavingQuery();

        $query->havingDate('Foo', [$date = date('Y-m-d'), $date]);

        $this->assertEquals(['HAVING DATE(`Foo`) IN (?, ?)', [$date, $date]], $query->toSql());
    }

    public function testCanSetHavingDateRow()
    {
        $query = $this->newHavingQuery();

        $query->havingDate(['Foo', 'Bar'], [date('Y-m-d'), date('Y-m-d')]);

        $this->assertEquals(['HAVING (DATE(`Foo`), DATE(`Bar`)) = (?, ?)', [date('Y-m-d'), date('Y-m-d')]], $query->toSql());
    }

    public function testCanSetHavingTime()
    {
        $query = $this->newHavingQuery();

        $query->havingTime('Foo', '19:00:00');

        $this->assertEquals(['HAVING TIME(`Foo`) = ?', ['19:00:00']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingTime
     *
     * @param Model $query
     */
    public function testCanSetOrHavingTime(Model $query)
    {
        $query->orHavingTime('Bar', '19:00:00');

        $this->assertEquals(['HAVING TIME(`Foo`) = ? OR TIME(`Bar`) = ?', ['19:00:00', '19:00:00']], $query->toSql());
    }

    public function testCanSetHavingYear()
    {
        $query = $this->newHavingQuery();

        $query->havingYear('Foo', 2016);

        $this->assertEquals(['HAVING YEAR(`Foo`) = ?', [2016]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingYear
     *
     * @param Model $query
     */
    public function testCanSetOrHavingYear(Model $query)
    {
        $query->orHavingYear('Bar', 2016);

        $this->assertEquals(['HAVING YEAR(`Foo`) = ? OR YEAR(`Bar`) = ?', [2016, 2016]], $query->toSql());
    }

    public function testCanSetHavingMonth()
    {
        $query = $this->newHavingQuery();

        $query->havingMonth('Foo', '01');

        $this->assertEquals(['HAVING MONTH(`Foo`) = ?', [1]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingMonth
     *
     * @param Model $query
     */
    public function testCanSetOrHavingMonth(Model $query)
    {
        $query->orHavingMonth('Bar', '01');

        $this->assertEquals(['HAVING MONTH(`Foo`) = ? OR MONTH(`Bar`) = ?', [1, 1]], $query->toSql());
    }

    public function testCanSetHavingDay()
    {
        $query = $this->newHavingQuery();

        $query->havingDay('Foo', '01');

        $this->assertEquals(['HAVING DAY(`Foo`) = ?', [1]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingDay
     *
     * @param Model $query
     */
    public function testCanSetOrHavingDay(Model $query)
    {
        $query->orHavingDay('Bar', '01');

        $this->assertEquals(['HAVING DAY(`Foo`) = ? OR DAY(`Bar`) = ?', [1, 1]], $query->toSql());
    }

    public function testCanSetHavingConditionsCallable()
    {
        $query = $this->newHavingQuery();

        $query->havingConditions(function (Conditions $query) {
            $query->column('Foo', 'foo');
        });

        $this->assertEquals(['HAVING (`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingConditionsCallable
     *
     * @param Model $query
     */
    public function testCanSetOrHavingConditionsCallable(Model $query)
    {
        $query->orHavingConditions(function (Conditions $query) {
            $query->column('Bar', 'bar');
        });

        $this->assertEquals(['HAVING (`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetHavingRaw()
    {
        $query = $this->newHavingQuery();

        $query->havingRaw('`Foo` = ?', 'foo');

        $this->assertEquals(['HAVING (`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetHavingRaw
     *
     * @param Model $query
     */
    public function testCanSetOrHavingRaw(Model $query)
    {
        $query->orHavingRaw('`Bar` = ?', 'bar');

        $this->assertEquals(['HAVING (`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanDetermineIfHavingExists()
    {
        $query = $this->newHavingQuery();

        $this->assertFalse($query->hasHaving());

        $query->havingRaw('`Foo` = ?', 'foo');

        $this->assertTrue($query->hasHaving());
    }

    public function testCanGetHaving()
    {
        $query = $this->newHavingQuery();

        $query->havingRaw('`Foo` = ?', 'foo');

        $this->assertCount(1, $query->getHaving());
    }

    public function testCanClearHaving()
    {
        $query = $this->newHavingQuery();

        $query->havingRaw('`Foo` = ?', 'foo');

        $query->clearHaving();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformHavingToString()
    {
        $query = $this->newHavingQuery();

        $query->having('Foo', 'foo');

        $this->assertEquals('HAVING `Foo` = ?', (string) $query);
    }

    public function testCanAssignHavingAppliers()
    {
        $this->model()->setHavingApplier(function (HavingClause $clause) {
            $clause->having('Column', 'bar');
        });

        $query = $this->model()->having('Column', 'foo');

        $this->assertEquals('HAVING (`Column` = ?) AND (`Column` = ?)', $query->toString());
    }

    public function testCanDetermineIfHavingAppliersExists()
    {
        $this->assertFalse($this->model()->hasHavingAppliers());

        $this->model()->setHavingApplier(function () {
        });

        $this->assertTrue($this->model()->hasHavingAppliers());
    }

    public function testCanGetHavingAppliers()
    {
        $this->model()->setHavingApplier(function () {
        });

        $this->assertCount(1, $this->model()->getHavingAppliers());
    }

    public function testCanClearHavingAppliers()
    {
        $this->model()->setHavingApplier(function () {
        });

        $this->model()->clearHavingAppliers();

        $this->assertFalse($this->model()->hasHavingAppliers());
    }

    public function testCanDetermineIfHavingExists2()
    {
        $this->assertFalse($this->model()->hasHaving());

        $this->assertFalse($this->model()->select('Column')->hasHaving());
    }

    public function testCanDetermineIfHavingClauseExists()
    {
        $this->assertFalse($this->model()->hasHavingClause());

        $this->assertFalse($this->model()->select('Column')->hasHavingClause());
    }

    public function testCanGetEmptyHaving()
    {
        $this->assertCount(0, $this->model()->getHaving());
    }

    public function testCanClearEmptyHaving()
    {
        $this->model()->clearHaving();

        $this->assertFalse($this->model()->hasHaving());
    }

    public function testCanGetClauseString()
    {
        $query = $this->model()->having('Column', 'foo');

        $this->assertEquals('HAVING `Column` = ?', $query->havingToString());
    }

    public function testCanEmptyHaving()
    {
        $this->assertEquals('', $this->model()->havingToString());
    }

    public function testCanSelectHaving()
    {
        $query = $this->model()->select('Column')->having('Column', 'foo');

        $this->assertEquals('SELECT `Column` FROM `Table` HAVING `Column` = ?', $query->toString());
    }

    public function testCanThrowExceptionIfNotHavingStrategy()
    {
        $this->expectException(SqlException::class);

        $this->model()->updateTable('Table2')->having('Column', 'foo');
    }

    protected function newHavingQuery(): Model
    {
        return $this->model()->intoHavingStrategy();
    }

    abstract protected function model(): Model;
}
