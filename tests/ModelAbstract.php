<?php

namespace Greg\Orm\Tests;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Driver\Mysql\MysqlDriver;
use Greg\Orm\Driver\PdoConnectorStrategy;
use Greg\Orm\Model;
use Greg\Orm\Tests\Utils\PdoMock;
use PHPUnit\Framework\TestCase;

trait BootTrait
{
    protected function bootBootTrait()
    {

    }
}

trait BootedTrait
{
    protected function bootedBootedTrait()
    {

    }
}

class MyModel extends Model
{
    use BootTrait, BootedTrait;

    protected $name = 'Table';

    protected $label = 'My Table';

    protected $nameColumn = 'Name';

    protected $unique = [
        'SystemName',
    ];

    protected $casts = [
        'Active' => 'bool',
    ];
}

abstract class ModelAbstract extends TestCase
{
    use PdoMock;

    /**
     * @var DriverStrategy
     */
    protected $driver;

    /**
     * @var MyModel
     */
    protected $model;

    public function setUp()
    {
        parent::setUp();

        $this->initPdoMock();

        $pdoMock = $this->pdoMock;

        $this->driver = new MysqlDriver(new class($pdoMock) implements PdoConnectorStrategy {
            private $mock;

            public function __construct($mock)
            {
                $this->mock = $mock;
            }

            public function connect(): \PDO
            {
                return $this->mock;
            }
        });

        $this->model = new MyModel([], $this->driver);
    }
}
