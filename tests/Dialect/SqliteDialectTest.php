<?php

namespace Greg\Orm\Dialect;

use Greg\Orm\Model;
use Greg\Orm\Query\SelectQuery;
use PHPUnit\Framework\TestCase;

class SqliteDialectTest extends TestCase
{
    /**
     * @var SqliteDialect
     */
    private $dialect;

    public function setUp()
    {
        parent::setUp();

        $this->dialect = new SqliteDialect();
    }

    public function testCanQuoteTable()
    {
        $this->assertEquals('`Table`', $this->dialect->quoteTable('Table'));

        $this->assertEquals('(SELECT 1)', $this->dialect->quoteTable('(SELECT 1)'));
    }

    public function testCanQuoteName()
    {
        $this->assertEquals('`Table`.`Column`', $this->dialect->quoteTable('Table.Column'));
    }

    public function testCanQuoteSql()
    {
        $this->assertEquals('select foo, `bar`', $this->dialect->quoteSql('select foo, !bar'));
    }

    public function testCanParseTable()
    {
        $this->assertEquals([null, 'foo'], $this->dialect->parseTable('foo'));

        $this->assertEquals(['bar', 'foo'], $this->dialect->parseTable('foo as bar'));

        $this->assertEquals(['bar', 'foo'], $this->dialect->parseTable(['bar' => 'foo']));

        $this->assertEquals(['bar', 'foo'], $this->dialect->parseTable(new class() extends Model {
            protected $name = 'foo';

            protected $alias = 'bar';
        }));

        $query = new SelectQuery($this->dialect);

        $this->assertEquals([null, $query], $this->dialect->parseTable($query));
    }

    public function testCanParseName()
    {
        $this->assertEquals([null, 'foo'], $this->dialect->parseName('foo'));

        $this->assertEquals(['bar', 'foo'], $this->dialect->parseName('foo as bar'));
    }

    public function testCanConcat()
    {
        $this->assertEquals('foo', $this->dialect->concat(['foo']));

        $this->assertEquals('foo + bar', $this->dialect->concat(['foo', 'bar']));

        $this->assertEquals('foo + "!" + bar', $this->dialect->concat(['foo', 'bar'], '"!"'));
    }

    public function testCanAddLimitToSql()
    {
        $this->assertEquals('SELECT 1 LIMIT 10', $this->dialect->addLimitToSql('SELECT 1', 10));
    }

    public function testCanAddOffsetToSql()
    {
        $this->assertEquals('SELECT 1 OFFSET 10', $this->dialect->addOffsetToSql('SELECT 1', 10));
    }

    public function testCanLockForUpdate()
    {
        $this->assertEquals('SELECT 1', $this->dialect->lockForUpdateSql('SELECT 1'));
    }

    public function testCanLockInShareMode()
    {
        $this->assertEquals('SELECT 1', $this->dialect->lockInShareMode('SELECT 1'));
    }
}