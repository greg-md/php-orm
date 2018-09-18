<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Conditions;
use Greg\Orm\Connection\ConnectionStrategy;
use Greg\Orm\Model;
use Greg\Orm\SqlException;

trait WhereTableClauseTraitTest
{
    public function testCanSetWhereColumn()
    {
        $query = $this->newQuery();

        $query->where('Foo', 'foo');

        $this->assertEquals(['WHERE `Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    public function testCanTransformWhereArrayColumnValueToScalar()
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
     * @depends testCanSetWhereColumn
     *
     * @param Model $query
     */
    public function testCanSetOrWhereColumn(Model $query)
    {
        $query->orWhere('Bar', 'bar');

        $this->assertEquals(['WHERE `Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetWhereColumns()
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
     * @depends testCanSetWhereColumns
     *
     * @param Model $query
     */
    public function testCanSetOrColumns(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrRelation(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrRelations(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrIs(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrIsNot(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrIsNull(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrIsNotNull(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrBetween(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrNotBetween(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrDate(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrTime(Model $query)
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
     * @param Model $query
     */
    public function testCanSetOrYear(Model $query)
    {
        $query->orWhereYear('Bar', 2016);

        $this->assertEquals(['WHERE YEAR(`Foo`) = ? OR YEAR(`Bar`) = ?', [2016, 2016]], $query->toSql());
    }

    public function testCanSetMonth()
    {
        $query = $this->newQuery();

        $query->whereMonth('Foo', '01');

        $this->assertEquals(['WHERE MONTH(`Foo`) = ?', ['01']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetMonth
     *
     * @param Model $query
     */
    public function testCanSetOrMonth(Model $query)
    {
        $query->orWhereMonth('Bar', '01');

        $this->assertEquals(['WHERE MONTH(`Foo`) = ? OR MONTH(`Bar`) = ?', ['01', '01']], $query->toSql());
    }

    public function testCanSetDay()
    {
        $query = $this->newQuery();

        $query->whereDay('Foo', '01');

        $this->assertEquals(['WHERE DAY(`Foo`) = ?', ['01']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDay
     *
     * @param Model $query
     */
    public function testCanSetOrDay(Model $query)
    {
        $query->orWhereDay('Bar', '01');

        $this->assertEquals(['WHERE DAY(`Foo`) = ? OR DAY(`Bar`) = ?', ['01', '01']], $query->toSql());
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
     * @param Model $query
     */
    public function testCanSetOrConditionsCallable(Model $query)
    {
        $query->orWhereConditions(function (Conditions $query) {
            $query->column('Bar', 'bar');
        });

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
     * @param Model $query
     */
    public function testCanSetOrRaw(Model $query)
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

    public function testCanGetWhere()
    {
        $query = $this->newQuery();

        $query->whereRaw('`Foo` = ?', 'foo');

        $this->assertCount(1, $query->getWhere());
    }

    public function testCanClearWhere()
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

    public function testCanAssignWhereAppliers()
    {
        $this->model()->setWhereApplier(function (WhereClause $clause) {
            $clause->where('Column', 'bar');
        });

        $query = $this->model()->where('Column', 'foo');

        $this->assertEquals('WHERE (`Column` = ?) AND (`Column` = ?)', $query->toString());
    }

    public function testCanDetermineIfWhereAppliersExists()
    {
        $this->assertFalse($this->model()->hasWhereAppliers());

        $this->model()->setWhereApplier(function () {
        });

        $this->assertTrue($this->model()->hasWhereAppliers());
    }

    public function testCanGetWhereAppliers()
    {
        $this->model()->setWhereApplier(function () {
        });

        $this->assertCount(1, $this->model()->getWhereAppliers());
    }

    public function testCanClearWhereAppliers()
    {
        $this->model()->setWhereApplier(function () {
        });

        $this->model()->clearWhereAppliers();

        $this->assertFalse($this->model()->hasWhereAppliers());
    }

    public function testCanDetermineIfWhereExists()
    {
        $this->assertFalse($this->model()->hasWhere());

        $this->assertFalse($this->model()->select('Column')->hasWhere());
    }

    public function testCanGetEmptyWhere()
    {
        $this->assertCount(0, $this->model()->getWhere());
    }

    public function testCanClearEmptyWhere()
    {
        $this->model()->clearWhere();

        $this->assertFalse($this->model()->hasWhere());
    }

    public function testCanSetExists()
    {
        $query = $this->model()->whereExists($this->connectionMock()->select());

        $this->assertEquals('WHERE EXISTS (SELECT *)', $query->whereToString());
    }

    public function testCanSetNotExists()
    {
        $query = $this->model()->whereNotExists($this->connectionMock()->select());

        $this->assertEquals('WHERE NOT EXISTS (SELECT *)', $query->whereToString());
    }

    public function testCanSetExistsRaw()
    {
        $query = $this->model()->whereExistsRaw('SELECT 1');

        $this->assertEquals('WHERE EXISTS (SELECT 1)', $query->whereToString());
    }

    public function testCanSetNotExistsRaw()
    {
        $query = $this->model()->whereNotExistsRaw('SELECT 1');

        $this->assertEquals('WHERE NOT EXISTS (SELECT 1)', $query->whereToString());
    }

    public function testCanWhereBeNull()
    {
        $this->assertEmpty($this->model()->whereToString());
    }

    public function testCanDetermineIfExistsExists()
    {
        $this->assertFalse($this->model()->hasExists());

        $query = $this->model()->whereExists($this->connectionMock()->select());

        $this->assertTrue($query->hasExists());
    }

    public function testCanGetExists()
    {
        $query = $this->model()->whereExists($this->connectionMock()->select());

        $this->assertNotEmpty($query->getExists());
    }

    public function testCanClearExists()
    {
        $this->model()->clearExists();

        $this->assertNull($this->model()->getExists());

        $query = $this->model()->whereExists($this->connectionMock()->select());

        $query->clearExists();

        $this->assertNull($this->model()->getExists());
    }

    public function testCanSelectWhere()
    {
        $query = $this->model()->select('Column')->where('Column', 'foo');

        $this->assertEquals('SELECT `Column` FROM `Table` WHERE `Column` = ?', $query->toString());
    }

    public function testCanDetermineIfWhereClauseExists()
    {
        $this->assertFalse($this->model()->hasWhereClause());

        $this->model()->intoWhereStrategy();

        $this->assertTrue($this->model()->hasWhereClause());
    }

    protected function newQuery(): Model
    {
        return $this->model()->intoWhereStrategy();
    }

    abstract protected function model(): Model;

    /**
     * @return ConnectionStrategy|\PHPUnit_Framework_MockObject_MockObject
     */
    abstract protected function connectionMock(): \PHPUnit_Framework_MockObject_MockObject;
}
