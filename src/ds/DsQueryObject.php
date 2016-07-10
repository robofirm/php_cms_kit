<?php
/**
 * Created by PhpStorm.
 * User: Yaniv Aran-Shamir
 * Date: 7/7/16
 * Time: 9:37 AM
 */

namespace ds;


use Gigya\sdk\GSFactory;

class DsQueryObject
{
    const VALUE_REG_EXP = '.*\s(and|AND|or|OR)\s(where|WHERE)\s.*';
    /**
     * @var string
     */
    private $query;
    /**
     * @var array
     */
    private $fields;
    /**
     * @var string
     */
    private $table;
    /**
     * @var array
     */
    private $ors;
    /**
     * @var array
     */
    private $ands;
    /**
     * @var string
     */
    private $oid;
    /**
     * @var array
     */
    private $operators;
    /**
     * @var array
     */
    private $ins;
    /**
     * @var string
     */
    private $appKey;
    /**
     * @var string
     */
    private $appSecret;
    /**
     * @var string
     */
    private $dataCenter;
    /**
     * @var null|string
     */
    private $siteSecret;
    /**
     * @var string
     */
    private $apiKey;
    /**
     * @var string
     */
    private $uid = null;

    /**
     * DsQueryObject constructor.
     *
     * @param string $apiKey
     * @param string $appKey
     * @param string $appSecret
     * @param string $dataCenter
     * @param string $siteSecret
     */
    public function __construct($apiKey, $appKey, $appSecret, $dataCenter,
        $siteSecret = null
    ) {
        $this->apiKey     = $apiKey;
        $this->appKey     = $appKey;
        $this->appSecret  = $appSecret;
        $this->dataCenter = $dataCenter;
        $this->siteSecret = $siteSecret;
        $this->operators  = array(
            "<",
            ">",
            "=",
            ">=",
            "<=",
            "!="
        );
    }


    /**
     * @param string $filed
     * @param array  $terms
     *
     * @return $this
     */
    public function addIn($filed, $terms)
    {
        $this->ins[$filed] = $terms;

        return $this;
    }

    /**
     * @param string $field
     * @param string $term
     * @param string $andOr
     *
     * @return $this
     */
    public function addContains($field, $term, $andOr = "and")
    {
        if ("or" == $andOr) {
            $this->addOr($field, "contains", $term);
        } else {
            $this->addAnd($field, "contains", $term);
        }

        return $this;
    }

    /**
     * @param string $field
     * @param string $op
     * @param string $value
     *
     * @return $this
     * @throws DsQueryException
     */
    public function addOr($field, $op, $value)
    {
        $this->ors[] = $this->sanitizeAndBuild($field, $op, $value);

        return $this;
    }

    private function sanitizeAndBuild($field, $op, $value)
    {
        $value = filter_var(
            $value, FILTER_VALIDATE_REGEXP, self::VALUE_REG_EXP
        );
        if (empty($field) || empty($op) || empty($value)) {
            throw new \InvalidArgumentException(
                "parameters can not be empty or a bad value string"
            );
        }
        if ( ! in_array($op, $this->operators)) {
            throw new DsQueryException($op . " is not a valid operator");
        }

        return filter_var($field, FILTER_SANITIZE_STRING) . " " . $op
        . " " . $value;
    }

    /**
     * @param string $field
     * @param string $op
     * @param string $value
     *
     * @return $this
     */
    public function addAnd($field, $op, $value)
    {
        $this->ands[] = $this->sanitizeAndBuild($field, $op, $value);

        return $this;
    }

