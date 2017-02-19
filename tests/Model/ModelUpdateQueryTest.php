<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\QueryException;
use Greg\Orm\Tests\ModelAbstract;

class ModelUpdateQueryTest extends ModelAbstract
{
    public function testCanSetColumn()
    {
        $query = $this->model->updateTable('Table2')->setValue('Column', 'foo');

        $this->assertEquals('UPDATE `Table`, `Table2` SET `Column` = ?', $query->toString());
    }

    public function testCanDetermineIfTablesExists()
    {
        $this->assertFalse($this->model->hasUpdateTables());

        $this->model->updateQuery();

        $this->assertTrue($this->model->hasUpdateTables());

        $this->model->updateTable('Table');

        $this->assertCount(2, $this->model->getUpdateTables());
    }

    public function testCanGetTables()
    {
        $this->assertCount(0, $this->model->getUpdateTables());

        $this->model->updateQuery();

        $this->model->updateTable('Table');

        $this->assertCount(2, $this->model->getUpdateTables());
    }

    public function testCanClearTables()
    {
        $this->model->updateQuery();

        $this->model->updateTable('Table');

        $this->model->clearUpdateTables();

        $this->assertFalse($this->model->hasUpdateTables());
    }

    public function testCanSetColumns()
    {
        $query = $this->model->setValues(['Column' => 'foo']);

        $this->assertEquals('UPDATE `Table` SET `Column` = ?', $query->toString());
    }

    public function testCanSetRawColumn()
    {
        $query = $this->model->setRawValue('`Column` = NOW()');

        $this->assertEquals('UPDATE `Table` SET `Column` = NOW()', $query->toString());
    }

    public function testCanIncrement()
    {
        $query = $this->model->increment('Column');

        $this->assertEquals('UPDATE `Table` SET `Column` = `Column` + ?', $query->toString());
    }

    public function testCanDecrement()
    {
        $query = $this->model->decrement('Column');

        $this->assertEquals('UPDATE `Table` SET `Column` = `Column` - ?', $query->toString());
    }

    public function testCanDetermineIfSetExists()
    {
        $this->assertFalse($this->model->hasSetValues());

        $this->model->updateQuery();

        $this->assertFalse($this->model->hasSetValues());

        $this->model->setValue('Column', 'foo');

        $this->assertTrue($this->model->hasSetValues());
    }

    public function testCanGetSet()
    {
        $this->assertCount(0, $this->model->getSetValues());

        $this->model->updateQuery();

        $this->model->setValue('Column', 'foo');

        $this->assertCount(1, $this->model->getSetValues());
    }

    public function testCanClearSet()
    {
        $this->model->updateQuery();

        $this->model->setValue('Column', 'foo');

        $this->model->clearSetValues();

        $this->assertFalse($this->model->hasSetValues());
    }

    public function testCanCombineQuery()
    {
        $query = $this->model
            ->inner('Table2')
            ->where('Column', 'foo')
            ->orderBy('Column')
            ->limit(1)
            ->setValue('Column', 'foo');

        $this->assertEquals('UPDATE `Table` INNER JOIN `Table2` SET `Column` = ? WHERE `Column` = ? ORDER BY `Column` LIMIT 1', $query->toString());
    }

    public function testCanThrowExceptionIfQueryNotSupported()
    {
        $this->expectException(QueryException::class);

        $this->model->select('Foo', 'foo')->setValue('Column', 'foo');
    }
}
