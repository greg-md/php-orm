<?php

namespace Greg\Orm;

trait ConditionsTestingTrait
{
    // Don't sure if in normal way this will be useful. If yes, we already have functionality for this.
    private $validateIfNull = false;

    public function testCanSetColumn()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('column')}('Foo', 'foo');

        $this->assertEquals([$this->prefix() . '`Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    public function testCanTransformArrayColumnValueToScalar()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('column')}(['Foo'], ['foo']);

        $this->assertEquals([$this->prefix() . '`Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanTransformColumnInToEquals()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('column')}('Foo', 'IN', ['foo']);

        $this->assertEquals([$this->prefix() . '`Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanSetColumnWithMultipleValues()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('column')}('Foo', ['foo1', 'foo2']);

        $this->assertEquals([$this->prefix() . '`Foo` IN (?, ?)', ['foo1', 'foo2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenColumnOperatorDoesNotAllowArrays()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $this->expectException(SqlException::class);

        $this->newClause()->{$this->method('column')}('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetColumnRow()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('column')}(['Foo', 'Bar'], ['foo', 'bar']);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix() . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (?, ?)', ['foo', 'bar']], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix() . '(`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
        }
    }

    public function testCanTransformColumnRowInToEquals()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('column')}(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix() . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (?, ?)', ['foo', 'bar']], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix() . '(`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
        }
    }

    public function testCanSetColumnRowWithMultipleValues()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('column')}(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix() . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) IN ((?, ?), (?, ?))', ['foo1', 'bar1', 'foo2', 'bar2']], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix() . '(`Foo`, `Bar`) IN ((?, ?), (?, ?))', ['foo1', 'bar1', 'foo2', 'bar2']], $query->toSql());
        }
    }

    public function testCanThrowExceptionWhenColumnRowOperatorDoesNotAllowArrays()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $this->expectException(SqlException::class);

        $this->newClause()->{$this->method('column')}(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenColumnRowInValuesAreNotTheSameLength()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $this->expectException(SqlException::class);

        $this->newClause()->{$this->method('column')}(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenColumnRowValuesAreNotTheSameLength()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $this->expectException(SqlException::class);

        $this->newClause()->{$this->method('column')}(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    public function testCanWorkWithNullInRow()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('column')}(['Foo', 'Bar'], ['foo', null]);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix() . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (?, ?)', ['foo', '']], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix() . '(`Foo`, `Bar`) = (?, ?)', ['foo', '']], $query->toSql());
        }
    }

    /**
     * @test
     *
     * @depends testCanSetColumn
     *
     * @param Conditions $query
     */
    public function testCanSetOrColumn($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orColumn')}('Bar', 'bar');

        $this->assertEquals([$this->prefix() . '`Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetColumns()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('columns')}([
            'Foo' => 'foo',
        ]);

        $this->assertEquals([$this->prefix() . '`Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetColumns
     *
     * @param Conditions $query
     */
    public function testCanSetOrColumns($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orColumns')}([
            'Bar' => 'bar',
        ]);

        $this->assertEquals([$this->prefix() . '`Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRelation()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('relation')}('Foo', 'foo');

        $this->assertEquals($this->prefix() . '`Foo` = `foo`', $query->toString());

        return $query;
    }

    public function testCanTransformArrayRelationValueToScalar()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('relation')}(['Foo'], ['foo']);

        $this->assertEquals($this->prefix() . '`Foo` = `foo`', $query->toString());
    }

    public function testCanTransformRelationInToEquals()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('relation')}('Foo', 'IN', ['foo']);

        $this->assertEquals($this->prefix() . '`Foo` = `foo`', $query->toString());
    }

    public function testCanSetRelationWithMultipleValues()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('relation')}('Foo', ['foo1', 'foo2']);

        $this->assertEquals($this->prefix() . '`Foo` IN (`foo1`, `foo2`)', $query->toString());
    }

    public function testCanThrowExceptionWhenRelationOperatorDoesNotAllowArrays()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $this->expectException(SqlException::class);

        $this->newClause()->{$this->method('relation')}('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetRelationRow()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('relation')}(['Foo', 'Bar'], ['foo', 'bar']);

        if ($this->validateIfNull) {
            $this->assertEquals($this->prefix() . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (IFNULL(`foo`, ""), IFNULL(`bar`, ""))', $query->toString());
        } else {
            $this->assertEquals($this->prefix() . '(`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
        }
    }

    public function testCanTransformRelationRowInToEquals()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('relation')}(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        if ($this->validateIfNull) {
            $this->assertEquals($this->prefix() . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (IFNULL(`foo`, ""), IFNULL(`bar`, ""))', $query->toString());
        } else {
            $this->assertEquals($this->prefix() . '(`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
        }
    }

    public function testCanSetRelationRowWithMultipleValues()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('relation')}(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        if ($this->validateIfNull) {
            $this->assertEquals($this->prefix() . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) IN ((IFNULL(`foo1`, ""), IFNULL(`bar1`, "")), (IFNULL(`foo2`, ""), IFNULL(`bar2`, "")))', $query->toString());
        } else {
            $this->assertEquals($this->prefix() . '(`Foo`, `Bar`) IN ((`foo1`, `bar1`), (`foo2`, `bar2`))', $query->toString());
        }
    }

    public function testCanThrowExceptionWhenRelationRowOperatorDoesNotAllowArrays()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $this->expectException(SqlException::class);

        $this->newClause()->{$this->method('relation')}(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenRelationRowInValuesAreNotTheSameLength()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $this->expectException(SqlException::class);

        $this->newClause()->{$this->method('relation')}(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenRelationRowValuesAreNotTheSameLength()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $this->expectException(SqlException::class);

        $this->newClause()->{$this->method('relation')}(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    /**
     * @test
     *
     * @depends testCanSetRelation
     *
     * @param Conditions $query
     */
    public function testCanSetOrRelation($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orRelation')}('Bar', 'bar');

        $this->assertEquals($this->prefix() . '`Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetRelations()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('relations')}([
            'Foo' => 'foo',
        ]);

        $this->assertEquals($this->prefix() . '`Foo` = `foo`', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRelations
     *
     * @param Conditions $query
     */
    public function testCanSetOrRelations($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orRelations')}([
            'Bar' => 'bar',
        ]);

        $this->assertEquals($this->prefix() . '`Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetIs()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('is')}('Foo');

        $this->assertEquals($this->prefix() . '`Foo` = 1', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIs
     *
     * @param Conditions $query
     */
    public function testCanSetOrIs($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orIs')}('Bar');

        $this->assertEquals($this->prefix() . '`Foo` = 1 OR `Bar` = 1', $query->toString());
    }

    public function testCanSetIsNot()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('isNot')}('Foo');

        $this->assertEquals($this->prefix() . '`Foo` = 0', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNot
     *
     * @param Conditions $query
     */
    public function testCanSetOrIsNot($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orIsNot')}('Bar');

        $this->assertEquals($this->prefix() . '`Foo` = 0 OR `Bar` = 0', $query->toString());
    }

    public function testCanSetIsNull()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('isNull')}('Foo');

        $this->assertEquals($this->prefix() . '`Foo` IS NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNull
     *
     * @param Conditions $query
     */
    public function testCanSetOrIsNull($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orIsNull')}('Bar');

        $this->assertEquals($this->prefix() . '`Foo` IS NULL OR `Bar` IS NULL', $query->toString());
    }

    public function testCanSetIsNotNull()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('isNotNull')}('Foo');

        $this->assertEquals($this->prefix() . '`Foo` IS NOT NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNotNull
     *
     * @param Conditions $query
     */
    public function testCanSetOrIsNotNull($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orIsNotNull')}('Bar');

        $this->assertEquals($this->prefix() . '`Foo` IS NOT NULL OR `Bar` IS NOT NULL', $query->toString());
    }

    public function testCanSetBetween()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('between')}('Foo', 1, 10);

        $this->assertEquals([$this->prefix() . '`Foo` BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetBetween
     *
     * @param Conditions $query
     */
    public function testCanSetOrBetween($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orBetween')}('Bar', 1, 10);

        $this->assertEquals([$this->prefix() . '`Foo` BETWEEN ? AND ? OR `Bar` BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetNotBetween()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('notBetween')}('Foo', 1, 10);

        $this->assertEquals([$this->prefix() . '`Foo` NOT BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetNotBetween
     *
     * @param Conditions $query
     */
    public function testCanSetOrNotBetween($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orNotBetween')}('Bar', 1, 10);

        $this->assertEquals([$this->prefix() . '`Foo` NOT BETWEEN ? AND ? OR `Bar` NOT BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetDate()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('date')}('Foo', $date = date('Y-m-d'));

        $this->assertEquals([$this->prefix() . 'DATE(`Foo`) = ?', [$date]], $query->toSql());

        return [$query, $date];
    }

    /**
     * @test
     *
     * @depends testCanSetDate
     *
     * @param array $args
     */
    public function testCanSetOrDate($args)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        /* @var Conditions $query */
        [$query, $date] = $args;

        $query->{$this->method('orDate')}('Bar', $date);

        $this->assertEquals([$this->prefix() . 'DATE(`Foo`) = ? OR DATE(`Bar`) = ?', [$date, $date]], $query->toSql());
    }

    public function testCanSetMultipleDates()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('date')}('Foo', [$date = date('Y-m-d'), $date]);

        $this->assertEquals([$this->prefix() . 'DATE(`Foo`) IN (?, ?)', [$date, $date]], $query->toSql());
    }

    public function testCanSetDateRow()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('date')}(['Foo', 'Bar'], [date('Y-m-d'), date('Y-m-d')]);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix() . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (?, ?)', [date('Y-m-d'), date('Y-m-d')]], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix() . '(DATE(`Foo`), DATE(`Bar`)) = (?, ?)', [date('Y-m-d'), date('Y-m-d')]], $query->toSql());
        }
    }

    public function testCanSetTime()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('time')}('Foo', $time = date('H:i:s'));

        $this->assertEquals([$this->prefix() . 'TIME(`Foo`) = ?', [$time]], $query->toSql());

        return [$query, $time];
    }

    /**
     * @test
     *
     * @depends testCanSetTime
     *
     * @param array $args
     */
    public function testCanSetOrTime($args)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        /* @var Conditions $query */
        [$query, $time] = $args;

        $query->{$this->method('orTime')}('Bar', $time);

        $this->assertEquals([$this->prefix() . 'TIME(`Foo`) = ? OR TIME(`Bar`) = ?', [$time, $time]], $query->toSql());
    }

    public function testCanSetYear()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('year')}('Foo', 2016);

        $this->assertEquals([$this->prefix() . 'YEAR(`Foo`) = ?', [2016]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetYear
     *
     * @param Conditions $query
     */
    public function testCanSetOrYear($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orYear')}('Bar', 2016);

        $this->assertEquals([$this->prefix() . 'YEAR(`Foo`) = ? OR YEAR(`Bar`) = ?', [2016, 2016]], $query->toSql());
    }

    public function testCanSetMonth()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('month')}('Foo', '01');

        $this->assertEquals([$this->prefix() . 'MONTH(`Foo`) = ?', ['01']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetMonth
     *
     * @param Conditions $query
     */
    public function testCanSetOrMonth($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orMonth')}('Bar', '01');

        $this->assertEquals([$this->prefix() . 'MONTH(`Foo`) = ? OR MONTH(`Bar`) = ?', ['01', '01']], $query->toSql());
    }

    public function testCanSetDay()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('day')}('Foo', '01');

        $this->assertEquals([$this->prefix() . 'DAY(`Foo`) = ?', ['01']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDay
     *
     * @param Conditions $query
     */
    public function testCanSetOrDay($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orDay')}('Bar', '01');

        $this->assertEquals([$this->prefix() . 'DAY(`Foo`) = ? OR DAY(`Bar`) = ?', ['01', '01']], $query->toSql());
    }

    public function testCanSetGroup()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('group')}(function (Conditions $query) {
            $query->column('Foo', 'foo');
        });

        $this->assertEquals([$this->prefix() . '(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetGroup
     *
     * @param Conditions $query
     */
    public function testCanSetOrGroup($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orGroup')}(function (Conditions $query) {
            $query->column('Bar', 'bar');
        });

        $this->assertEquals([$this->prefix() . '(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetConditions()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('conditions')}((new Conditions())->column('Foo', 'foo'));

        $this->assertEquals([$this->prefix() . '(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetConditions
     *
     * @param Conditions $query
     */
    public function testCanSetOrConditions($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orConditions')}((new Conditions())->column('Bar', 'bar'));

        $this->assertEquals([$this->prefix() . '(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRaw()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return null;
        }

        $query = $this->newClause();

        $query->{$this->method('raw')}('`Foo` = ?', 'foo');

        $this->assertEquals([$this->prefix() . '(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRaw
     *
     * @param Conditions $query
     */
    public function testCanSetOrRaw($query)
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query->{$this->method('orRaw')}('`Bar` = ?', 'bar');

        $this->assertEquals([$this->prefix() . '(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanDetermineIfConditionsExists()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $this->assertFalse($query->{$this->method('has')}());

        $query->{$this->method('raw')}('`Foo` = ?', 'foo');

        $this->assertTrue($query->{$this->method('has')}());
    }

    public function testCanGet()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('raw')}('`Foo` = ?', 'foo');

        $this->assertCount(1, $query->{$this->method('get')}());
    }

    public function testCanClear()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('raw')}('`Foo` = ?', 'foo');

        $query->{$this->method('clear')}();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('column')}('Foo', 'foo');

        $this->assertEquals($this->prefix() . '`Foo` = ?', (string) $query);
    }

    public function testCanAddLogic()
    {
        if ($this->disableTest(__FUNCTION__)) {
            $this->assertTrue(true);

            return;
        }

        $query = $this->newClause();

        $query->{$this->method('logic')}('and', '`Foo` = ?', ['foo']);

        $this->assertEquals($this->prefix() . '`Foo` = ?', (string) $query);
    }

    protected function disableTest($name): bool
    {
        return in_array($name, $this->getDisabledTests());
    }

    protected function method($name)
    {
        return $this->getMethods()[$name] ?? $name;
    }

    protected function prefix()
    {
        return $this->getPrefix();
    }

    abstract protected function getMethods(): array;

    abstract protected function getPrefix(): ?string;

    abstract protected function getDisabledTests(): array;

    /**
     * @return Conditions
     */
    abstract protected function newClause();
}