    /**
     * @param string $field
     * @param string $term
     * @param string $andOr
     *
     * @return $this
     */
    public function addNotContains($field, $term, $andOr = "and")
    {
        if ("or" == $andOr) {
            $this->addOr($field, "not contains", $term);
        } else {
            $this->addAnd($field, "not contains", $term);
        }

        return $this;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function addField($field)
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * @param string $field
     * @param string $andOr
     *
     * @return $this
     */
    public function addIsNull($field, $andOr = "and")
    {
        if ("and" == strtolower($andOr)) {
            $this->ands[] = trim($field) . " is null";
        } elseif ("or" == strtolower($andOr)) {
            $this->ors[] = trim($field) . " is null";
        } else {
            throw new \InvalidArgumentException(
                'andOr parameter should "and" or "or"'
            );
        }

        return $this;

    }

    /**
     * @param string $field
     * @param string $andOr
     *
     * @return $this
     */
    public function addIsNotNull($field, $andOr = "and")
    {
        if ("and" == strtolower($andOr)) {
            $this->ands[] = trim($field) . " is not null";
        } elseif ("or" == strtolower($andOr)) {
            $this->ors[] = trim($field) . " is not null";
        } else {
            throw new \InvalidArgumentException(
                'andOr parameter should "and" or "or"'
            );
        }

        return $this;

    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }


    // getters and setters

    /**
     * @param mixed $fields
     *
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return string mixed
     */
    public function getOid()
    {
        return $this->oid;
    }

    /**
     * @param string $oid
     *
     * @return $this
     */
    public function setOid($oid)
    {
        $this->oid = $oid;

        return $this;
    }

    /**
     * @param string $appKey
     *
     * @return $this
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;

        return $this;
    }

    /**
     * @param string $appSecret
     *
     * @return $this
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;

        return $this;
    }

    /**
     * @param string $dataCenter
     *
     * @return $this
     */
    public function setDataCenter($dataCenter)
    {
        $this->dataCenter = $dataCenter;

        return $this;
    }

    /**
     * @param string $siteSecret
     *
     * @return $this
     */
    public function setSiteSecret($siteSecret)
    {
        $this->siteSecret = $siteSecret;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    public function dsGet()
    {
        $paramsArray = array("oid" => $this->oid, "type" => $this->table);
        if ( ! empty($this->uid)) {
            $paramsArray['UID'] = $this->uid;
        }
        if (count($this->fields) > 0) {
            $paramsArray['fields'] = $this->buildFieldsString();
        }
        $params = GSFactory::createGSObjectFromArray($paramsArray);
        if (null === $this->siteSecret) {
            $req = GSFactory::createGsRequest(
                $this->apiKey, $this->siteSecret, "ds.get", $params,
                $this->dataCenter
            );
        } else {
            $req = GSFactory::createGSRequestAppKey(
                $this->apiKey, $this->appKey, $this->appSecret, "ds.get",
                $this->dataCenter
            );
        }
        $res = $req->send();

        return $res->getData();
    }

    public function dsSearch()
    {
        $params = GSFactory::createGSObjectFromArray(
            array("query" => $this->query)
        );
        if (null === $this->siteSecret) {
            $req = GSFactory::createGsRequest(
                $this->apiKey, $this->siteSecret, "ds.search", $params,
                $this->dataCenter
            );
        } else {
            $req = GSFactory::createGSRequestAppKey(
                $this->apiKey, $this->appKey, $this->appSecret, "ds.search",
                $this->dataCenter
            );
        }
        $res = $req->send();

        return $res->getData();
    }

    public function dsDelete()
    {

        $paramsArray = array("oid" => $this->oid, "type" => $this->table);
        if ( ! empty($this->uid)) {
            $paramsArray['UID'] = $this->uid;
        }
        if (count($this->fields) > 0) {
            $paramsArray['fields'] = $this->buildFieldsString();
        }
        $params = GSFactory::createGSObjectFromArray(
            array("oid" => $this->oid)
        );
        if (null === $this->siteSecret) {
            $req = GSFactory::createGsRequest(
                $this->apiKey, $this->siteSecret, "ds.delete", $params,
                $this->dataCenter
            );
        } else {
            $req = GSFactory::createGSRequestAppKey(
                $this->apiKey, $this->appKey, $this->appSecret, "ds.delete",
                $this->dataCenter
            );
        }
        $req->send();
    }

    protected function buildQuery()
    {
        if ( ! $this->checkAllRequired()) {
            throw new DsQueryException("missing fields or table");
        }
        $fields = $this->buildFieldsString();
        $q      = "SELECT " . $fields . " FROM " . $this->table;
        $where  = true;
        if ( ! empty($this->ins)) {
            $q .= " WHERE ";
            $ins = join(", ", $this->ins);
            $in  = "in(" . $ins . ")";
            $q .= $in;
            $where = false;
        } elseif ( ! empty($this->ands)) {
            if ($where) {
                $q .= " WHERE ";
                $where = false;
            } else {
                $q .= " AND ";
            }
            $ands = join(" AND ", $this->ands);
            $q .= $ands;
        } elseif ( ! empty($this->ors)) {
            if ($where) {
                $q .= " WHERE ";
                $where = false;
            } else {
                $q .= " OR ";
            }
            $ors = join(" OR ", $this->ors);
            $q .= $ors;
        }
        $this->query = $q;
    }

    /**
     * @return bool
     */
    private function checkAllRequired()
    {
        return ! (empty($this->fields) && empty($this->table));
    }

    private function buildFieldsString()
    {
        return in_array("*", $this->fields)
            ? "*"
            : join(
                ", ", $this->fields
            );

    }


}