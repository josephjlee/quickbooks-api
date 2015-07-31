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

    public function testSingleWhereQuery()
    {
        $query = Query::from('test')
            ->select('*')
            ->where('total', '=', '1000.00')
            ->build();

        $this->assertEquals("SELECT * FROM test WHERE total = '1000.00'", $query);
    }

    public function testMultipleWhereQuery()
    {
        $query = Query::from('test')
            ->select('*')
            ->where('cost', '=', '1000.00')
            ->where('total', '>', 5)
            ->build();

        $this->assertEquals("SELECT * FROM test WHERE cost = '1000.00' AND total > '5'", $query);
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