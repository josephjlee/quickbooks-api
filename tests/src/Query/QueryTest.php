<?php
namespace Wheniwork\Quickbooks\Test\Query;

use PHPUnit_Framework_TestCase;
use RuntimeException;
use Wheniwork\Quickbooks\Query\Query;

class QueryTest extends PHPUnit_Framework_TestCase
{

    public function testBasicQuery()
    {
        $query = Query::from('test')
            ->select('*')
            ->build();

        $this->assertEquals("SELECT * FROM test", $query);
    }

    public function testComplexQuery()
    {
        $query = Query::from('test')
            ->select('*')
            ->where('test', '=', true)
            ->where('cost', '>', 5)
            ->where('total', '=', '50.00')
            ->order('cost', 'desc')
            ->order('total', 'asc')
            ->offset(5)
            ->take(100)
            ->build();

        $this->assertEquals("SELECT * FROM test WHERE test = true AND cost > 5 AND total = '50.00' ORDERBY cost desc, total asc STARTPOSITION 5 MAXRESULTS 100", $query);
    }

    public function testMissingSelect()
    {
        $query = Query::from('test')->build();

        $this->assertEquals("SELECT * FROM test", $query);
    }

    public function testSingleWhere()
    {
        $query = Query::from('test')
            ->select('*')
            ->where('total', '=', '1000.00')
            ->build();

        $this->assertEquals("SELECT * FROM test WHERE total = '1000.00'", $query);
    }

    public function testMultipleWhere()
    {
        $query = Query::from('test')
            ->select('*')
            ->where('cost', '=', '1000.00')
            ->where('total', '>', 5)
            ->build();

        $this->assertEquals("SELECT * FROM test WHERE cost = '1000.00' AND total > 5", $query);
    }

    public function testSingleOrder()
    {
        $query = Query::from('test')
            ->select('*')
            ->order('total')
            ->build();

        $this->assertEquals("SELECT * FROM test ORDERBY total", $query);

        $query = Query::from('test')
            ->select('*')
            ->order('total', 'DESC')
            ->build();

        $this->assertEquals("SELECT * FROM test ORDERBY total DESC", $query);
    }

    public function testMultipleOrder()
    {
        $query = Query::from('test')
            ->select('*')
            ->order('cost')
            ->order('total', 'DESC')
            ->build();

        $this->assertEquals("SELECT * FROM test ORDERBY cost, total DESC", $query);
    }

    public function testOffset()
    {
        $query = Query::from('test')
            ->offset(5)
            ->build();

        $this->assertEquals("SELECT * FROM test STARTPOSITION 5", $query);
    }

    public function testTake()
    {
        $query = Query::from('test')
            ->take(7)
            ->build();

        $this->assertEquals("SELECT * FROM test MAXRESULTS 7", $query);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testOffsetMinimumException()
    {
        Query::from('test')->offset(-5);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testTakeMaximumException()
    {
        Query::from('test')->take(123124124);
    }
}