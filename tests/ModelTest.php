<?php

namespace Greg\Orm\Tests;

use Greg\Orm\Model;
use PHPUnit\Framework\TestCase;

class MyModel extends Model
{

}

class ModelTest extends TestCase
{
    protected $model = null;

    public function setUp()
    {
        parent::setUp();

        $this->model = new MyModel();
    }
}