<?php

/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/26/16
 * Time: 9:06 AM
 */
namespace fieldMapping;

class Conf
{

    private $cmsKeyed;
    private $gigyaKeyed;
    private $mappingConf;

    public function __construct($json)
    {
        $this->mappingConf = json_decode($json, true);
    }

    protected function buildKeyedArrays($array)
    {
        $cmsKeyedArray = array();
        $gigyaKeyedArray = array();
        foreach ($array as $confItem) {
            $cmsKey = $confItem['cmsName'];
            $gigyaKey = $confItem['gigyaName'];
            $direction = $confItem['direction'];
            $conf = new Gigya_Social_Helper_FieldMapping_ConfItem($confItem);
            switch ($direction) {
                case "g2cms" :
                    $gigyaKeyedArray[$gigyaKey][] = $conf;
                    break;
                case "cms2g":
                    $cmsKeyedArray[$cmsKey][] = $conf;
                    break;
                default:
                    $gigyaKeyedArray[$gigyaKey][] = $conf;
                    $cmsKeyedArray[$cmsKey][] = $conf;
                    break;
            }
        }
        $this->gigyaKeyed = $gigyaKeyedArray;
        $this->cmsKeyed   = $cmsKeyedArray;
    }


    /**
     * @return array
     */
    public function getCmsKeyed()
    {
        if (empty($this->cmsKeyed)) {
            $this->buildKeyedArrays($this->mappingConf);
        }
        return $this->cmsKeyed;
    }

    /**
     * @return array
     */
    public function getGigyaKeyed()
    {
        if (empty($this->gigyaKeyed)) {
            $this->buildKeyedArrays($this->mappingConf);
        }
        return $this->gigyaKeyed;
    }

    /**
     * @return array
     */
    public function getMappingConf()
    {
        return $this->mappingConf;
    }



}