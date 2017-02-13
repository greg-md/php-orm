<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Mysql\Clause\MysqlConditions;
use Greg\Orm\QueryException;
use PHPUnit\Framework\TestCase;

class MysqlConditionsTest extends TestCase
{
    // Don't sure if in normal way this will be useful. If yes, we already have functionality for this.
    private $validateIfNull = false;

    protected $methods = [];

    protected $prefix = null;

    public function testCanSetColumn()
    {
        $query = $this->newCondition();

        $query->{$this->method('column')}('Foo', 'foo');

        $this->assertEquals([$this->prefix . '`Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    public function testCanTransformArrayColumnValueToScalar()
    {
        $query = $this->newCondition();

        $query->{$this->method('column')}(['Foo'], ['foo']);

        $this->assertEquals([$this->prefix . '`Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanTransformColumnInToEquals()
    {
        $query = $this->newCondition();

        $query->{$this->method('column')}('Foo', 'IN', ['foo']);

        $this->assertEquals([$this->prefix . '`Foo` = ?', ['foo']], $query->toSql());
    }

    public function testCanSetColumnWithMultipleValues()
    {
        $query = $this->newCondition();

        $query->{$this->method('column')}('Foo', ['foo1', 'foo2']);

        $this->assertEquals([$this->prefix . '`Foo` IN (?, ?)', ['foo1', 'foo2']], $query->toSql());
    }

    public function testCanThrowExceptionWhenColumnOperatorDoesNotAllowArrays()
    {
        $this->expectException(QueryException::class);

        $this->newCondition()->{$this->method('column')}('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetColumnRow()
    {
        $query = $this->newCondition();

        $query->{$this->method('column')}(['Foo', 'Bar'], ['foo', 'bar']);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (?, ?)', ['foo', 'bar']], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix . '(`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
        }
    }

    public function testCanTransformColumnRowInToEquals()
    {
        $query = $this->newCondition();

        $query->{$this->method('column')}(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (?, ?)', ['foo', 'bar']], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix . '(`Foo`, `Bar`) = (?, ?)', ['foo', 'bar']], $query->toSql());
        }
    }

    public function testCanSetColumnRowWithMultipleValues()
    {
        $query = $this->newCondition();

        $query->{$this->method('column')}(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) IN ((?, ?), (?, ?))', ['foo1', 'bar1', 'foo2', 'bar2']], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix . '(`Foo`, `Bar`) IN ((?, ?), (?, ?))', ['foo1', 'bar1', 'foo2', 'bar2']], $query->toSql());
        }
    }

    public function testCanThrowExceptionWhenColumnRowOperatorDoesNotAllowArrays()
    {
        $this->expectException(QueryException::class);

        $this->newCondition()->{$this->method('column')}(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenColumnRowInValuesAreNotTheSameLength()
    {
        $this->expectException(QueryException::class);

        $this->newCondition()->{$this->method('column')}(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenColumnRowValuesAreNotTheSameLength()
    {
        $this->expectException(QueryException::class);

        $this->newCondition()->{$this->method('column')}(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    public function testCanWorkWithNullInRow()
    {
        $query = $this->newCondition();

        $query->{$this->method('column')}(['Foo', 'Bar'], ['foo', null]);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (?, ?)', ['foo', '']], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix . '(`Foo`, `Bar`) = (?, ?)', ['foo', '']], $query->toSql());
        }
    }

    /**
     * @test
     *
     * @depends testCanSetColumn
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrColumn($query)
    {
        $query->{$this->method('orColumn')}('Bar', 'bar');

        $this->assertEquals([$this->prefix . '`Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetColumns()
    {
        $query = $this->newCondition();

        $query->{$this->method('columns')}([
            'Foo' => 'foo',
        ]);

        $this->assertEquals([$this->prefix . '`Foo` = ?', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetColumns
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrColumns($query)
    {
        $query->{$this->method('orColumns')}([
            'Bar' => 'bar',
        ]);

        $this->assertEquals([$this->prefix . '`Foo` = ? OR `Bar` = ?', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRelation()
    {
        $query = $this->newCondition();

        $query->{$this->method('relation')}('Foo', 'foo');

        $this->assertEquals($this->prefix . '`Foo` = `foo`', $query->toString());

        return $query;
    }

    public function testCanTransformArrayRelationValueToScalar()
    {
        $query = $this->newCondition();

        $query->{$this->method('relation')}(['Foo'], ['foo']);

        $this->assertEquals($this->prefix . '`Foo` = `foo`', $query->toString());
    }

    public function testCanTransformRelationInToEquals()
    {
        $query = $this->newCondition();

        $query->{$this->method('relation')}('Foo', 'IN', ['foo']);

        $this->assertEquals($this->prefix . '`Foo` = `foo`', $query->toString());
    }

    public function testCanSetRelationWithMultipleValues()
    {
        $query = $this->newCondition();

        $query->{$this->method('relation')}('Foo', ['foo1', 'foo2']);

        $this->assertEquals($this->prefix . '`Foo` IN (`foo1`, `foo2`)', $query->toString());
    }

    public function testCanThrowExceptionWhenRelationOperatorDoesNotAllowArrays()
    {
        $this->expectException(QueryException::class);

        $this->newCondition()->{$this->method('relation')}('Foo', '>', ['foo1', 'foo2']);
    }

    public function testCanSetRelationRow()
    {
        $query = $this->newCondition();

        $query->{$this->method('relation')}(['Foo', 'Bar'], ['foo', 'bar']);

        if ($this->validateIfNull) {
            $this->assertEquals($this->prefix . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (IFNULL(`foo`, ""), IFNULL(`bar`, ""))', $query->toString());
        } else {
            $this->assertEquals($this->prefix . '(`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
        }
    }

    public function testCanTransformRelationRowInToEquals()
    {
        $query = $this->newCondition();

        $query->{$this->method('relation')}(['Foo', 'Bar'], 'IN', ['foo', 'bar']);

        if ($this->validateIfNull) {
            $this->assertEquals($this->prefix . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (IFNULL(`foo`, ""), IFNULL(`bar`, ""))', $query->toString());
        } else {
            $this->assertEquals($this->prefix . '(`Foo`, `Bar`) = (`foo`, `bar`)', $query->toString());
        }
    }

    public function testCanSetRelationRowWithMultipleValues()
    {
        $query = $this->newCondition();

        $query->{$this->method('relation')}(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2', 'bar2']]);

        if ($this->validateIfNull) {
            $this->assertEquals($this->prefix . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) IN ((IFNULL(`foo1`, ""), IFNULL(`bar1`, "")), (IFNULL(`foo2`, ""), IFNULL(`bar2`, "")))', $query->toString());
        } else {
            $this->assertEquals($this->prefix . '(`Foo`, `Bar`) IN ((`foo1`, `bar1`), (`foo2`, `bar2`))', $query->toString());
        }
    }

    public function testCanThrowExceptionWhenRelationRowOperatorDoesNotAllowArrays()
    {
        $this->expectException(QueryException::class);

        $this->newCondition()->{$this->method('relation')}(['Foo', 'Bar'], '>', [['foo1', 'bar1'], ['foo2', 'bar2']]);
    }

    public function testCanThrowExceptionWhenRelationRowInValuesAreNotTheSameLength()
    {
        $this->expectException(QueryException::class);

        $this->newCondition()->{$this->method('relation')}(['Foo', 'Bar'], [['foo1', 'bar1'], ['foo2']]);
    }

    public function testCanThrowExceptionWhenRelationRowValuesAreNotTheSameLength()
    {
        $this->expectException(QueryException::class);

        $this->newCondition()->{$this->method('relation')}(['Foo', 'Bar', 'Baz'], ['foo', 'bar']);
    }

    /**
     * @test
     *
     * @depends testCanSetRelation
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrRelation($query)
    {
        $query->{$this->method('orRelation')}('Bar', 'bar');

        $this->assertEquals($this->prefix . '`Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetRelations()
    {
        $query = $this->newCondition();

        $query->{$this->method('relations')}([
            'Foo' => 'foo',
        ]);

        $this->assertEquals($this->prefix . '`Foo` = `foo`', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRelations
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrRelations($query)
    {
        $query->{$this->method('orRelations')}([
            'Bar' => 'bar',
        ]);

        $this->assertEquals($this->prefix . '`Foo` = `foo` OR `Bar` = `bar`', $query->toString());
    }

    public function testCanSetIsNull()
    {
        $query = $this->newCondition();

        $query->{$this->method('isNull')}('Foo');

        $this->assertEquals($this->prefix . '`Foo` IS NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNull
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrIsNull($query)
    {
        $query->{$this->method('orIsNull')}('Bar');

        $this->assertEquals($this->prefix . '`Foo` IS NULL OR `Bar` IS NULL', $query->toString());
    }

    public function testCanSetIsNotNull()
    {
        $query = $this->newCondition();

        $query->{$this->method('isNotNull')}('Foo');

        $this->assertEquals($this->prefix . '`Foo` IS NOT NULL', $query->toString());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetIsNotNull
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrIsNotNull($query)
    {
        $query->{$this->method('orIsNotNull')}('Bar');

        $this->assertEquals($this->prefix . '`Foo` IS NOT NULL OR `Bar` IS NOT NULL', $query->toString());
    }

    public function testCanSetBetween()
    {
        $query = $this->newCondition();

        $query->{$this->method('between')}('Foo', 1, 10);

        $this->assertEquals([$this->prefix . '`Foo` BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetBetween
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrBetween($query)
    {
        $query->{$this->method('orBetween')}('Bar', 1, 10);

        $this->assertEquals([$this->prefix . '`Foo` BETWEEN ? AND ? OR `Bar` BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetNotBetween()
    {
        $query = $this->newCondition();

        $query->{$this->method('notBetween')}('Foo', 1, 10);

        $this->assertEquals([$this->prefix . '`Foo` NOT BETWEEN ? AND ?', [1, 10]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetNotBetween
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrNotBetween($query)
    {
        $query->{$this->method('orNotBetween')}('Bar', 1, 10);

        $this->assertEquals([$this->prefix . '`Foo` NOT BETWEEN ? AND ? OR `Bar` NOT BETWEEN ? AND ?', [1, 10, 1, 10]], $query->toSql());
    }

    public function testCanSetDate()
    {
        $query = $this->newCondition();

        $query->{$this->method('date')}('Foo', date('Y-m-d'));

        $this->assertEquals([$this->prefix . 'DATE(`Foo`) = ?', [date('Y-m-d')]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDate
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrDate($query)
    {
        $query->{$this->method('orDate')}('Bar', date('Y-m-d'));

        $this->assertEquals([$this->prefix . 'DATE(`Foo`) = ? OR DATE(`Bar`) = ?', [date('Y-m-d'), date('Y-m-d')]], $query->toSql());
    }

    public function testCanSetDateRow()
    {
        $query = $this->newCondition();

        $query->{$this->method('date')}(['Foo', 'Bar'], [date('Y-m-d'), date('Y-m-d')]);

        if ($this->validateIfNull) {
            $this->assertEquals([$this->prefix . '(IFNULL(`Foo`, ""), IFNULL(`Bar`, "")) = (?, ?)', [date('Y-m-d'), date('Y-m-d')]], $query->toSql());
        } else {
            $this->assertEquals([$this->prefix . '(DATE(`Foo`), DATE(`Bar`)) = (?, ?)', [date('Y-m-d'), date('Y-m-d')]], $query->toSql());
        }
    }

    public function testCanSetTime()
    {
        $query = $this->newCondition();

        $query->{$this->method('time')}('Foo', $time = date('H:i:s'));

        $this->assertEquals([$this->prefix . 'TIME(`Foo`) = ?', [$time]], $query->toSql());

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
        /** @var ConditionsStrategy $query */
        [$query, $time] = $args;

        $query->{$this->method('orTime')}('Bar', $time);

        $this->assertEquals([$this->prefix . 'TIME(`Foo`) = ? OR TIME(`Bar`) = ?', [$time, $time]], $query->toSql());
    }

    public function testCanSetYear()
    {
        $query = $this->newCondition();

        $query->{$this->method('year')}('Foo', 2016);

        $this->assertEquals([$this->prefix . 'YEAR(`Foo`) = ?', [2016]], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetYear
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrYear($query)
    {
        $query->{$this->method('orYear')}('Bar', 2016);

        $this->assertEquals([$this->prefix . 'YEAR(`Foo`) = ? OR YEAR(`Bar`) = ?', [2016, 2016]], $query->toSql());
    }

    public function testCanSetMonth()
    {
        $query = $this->newCondition();

        $query->{$this->method('month')}('Foo', '01');

        $this->assertEquals([$this->prefix . 'MONTH(`Foo`) = ?', ['01']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetMonth
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrMonth($query)
    {
        $query->{$this->method('orMonth')}('Bar', '01');

        $this->assertEquals([$this->prefix . 'MONTH(`Foo`) = ? OR MONTH(`Bar`) = ?', ['01', '01']], $query->toSql());
    }

    public function testCanSetDay()
    {
        $query = $this->newCondition();

        $query->{$this->method('day')}('Foo', '01');

        $this->assertEquals([$this->prefix . 'DAY(`Foo`) = ?', ['01']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetDay
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrDay($query)
    {
        $query->{$this->method('orDay')}('Bar', '01');

        $this->assertEquals([$this->prefix . 'DAY(`Foo`) = ? OR DAY(`Bar`) = ?', ['01', '01']], $query->toSql());
    }

    public function testCanSetGroup()
    {
        $query = $this->newCondition();

        $query->{$this->method('group')}(function(ConditionsStrategy $query) {
            $query->column('Foo', 'foo');
        });

        $this->assertEquals([$this->prefix . '(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetGroup
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrGroup($query)
    {
        $query->{$this->method('orGroup')}(function(ConditionsStrategy $query) {
            $query->column('Bar', 'bar');
        });

        $this->assertEquals([$this->prefix . '(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetCondition()
    {
        $query = $this->newCondition();

        $query->{$this->method('condition')}((new MysqlConditions())->column('Foo', 'foo'));

        $this->assertEquals([$this->prefix . '(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetCondition
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrCondition($query)
    {
        $query->{$this->method('orCondition')}((new MysqlConditions())->column('Bar', 'bar'));

        $this->assertEquals([$this->prefix . '(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanSetRaw()
    {
        $query = $this->newCondition();

        $query->{$this->method('raw')}('`Foo` = ?', 'foo');

        $this->assertEquals([$this->prefix . '(`Foo` = ?)', ['foo']], $query->toSql());

        return $query;
    }

    /**
     * @test
     *
     * @depends testCanSetRaw
     *
     * @param ConditionsStrategy $query
     */
    public function testCanSetOrRaw($query)
    {
        $query->{$this->method('orRaw')}('`Bar` = ?', 'bar');

        $this->assertEquals([$this->prefix . '(`Foo` = ?) OR (`Bar` = ?)', ['foo', 'bar']], $query->toSql());
    }

    public function testCanDetermineIfConditionsExists()
    {
        $query = $this->newCondition();

        $this->assertFalse($query->{$this->method('has')}());

        $query->{$this->method('raw')}('`Foo` = ?', 'foo');

        $this->assertTrue($query->{$this->method('has')}());
    }

    public function testCanGet()
    {
        $query = $this->newCondition();

        $query->{$this->method('raw')}('`Foo` = ?', 'foo');

        $this->assertCount(1, $query->{$this->method('get')}());
    }

    public function testCanClear()
    {
        $query = $this->newCondition();

        $query->{$this->method('raw')}('`Foo` = ?', 'foo');

        $query->{$this->method('clear')}();

        $this->assertEquals(['', []], $query->toSql());
    }

    public function testCanTransformToString()
    {
        $query = $this->newCondition();

        $query->{$this->method('column')}('Foo', 'foo');

        $this->assertEquals($this->prefix . '`Foo` = ?', (string) $query);
    }

    public function testCanAddLogic()
    {
        $query = $this->newCondition();

        $query->{$this->method('logic')}('and', '`Foo` = ?', ['foo']);

        $this->assertEquals($this->prefix . '`Foo` = ?', (string) $query);
    }

    protected function newCondition()
    {
        return new MysqlConditions();
    }

    protected function method($name)
    {
        return $this->methods[$name] ?? $name;
    }
}