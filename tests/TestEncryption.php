<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 4/7/16
 * Time: 8:47 PM
 */
class TestEncryption extends PHPUnit_Framework_TestCase
{
    private $key;

    public function testEnc()
    {
        $toEnc = "testing testing 123";
        $encStr = \Gigya\GigyaApiHelper::enc($toEnc, $this->key);
        $decStr = \Gigya\GigyaApiHelper::decrypt($encStr, $this->key);
        $this->assertEquals($toEnc, $decStr);
    }

    protected function setUp()
    {
        $this->key = \Gigya\GigyaApiHelper::genKeyFromString("testGenKey");
    }
    
    


}
