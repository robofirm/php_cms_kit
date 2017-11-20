<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 4/5/16
 * Time: 5:06 PM
 */

namespace Gigya\CmsStarterKit;


use Gigya\CmsStarterKit\Sdk\GSApiException;
use Gigya\CmsStarterKit\Sdk\GSFactory;
use Gigya\CmsStarterKit\Sdk\GSObject;
use Gigya\CmsStarterKit\Sdk\SigUtils;
use Gigya\CmsStarterKit\User\GigyaUserFactory;

class GigyaApiHelper
{

    private $key;
    private $secret;
    private $apiKey;
    private $dataCenter;
    private $token;
    private $defConfigFilePath;

    /**
     * GigyaApiHelper constructor.
     *
     * @param string $key    gigya app/user key
     * @param string $secret gigya app/user secret
     */
    public function __construct($apiKey, $key, $secret, $dataCenter)
    {
        $this->defConfigFilePath = ".." . DIRECTORY_SEPARATOR . "configuration/DefaultConfiguration.json";
        $defaultConf = @file_get_contents($this->defConfigFilePath);
        if (!$defaultConf) {
            $confArray = array();
        } else {
            $confArray = json_decode(file_get_contents($this->defConfigFilePath));
        }
        $this->key    = !empty($key) ? $key : $confArray['appKey'];
        $this->secret = !empty($secret) ? self::decrypt($secret) : self::decrypt($confArray['appSecret']);
        $this->apiKey = !empty($apiKey) ? $apiKey : $confArray['apiKey'];
        $this->dataCenter = !empty($dataCenter) ? $dataCenter : $confArray['dataCenter'];

    }

    public function sendApiCall($method, $params)
    {
        $req = GSFactory::createGSRequestAppKey($this->apiKey, $this->key, $this->secret, $method,
          GSFactory::createGSObjectFromArray($params), $this->dataCenter);

        return $req->send();
    }

    /**
     * Validate and get gigya user
     * @param $uid
     * @param $uidSignature
     * @param $signatureTimestamp
     * @param null $include
     * @param null $extraProfileFields
     * @param array $org_params
     *
     * @return bool|user\GigyaUser
     */
    public function validateUid($uid, $uidSignature, $signatureTimestamp, $include = null, $extraProfileFields = null, $org_params = array())
    {
        $params = $org_params;
        $params['UID'] = $uid;
        $params['UIDSignature'] = $uidSignature;
        $params['signatureTimestamp'] = $signatureTimestamp;
        $res          = $this->sendApiCall("socialize.exchangeUIDSignature", $params);
        $sig          = $res->getData()->getString("UIDSignature", null);
        $sigTimestamp = $res->getData()->getString("signatureTimestamp", null);
        if (null !== $sig && null !== $sigTimestamp) {
            if (SigUtils::validateUserSignature($uid, $sigTimestamp, $this->secret, $sig)) {
                $user = $this->fetchGigyaAccount($uid, $include, $extraProfileFields, $org_params);
                return $user;
            }
        }

        return false;
    }

    public function fetchGigyaAccount($uid, $include = null, $extraProfileFields = null, $params = array())
    {
        if (null == $include) {
            $include
              = "identities-active,identities-all,loginIDs,emails,profile,data,password,lastLoginLocation,rba,
            regSource,irank";
        }
        if (null == $extraProfileFields) {
            $extraProfileFields
              = "languages,address,phones,education,honors,publications,patents,certifications,
            professionalHeadline,bio,industry,specialties,work,skills,religion,politicalView,interestedIn,
            relationshipStatus,hometown,favorites,followersCount,followingCount,username,locale,verified,timezone,likes,
            samlData";
        }
        $params['UID'] = $uid;
        $params['include'] = $include;
        $params['extraProfileFields'] = $extraProfileFields;

        $res          = $this->sendApiCall("accounts.getAccountInfo", $params);
        $dataArray    = $res->getData()->serialize();
        $profileArray = $dataArray['profile'];
        $gigyaUser    = GigyaUserFactory::createGigyaUserFromArray($dataArray);
        $gigyaProfile = GigyaUserFactory::createGigyaProfileFromArray($profileArray);
        $gigyaUser->setProfile($gigyaProfile);

        return $gigyaUser;
    }

    /**
     * @param string $uid
     * @param array $profile
     * @param array $data
     *
     * @throws GSApiException
     */
    public function updateGigyaAccount($uid, $profile = array(), $data = array())
    {
        if (empty($uid)) {
            throw new \InvalidArgumentException("uid can not be empty");
        }
        $paramsArray['UID'] = $uid;
        if (!empty($profile) && count($profile) > 0) {
            $paramsArray['profile'] = $profile;
        }
        if (!empty($data) && count($data) > 0) {
            $paramsArray['data'] = $data;
        }
        $this->sendApiCall("accounts.setAccountInfo", $paramsArray);
    }

    public function getSiteSchema()
    {
        $params = GSFactory::createGSObjectFromArray(array("apiKey" => $this->apiKey));
        $res    = $this->sendApiCall("accounts.getSchema", $params);
        //TODO: implement
    }

    public function isRaasEnabled($apiKey = null)
    {
        if (null === $apiKey) {
            $apiKey = $this->apiKey;
        }
        $params = GSFactory::createGSObjectFromArray(array("apiKey" => $apiKey));
        try {
            $this->sendApiCall("accounts.getGlobalConfig", $params);
            return true;
        } catch (GSApiException $e) {
            if ($e->getErrorCode() == 403036) {
                return false;
            }
            throwException($e);
        }
        return false;
    }

    public function queryDs($uid, $table, $fields)
    {


    }

    public function userObjFromArray($user_arr)
    {
        $obj = GigyaUserFactory::createGigyaUserFromArray($user_arr);
        return $obj;
    }

    // static

    static public function decrypt($str, $key = null)
    {
        if (null == $key) {
            $key = getenv("KEK");
        }
        if (!empty($key)) {
            $iv_size       = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $strDec        = base64_decode($str);
            $iv            = substr($strDec, 0, $iv_size);
            $text_only     = substr($strDec, $iv_size);
            $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
              $text_only, MCRYPT_MODE_CBC, $iv);

            return $plaintext_dec;
        }
        return $str;
    }

    static public function enc($str, $key = null)
    {
        if (null == $key) {
            $key = getenv("KEK");
        }
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypt   = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC, $iv);

        return trim(base64_encode($iv . $crypt));
    }

    static public function genKeyFromString($str = null) {
        if (null == $str) {
            $str = openssl_random_pseudo_bytes(32);
        }
        $salt = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
        $key = hash_pbkdf2("sha256", $str, $salt, 1000, 32);
        return $key;
    }

}
