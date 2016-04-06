<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 4/5/16
 * Time: 5:06 PM
 */

namespace gigya;


use gigya\sdk\GSFactory;
use gigya\sdk\SigUtils;

class GigyaApiHelper {

	private $key;
	private $secret;
    private $apiKey;
	private $token;

	/**
	 * GigyaApiHelper constructor.
	 *
	 * @param string $key gigya app/user key
	 * @param string $secret gigya app/user secret
	 */
	public function __construct( $key, $secret, $apiKey ) {
		$this->key    = $key;
		$this->secret = $secret;
        $this->apiKey = $apiKey;
	}

    public function sendApiCall($method, $params) {
        $req = GSFactory::createGSRequestAppKey($this->apiKey, $this->key, $this->secret, $method,
            GSFactory::createGSObjectFromArray($params));
        return $req->send();
    }

	public function validateUid( $uid, $uidSignature, $signatureTimestamp ) {
		$params = array(
            "UID" => $uid,
            "UIDSignature" => $uidSignature,
            "signatureTimestamp" => $signatureTimestamp
        );
        $res = $this->sendApiCall("socialize.exchangeUIDSignature", $params);
        $sig = $res->getData()->getString("UIDSignature", null);
        $sigTimestamp = $res->getData()->getString("signatureTimestamp", null);
        if (null !== $sig && null !== $sigTimestamp) {
            if (SigUtils::validateUserSignature($uid, $sigTimestamp, $this->secret, $sig)) {
                $user = $this->fetchGigyaAccount($uid);
                return $user;
            }
        }
        return false;
	}

    public function fetchGigyaAccount($uid, $include = null, $extraProfileFields = null) {
        if (null == $include) {
            $include = "identities-active,identities-all,loginIDs,emails,profile,data,password,lastLoginLocation,rba,
            regSource,irank";
        }
        if (null == $extraProfileFields) {
            $extraProfileFields = "languages,address,phones,education,honors,publications,patents,certifications,
            professionalHeadline,bio,industry,specialties,work,skills,religion,politicalView,interestedIn,
            relationshipStatus,hometown,favorites,followersCount,followingCount,username,locale,verified,timezone,likes,
            samlData";
        }
        $params = array(
            "UID" => $uid,
            "include" => $include,
            "extraProfileFields" => $extraProfileFields
        );
        $res = $this->sendApiCall("accounts.getAccountInfo", $params);
        // retun gigya user object

    }


}