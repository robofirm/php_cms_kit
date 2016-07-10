<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/10/16
 * Time: 2:49 PM
 */

namespace ds;


class DsFactory
{
    private $apiKey;
    private $appKey;
    private $appSecret;
    private $siteSecret;

    /**
     * DsFactory constructor.
     *
     * @param $apiKey
     * @param $appKey
     * @param $appSecret
     * @param $siteSecret
     */
    public function __construct($apiKey, $appKey, $appSecret, $siteSecret = null)
    {
        $this->apiKey     = $apiKey;
        $this->appKey     = $appKey;
        $this->appSecret  = $appSecret;
        $this->siteSecret = $siteSecret;
    }

    public function createDsqFromQuery($query)
    {
        $dsQueryObj = new DsQueryObject($this->apiKey, $this->appKey, $this->appSecret, $this->siteSecret);
        $dsQueryObj->setQuery($query);
        return $dsQueryObj;
    }

    public function createDsqFromFields($type, $fields)
    {
        $dsQueryObj = new DsQueryObject($this->apiKey, $this->appKey, $this->appSecret, $this->siteSecret);
        $dsQueryObj->setFields($fields);
        $dsQueryObj->setTable($type);
        return $dsQueryObj;

    }

    public function createDsqFromOid($oid, $type)
    {
        $dsQueryObj = new DsQueryObject($this->apiKey, $this->appKey, $this->appSecret, $this->siteSecret);
        $dsQueryObj->setOid($oid);
        $dsQueryObj->setTable($type);
        return $dsQueryObj;
    }


}