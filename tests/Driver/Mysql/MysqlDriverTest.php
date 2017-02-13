<?php

namespace Greg\Orm\Tests\Driver\Mysql;

use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Driver\Mysql\MysqlDriver;
use Greg\Orm\Query\DeleteQueryStrategy;
use Greg\Orm\Query\InsertQueryStrategy;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Query\UpdateQueryStrategy;
use PHPUnit\Framework\TestCase;

class MysqlDriverTest extends TestCase
{
    /**
     * @var MysqlDriver
     */
    private $db;

    public function setUp()
    {
        parent::setUp();

        $this->db = new MysqlDriver('mysql:host=127.0.0.1;dbname=testing', 'homeserver', 'secret');
    }

    public function testCanGetDsn()
    {
        $this->assertEquals('mysql:host=127.0.0.1;dbname=testing', $this->db->dsn());

        $this->assertEquals('127.0.0.1', $this->db->dsn('host'));
    }

    public function testCanQuoteLike()
    {
        $this->assertEquals('My \\% percentage', $this->db->quoteLike('My % percentage'));
    }

    public function testCanConcat()
    {
        $this->assertEquals('concat_ws("", foo, bar)', $this->db->concat(['foo', 'bar']));
    }

    /**
     * @test
     *
     * @dataProvider queries
     *
     * @param $name
     * @param $class
     */
    public function testCanInstanceAQuery($name, $class)
    {
        $this->assertInstanceOf($class, $this->db->{$name}());
    }

    public function queries()
    {
        yield ['select', SelectQueryStrategy::class];
        yield ['insert', InsertQueryStrategy::class];
        yield ['delete', DeleteQueryStrategy::class];
        yield ['update', UpdateQueryStrategy::class];
        yield ['from', FromClauseStrategy::class];
        yield ['join', JoinClauseStrategy::class];
        yield ['where', WhereClauseStrategy::class];
        yield ['having', HavingClauseStrategy::class];
        yield ['orderBy', OrderByClauseStrategy::class];
        yield ['groupBy', GroupByClauseStrategy::class];
        yield ['limit', LimitClauseStrategy::class];
    }

//    public function testCanGetConnection()
//    {
//        $this->assertInstanceOf(\PDO::class, $this->db->connection());
//    }
//
//    public function testCanExecInitEvent()
//    {
//        $used = false;
//
//        $this->db->onInit(function() use (&$used) {
//            $used = true;
//        });
//
//        $this->db->connect();
//
//        $this->assertTrue($used);
//    }
//
//    public function testCanPrepareAStatement()
//    {
//        $stmt = $this->db->prepare('SELECT 1');
//
//        $this->assertInstanceOf(StatementStrategy::class, $stmt);
//    }
//
//    public function testCanExecInTransaction()
//    {
//        $inTransaction = false;
//
//        $this->db->transaction(function(MysqlDriver $db) use (&$inTransaction) {
//            $inTransaction = $db->inTransaction();
//        });
//
//        $this->assertTrue($inTransaction);
//    }
}