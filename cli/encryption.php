<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 4/7/16
 * Time: 3:49 PM
 */

include_once  __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "GigyaApiHelper.php";

if ($argv[1] == "-e") {
    $encStr = \Gigya\CmsStarterKit\GigyaApiHelper::enc($argv[2], $argv[3]);

    echo $encStr . PHP_EOL;
} elseif ($argv[1] == "-d") {
    $dec = \Gigya\CmsStarterKit\GigyaApiHelper::decrypt($argv[2], $argv[3]);

    echo $dec . PHP_EOL;
} elseif ($argv[1] == "-gen") {
    $str = isset($argv[2]) ? $argv[2] : null;
    $key = \Gigya\CmsStarterKit\GigyaApiHelper::genKeyFromString($str);

    echo $key . PHP_EOL;
}