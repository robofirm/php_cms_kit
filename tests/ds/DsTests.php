<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/10/16
 * Time: 5:17 PM
 */

namespace ds;


class DsTests extends \PHPUnit_Framework_TestCase
{
    private $factory;
    protected function setUp()
    {
        $this->factory = new DsFactory("apiKey", "appKey", "appSecret");
    }

    

}
