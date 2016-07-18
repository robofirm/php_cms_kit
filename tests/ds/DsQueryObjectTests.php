<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/13/16
 * Time: 1:35 PM
 */

namespace Gigya\ds;


use Gigya\GigyaApiHelper;
use Gigya\sdk\GSResponse;

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
        $expectedQry
             = 'SELECT field1, field2 FROM test WHERE field1 in("term1", "term2") AND field2 in("term3", "term4")';
        $this->assertEquals($expectedQry, $qry);

    }

    protected static function getMethod($name)
    {
        $class  = new \ReflectionClass('Gigya\ds\DsQueryObject');
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public function testAddInOr()
    {
        $this->queryObject->addIn("field1", array("term1", "term2"), "or");
        $this->queryObject->addIn("field2", array("term3", "term4"), "or");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry = $this->queryObject->getQuery();
        $expectedQry
             = 'SELECT field1, field2 FROM test WHERE field1 in("term1", "term2") OR field2 in("term3", "term4")';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testAddContains()
    {
        $this->queryObject->addContains("field1", "term1");
        $this->queryObject->addContains("field2", "term2");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry         = $this->queryObject->getQuery();
        $expectedQry = 'SELECT field1, field2 FROM test WHERE field1 contains "term1" AND field2 contains "term2"';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testAddContainsOr()
    {
        $this->queryObject->addContains("field1", "term1", "or");
        $this->queryObject->addContains("field2", "term2", "or");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry         = $this->queryObject->getQuery();
        $expectedQry = 'SELECT field1, field2 FROM test WHERE field1 contains "term1" OR field2 contains "term2"';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testAddNotContains()
    {
        $this->queryObject->addNotContains("field1", "term1");
        $this->queryObject->addNotContains("field2", "term2");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry = $this->queryObject->getQuery();
        $expectedQry
             = 'SELECT field1, field2 FROM test WHERE field1 not contains "term1" AND field2 not contains "term2"';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testAddNotContainsOr()
    {
        $this->queryObject->addNotContains("field1", "term1", "or");
        $this->queryObject->addNotContains("field2", "term2", "or");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry = $this->queryObject->getQuery();
        $expectedQry
             = 'SELECT field1, field2 FROM test WHERE field1 not contains "term1" OR field2 not contains "term2"';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testAddIsNull()
    {
        $this->queryObject->addIsNull("field1");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry         = $this->queryObject->getQuery();
        $expectedQry = 'SELECT field1, field2 FROM test WHERE field1 is null';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testAddIsNullOr()
    {
        $this->queryObject->addIsNull("field1", "or");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry         = $this->queryObject->getQuery();
        $expectedQry = 'SELECT field1, field2 FROM test WHERE field1 is null';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testAddCondition()
    {
        $this->queryObject->addWhere("field1", ">", 0, "int")->addAnd("field1", "<", 1, "int")->addAnd(
                "field1", "=", 2, "int"
            )->addOr("field2", ">=", 3, "int")->addOr("field2", "<=", 4, "int")->addAnd("field3", "!=", true, "bool");
        $build = self::getMethod('buildQuery');
        $build->invoke($this->queryObject);
        $qry = $this->queryObject->getQuery();
        $expectedQry
             = 'SELECT field1, field2 FROM test WHERE field1 > 0 AND field1 < 1 AND field1 = 2 AND field3 != true OR field2 >= 3 OR field2 <= 4';
        $this->assertEquals($expectedQry, $qry);
    }

    public function testBadValue()
    {
        $this->expectException('InvalidArgumentException');
        $this->queryObject->addWhere("filed1", "!=", "or 1=1");
    }

    public function testDsGet()
    {
        $oid  = "1234";
        $type = "test";
        $res  = new GSResponse("ds.get", json_encode(array("bla" => 1)));

        $helper = $this->getMockBuilder(GigyaApiHelper::class)->disableOriginalConstructor()->setMethods(
                array("sendApiCall")
            )->getMock();
        $helper->expects($this->once())->method("sendApiCall")->with(
                $this->equalTo("ds.get"), $this->equalTo(array("oid" => $oid, "type" => $type))
            )->willReturn($res);
        $qObj = new DsQueryObject($helper);
        $qObj->setOid($oid)->setTable($type);
        $qObj->dsGet();
    }

    public function testDsDelete()
    {
        $oid  = "1234";
        $type = "test";
        $res  = new GSResponse("ds.get", json_encode(array("bla" => 1)));

        $helper = $this->getMockBuilder(GigyaApiHelper::class)->disableOriginalConstructor()->setMethods(
            array("sendApiCall")
        )->getMock();
        $helper->expects($this->once())->method("sendApiCall")->with(
            $this->equalTo("ds.delete"), $this->equalTo(array("oid" => $oid, "type" => $type))
        )->willReturn($res);
        $qObj = new DsQueryObject($helper);
        $qObj->setOid($oid)->setTable($type);
        $qObj->dsDelete();
    }

    public function testDsSearch()
    {
        $query = "SELECT field1 FROM test";
        $res  = new GSResponse("ds.get", json_encode(array("bla" => 1)));

        $helper = $this->getMockBuilder(GigyaApiHelper::class)->disableOriginalConstructor()->setMethods(
            array("sendApiCall")
        )->getMock();
        $helper->expects($this->once())->method("sendApiCall")->with(
            $this->equalTo("ds.search"), $this->equalTo(array("query" => $query))
        )->willReturn($res);
        $qObj = new DsQueryObject($helper);
        $qObj->setTable("test")->setFields(array("field1"));
        $qObj->dsSearch();
    }


    protected function setUp()
    {
        $helper            = $this->createMock(GigyaApiHelper::class);
        $this->queryObject = new DsQueryObject($helper);
        $this->queryObject->setFields(array("field1", "field2"));
        $this->queryObject->setTable("test");
    }

}
