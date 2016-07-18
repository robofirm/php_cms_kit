<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 4/6/16
 * Time: 4:38 PM
 */

//include_once "../vendor/autoload.php";

class GigyaUserTest extends PHPUnit_Framework_TestCase
{


    public function testCreateGigyaUserFromJson()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "resources/account.json");
        $gigyaUser = Gigya\user\GigyaUserFactory::createGigyaUserFromJson($json);
        $this->assertEquals("9b792cd0d4df4c9d938402ea793f33e6", $gigyaUser->getUID, "checking UID");
        $this->assertTrue($gigyaUser->getIsActive(), "Checking active");
    }

    public function testCreateGigyaUserFromArray()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "resources/account.json");
        $gso = new \Gigya\sdk\GSObject($json);
        $gigyaUser = \Gigya\user\GigyaUserFactory::createGigyaUserFromArray($gso->serialize());
        $this->assertEquals("9b792cd0d4df4c9d938402ea793f33e6", $gigyaUser->getUID, "checking UID");
        $this->assertTrue($gigyaUser->getIsActive(), "Checking active");
        
    }

    public function testGetNestedValue()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "resources/account.json");
        $gigyaUser = Gigya\user\GigyaUserFactory::createGigyaUserFromJson($json);
        $this->assertEquals('ibm', $gigyaUser->getNestedValue('profile.work.company'), "Testing get from profile");
        $this->assertEquals('true', $gigyaUser->getNestedValue('data.TSN.myTsnEmailEnabled'), "Test getting from data");
        $this->assertEquals(39.97569274902344, $gigyaUser->getNestedValue('lastLoginLocation.coordinates.lat'), "Test getting from account");

    }


}
