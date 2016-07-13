<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/10/16
 * Time: 5:17 PM
 */

use Gigya\ds\DsFactory;


class DsFactoryTests extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DsFactory
     */
    private $factory;


    public function testStringQuery()
    {
        $qry      = "SELECT * FROM test";
        $queryObj = $this->factory->createDsqFromQuery($qry);
        $this->assertEquals($qry, $queryObj->getQuery(), "Testing query strings");
    }

    public function testFromFields()
    {
        $fields   = array("test", "foo", "bar", "baz");
        $type     = "example";
        $queryObj = $this->factory->createDsqFromFields($type, $fields);
        $build = self::getMethod('buildQuery');
        $build->invoke($queryObj);
        $qry = $queryObj->getQuery();
        $expectedQry = "SELECT test, foo, bar, baz FROM example";
        $this->assertEquals($expectedQry, $qry);

    }

    public function testFromOid()
    {
        $queryObj = $this->factory->createDsqFromOid("testOid", "test");
        $this->assertEquals("testOid", $queryObj->getOid());
        $this->assertEquals("test", $queryObj->getTable());
        $this->assertEmpty($queryObj->getQuery());
    }

    public function testConstructor()
    {
        $queryObj = $this->factory->createDsqFromQuery("some query string");
        $obj  = new ReflectionObject($queryObj);
        $p = $obj->getProperty("apiKey");
        $p->setAccessible(true);
        $this->assertEquals("apiKey", $p->getValue($queryObj));
        $p = $obj->getProperty("appKey");
        $p->setAccessible(true);
        $this->assertEquals("appKey", $p->getValue($queryObj));
        $p = $obj->getProperty("appSecret");
        $p->setAccessible(true);
        $this->assertEquals("appSecret", $p->getValue($queryObj));
        $p = $obj->getProperty("dataCenter");
        $p->setAccessible(true);
        $this->assertEquals("us1.gigya.com", $p->getValue($queryObj));
        $p = $obj->getProperty("siteSecret");
        $p->setAccessible(true);
        $this->assertEquals(null, $p->getValue($queryObj));


    }


    protected function setUp()
    {
        $this->factory = new DsFactory("apiKey", "appKey", "appSecret", "us1.gigya.com");
    }

    protected static function getMethod($name)
    {
        $class  = new ReflectionClass('Gigya\ds\DsQueryObject');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

}
