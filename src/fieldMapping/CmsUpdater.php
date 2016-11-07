<?php

/**
 *
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 5/29/16
 * Time: 4:47 PM
 */

namespace Gigya\CmsStarterKit\fieldMapping;
use Gigya\CmsStarterKit\fieldMapping\CmsUpdaterException;
use Gigya\CmsStarterKit\fieldMapping\Conf;
use Gigya\CmsStarterKit\fieldMapping\ConfItem;

abstract class CmsUpdater
{

    private $gigyaUser;
    private $gigyaMapping;
    /**
     * @var bool
     */
    private $mapped = false;
    private $path;

    /**
     * CmsUpdater constructor.
     *
     * @param array $gigyaAccount
     */
    public function __construct($gigyaAccount, $mappingFilePath)
    {
        $this->gigyaUser = $gigyaAccount;
        $this->path      = (string)$mappingFilePath;
        $this->mapped    = ! empty($this->path);
    }

    /**
     * @param mixed $cmsAccount
     *
     * @throws \Exception
     */
    public function updateCmsAccount(&$cmsAccount)
    {
        $this->retrieveFieldMappings();

        if (method_exists($this, 'callCmsHook')) {
            $this->callCmsHook();
        }
        $this->setAccountValues($cmsAccount);
        $this->saveCmsAccount($cmsAccount);
    }

    /**
     * @return boolean
     */
    public function isMapped()
    {
        return $this->mapped;
    }

    abstract protected function callCmsHook();

    abstract protected function saveCmsAccount(&$cmsAccount);


    protected function retrieveFieldMappings()
    {
        $mappingJson = file_get_contents($this->path);
        if (false === $mappingJson) {
            $err     = error_get_last();
            $message = "Could not retrieve field mapping configuration file. message was:" . $err['message'];
            throw new CmsUpdaterException("$message");
        }
        $conf               = new Conf($mappingJson);
        $this->gigyaMapping = $conf->getGigyaKeyed();
    }

    /**
     * @param mixed $account
     */
    abstract protected function setAccountValues(&$account);

    public function getValueFromGigyaAccount($path)
    {
        $accArray = $this->gigyaUser;
        $keys     = explode(".", $path);
        foreach ($keys as $key) {
            if (isset($accArray[$key])) {
                $accArray = $accArray[$key];
            } else {
                $accArray = null;
            }
        }
        if (is_array($accArray) || is_object($accArray)) {
            $accArray = json_encode($accArray, JSON_UNESCAPED_SLASHES);
        }

        return $accArray;
    }

    /**
     * @param mixed    $value
     * @param ConfItem $conf
     *
     * @return mixed
     */
    protected function castValue($value, $conf)
    {
        switch ($conf->getCmsType()) {
            case "decimal":
                $value = (float)$value;
                break;
            case "int":
                $value = (int)$value;
                break;
            case "text":
                $value = (string)$value;
                break;
            case "varchar":
                $value = (string)$value;
                break;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return \Gigya\user\GigyaUser
     */
    public function getGigyaUser()
    {
        return $this->gigyaUser;
    }

    /**
     * @param array $gigyaUser
     */
    public function setGigyaUser($gigyaUser)
    {
        $this->gigyaUser = $gigyaUser;
    }

    /**
     * @return mixed
     */
    public function getGigyaMapping()
    {
        return $this->gigyaMapping;
    }

    /**
     * @param mixed $gigyaMapping
     */
    public function setGigyaMapping($gigyaMapping)
    {
        $this->gigyaMapping = $gigyaMapping;
    }


}