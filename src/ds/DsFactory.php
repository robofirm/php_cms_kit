<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/10/16
 * Time: 2:49 PM
 */

namespace Gigya\ds;


class DsFactory
{
    private $apiHelper;


    /**
     * DsFactory constructor.
     *
     * @param $helper
     *
     */
    public function __construct($helper)
    {
        $this->apiHelper;
    }

    public function createDsqFromQuery($query)
    {
        $dsQueryObj = new DsQueryObject($this->apiHelper);
        $dsQueryObj->setQuery($query);
        return $dsQueryObj;
    }

    public function createDsqFromFields($type, $fields)
    {
        $dsQueryObj = new DsQueryObject($this->apiHelper);
        $dsQueryObj->setFields($fields);
        $dsQueryObj->setTable($type);
        return $dsQueryObj;

    }

    public function createDsqFromOid($oid, $type)
    {
        $dsQueryObj = new DsQueryObject($this->apiHelper);
        $dsQueryObj->setOid($oid);
        $dsQueryObj->setTable($type);
        return $dsQueryObj;
    }


}