<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/13/16
 * Time: 1:35 PM
 */

namespace Gigya\ds;


class TestDsQueryObject extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DsQueryObject
     */
    private $queryObject;

    public function testAddIn()
    {
        $this->queryObject->addIn("field1", array("term1", "term2"));
        $this->queryObject->addIn("field2", array("term3", "term4"));
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry = $this->queryObject->getQuery();
        $expectedQry = 'SELECT field1, field2 FROM test WHERE field1 in("term1", "term2") AND field2 in("term3", "term4")';
        $this->assertEquals($expectedQry, $qry);

    }

    public function testAddInOr()
    {
        $this->queryObject->addIn("field1", array("term1", "term2"), "or");
        $this->queryObject->addIn("field2", array("term3", "term4"), "or");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry = $this->queryObject->getQuery();
        $expectedQry = 'SELECT field1, field2 FROM test WHERE field1 in("term1", "term2") OR field2 in("term3", "term4")';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testAddContains()
    {
        $this->queryObject->addContains("field1", "term1");
        $this->queryObject->addContains("field2", "term2");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry = $this->queryObject->getQuery();
        $expectedQry = 'SELECT field1, field2 FROM test WHERE field1 contains "term1" AND field2 contains "term2"';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testAddContainsOr()
    {
        $this->queryObject->addContains("field1", "term1", "or");
        $this->queryObject->addContains("field2", "term2", "or");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry = $this->queryObject->getQuery();
        $expectedQry = 'SELECT field1, field2 FROM test WHERE field1 contains "term1" OR field2 contains "term2"';
        $this->assertEquals($expectedQry, $qry);
    }

    protected function setUp()
    {
        $this->queryObject = new DsQueryObject("apiKey", "appKey", "appSecret", "us1.gigya.com");
        $this->queryObject->setFields(array("field1", "field2"));
        $this->queryObject->setTable("test");
    }

    protected static function getMethod($name)
    {
        $class  = new \ReflectionClass('Gigya\ds\DsQueryObject');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

}
