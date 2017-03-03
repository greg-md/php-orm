<?php

namespace Greg\Orm\Model;

use Greg\Orm\ModelTestingAbstract;
use Greg\Orm\SqlException;

class ModelUpdateQueryTest extends ModelTestingAbstract
{
    public function testCanSetColumn()
    {
        $query = $this->model->updateTable('Table2')->setValue('Column', 'foo');

        $this->assertEquals('UPDATE `Table`, `Table2` SET `Column` = ?', $query->toString());
    }

    public function testCanDetermineIfTablesExists()
    {
        $this->assertFalse($this->model->hasUpdateTables());

        $this->model->setQuery($this->model->newUpdateQuery());

        $this->assertTrue($this->model->hasUpdateTables());

        $this->model->updateTable('Table');

        $this->assertCount(2, $this->model->getUpdateTables());
    }

    public function testCanGetTables()
    {
        $this->assertCount(0, $this->model->getUpdateTables());

        $this->model->setQuery($this->model->newUpdateQuery());

        $this->model->updateTable('Table');

        $this->assertCount(2, $this->model->getUpdateTables());
    }

    public function testCanClearTables()
    {
        $this->model->setQuery($this->model->newUpdateQuery());

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

        $this->model->setQuery($this->model->newUpdateQuery());

        $this->assertFalse($this->model->hasSetValues());

        $this->model->setValue('Column', 'foo');

        $this->assertTrue($this->model->hasSetValues());
    }

    public function testCanGetSet()
    {
        $this->assertCount(0, $this->model->getSetValues());

        $this->model->setQuery($this->model->newUpdateQuery());

        $this->model->setValue('Column', 'foo');

        $this->assertCount(1, $this->model->getSetValues());
    }

    public function testCanClearSet()
    {
        $this->model->setQuery($this->model->newUpdateQuery());

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
        $this->expectException(SqlException::class);

        $this->model->select('Foo', 'foo')->setValue('Column', 'foo');
    }
}
