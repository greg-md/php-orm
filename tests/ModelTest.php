<?php

namespace Greg\Orm\Tests;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Driver\StatementStrategy;
use Greg\Orm\Model;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

class CustomModel extends Model
{
}

class ModelTest extends ModelAbstract
{
    public function testCanManageQuery()
    {
        $this->assertFalse($this->model->hasQuery());

        $this->assertEmpty($this->model->getQuery());

        $this->model->selectQuery();

        $this->assertTrue($this->model->hasQuery());

        $this->assertInstanceOf(QueryStrategy::class, $this->model->getQuery());

        $this->assertInstanceOf(QueryStrategy::class, $this->model->query());

        $this->model->clearQuery();

        $this->assertEmpty($this->model->getQuery());

        $this->expectException(QueryException::class);

        $this->model->query();
    }

    public function testCanUseWhen()
    {
        $this->model->selectQuery();

        $callable = function (Model $model) {
            $model->where('Column', 'foo');
        };

        $this->model->when(false, $callable);

        $this->assertEquals('SELECT * FROM `Table`', $this->model->toString());

        $this->model->when(true, $callable);

        $this->assertEquals('SELECT * FROM `Table` WHERE `Column` = ?', $this->model->toString());
    }

    public function testCanPrepare()
    {
        $this->mockStatements();

        $this->model->selectQuery()->where('Column', 'foo');

        $this->assertInstanceOf(StatementStrategy::class, $this->model->prepare());
    }

    public function testCanExecute()
    {
        $this->mockStatements();

        $this->model->selectQuery();

        $this->assertInstanceOf(StatementStrategy::class, $this->model->execute());
    }

    public function testCanGetClausesSql()
    {
        $query = $this->model
            ->from('Table1')
            ->inner('Table2')
            ->where('Column', 'foo')
            ->having('Column', 'foo')
            ->orderBy('Column')
            ->groupBy('Column')
            ->limit(10)
            ->offset(10);

        $sql = 'FROM `Table1` INNER JOIN `Table2` WHERE `Column` = ?'
                . ' GROUP BY `Column` HAVING `Column` = ? ORDER BY `Column` LIMIT 10 OFFSET 10';

        $this->assertEquals($sql, $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->model->where('Column', 'foo');

        $this->assertEquals('WHERE `Column` = ?', (string) $query);
    }

    public function testCanGetClause()
    {
        $query = $this->model->where('Column', 'foo');

        $this->assertInstanceOf(WhereClause::class, $query->clause('WHERE'));
    }

    public function testCanThrowExceptionIfClauseNotExists()
    {
        $this->expectException(QueryException::class);

        $this->model->clause('FROM');
    }

    public function testCanDetermineIfClauseExists()
    {
        $this->assertFalse($this->model->hasClause('WHERE'));

        $query = $this->model->where('Column', 'foo');

        $this->assertTrue($query->hasClause('WHERE'));
    }

    public function testCanClearClause()
    {
        $query = $this->model->where('Column', 'foo');

        $query->clearClause('WHERE');

        $this->assertFalse($query->hasClause('WHERE'));
    }

    public function testCanThrowExceptionIfNameNotDefined()
    {
        $model = new CustomModel();

        $this->expectException(\Exception::class);

        $model->name();
    }

    public function testCanThrowExceptionWhenDriverNotDefined()
    {
        $model = new CustomModel();

        $this->expectException(\Exception::class);

        $model->driver();
    }

    public function testCanGetLabel()
    {
        $this->assertEquals('My Table', $this->model->label());
    }

    public function testCanGetFillable()
    {
        $this->assertEquals('*', $this->model->fillable());
    }

    public function testCanGetPrimary()
    {
        $this->assertEquals(['Id'], $this->model->primary());
    }

    public function testCanGetUnique()
    {
        $this->assertEquals([['SystemName']], $this->model->unique());
    }

    public function testCanGetFirstUnique()
    {
        $this->assertEquals(['Id'], $this->model->firstUnique());
    }

    public function testCanGetAutoIncrement()
    {
        $this->assertEquals('Id', $this->model->autoIncrement());
    }

    public function testCanGetNameColumn()
    {
        $this->assertEquals('Name', $this->model->nameColumn());
    }

    public function testCanGetCasts()
    {
        $this->assertEquals(['Active' => 'bool'], $this->model->casts());
    }

    public function testCanGetCast()
    {
        $this->assertEquals('bool', $this->model->cast('Active'));
    }

    public function testCanSelectPairs()
    {
        $query = $this->model->selectPairs();

        $this->assertEquals('SELECT `Id` AS `key`, `Name` AS `value` FROM `Table`', $query->toString());
    }

    public function testCanThrowExceptionIfCanNotSelectPairs()
    {
        $this->expectException(\Exception::class);

        (new CustomModel())->selectPairs();
    }
}
