<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: accounting.proto

namespace common\grpc\boxme\Accouting;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;
use common\grpc\boxme\GPBMetadata\Accounting as GPBMetadataAccounting;

/**
 * Generated from protobuf message <code>Accouting.Merchantinfo</code>
 */
class Merchantinfo extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int32 UserId = 1;</code>
     */
    private $UserId = 0;
    /**
     * Generated from protobuf field <code>string CountryCode = 2;</code>
     */
    private $CountryCode = '';
    /**
     * loai tien te
     *
     * Generated from protobuf field <code>string HomeCurrency = 3;</code>
     */
    private $HomeCurrency = '';
    /**
     * 1 user,2 user
     *
     * Generated from protobuf field <code>double UserLevel = 4;</code>
     */
    private $UserLevel = 0.0;
    /**
     * so du pvc (dung duyet don)
     *
     * Generated from protobuf field <code>double BalancePvc = 5;</code>
     */
    private $BalancePvc = 0.0;
    /**
     * so du cod
     *
     * Generated from protobuf field <code>double BalanceCod = 6;</code>
     */
    private $BalanceCod = 0.0;
    /**
     * tien thu ho tam tinh
     *
     * Generated from protobuf field <code>double Provisional = 7;</code>
     */
    private $Provisional = 0.0;
    /**
     * phi van chueyn tam tinh
     *
     * Generated from protobuf field <code>double Freeze = 8;</code>
     */
    private $Freeze = 0.0;
    /**
     * han muc cap cho khach hang
     *
     * Generated from protobuf field <code>double Quota = 9;</code>
     */
    private $Quota = 0.0;
    /**
     * Generated from protobuf field <code>double MoneyAvailable = 10;</code>
     */
    private $MoneyAvailable = 0.0;
    /**
     * Generated from protobuf field <code>double BalanceConfig = 11;</code>
     */
    private $BalanceConfig = 0.0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $UserId
     *     @type string $CountryCode
     *     @type string $HomeCurrency
     *           loai tien te
     *     @type float $UserLevel
     *           1 user,2 user
     *     @type float $BalancePvc
     *           so du pvc (dung duyet don)
     *     @type float $BalanceCod
     *           so du cod
     *     @type float $Provisional
     *           tien thu ho tam tinh
     *     @type float $Freeze
     *           phi van chueyn tam tinh
     *     @type float $Quota
     *           han muc cap cho khach hang
     *     @type float $MoneyAvailable
     *     @type float $BalanceConfig
     * }
     */
    public function __construct($data = NULL) {
        GPBMetadataAccounting::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>int32 UserId = 1;</code>
     * @return int
     */
    public function getUserId()
    {
        return $this->UserId;
    }

    /**
     * Generated from protobuf field <code>int32 UserId = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setUserId($var)
    {
        GPBUtil::checkInt32($var);
        $this->UserId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string CountryCode = 2;</code>
     * @return string
     */
    public function getCountryCode()
    {
        return $this->CountryCode;
    }

    /**
     * Generated from protobuf field <code>string CountryCode = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setCountryCode($var)
    {
        GPBUtil::checkString($var, True);
        $this->CountryCode = $var;

        return $this;
    }

    /**
     * loai tien te
     *
     * Generated from protobuf field <code>string HomeCurrency = 3;</code>
     * @return string
     */
    public function getHomeCurrency()
    {
        return $this->HomeCurrency;
    }

    /**
     * loai tien te
     *
     * Generated from protobuf field <code>string HomeCurrency = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setHomeCurrency($var)
    {
        GPBUtil::checkString($var, True);
        $this->HomeCurrency = $var;

        return $this;
    }

    /**
     * 1 user,2 user
     *
     * Generated from protobuf field <code>double UserLevel = 4;</code>
     * @return float
     */
    public function getUserLevel()
    {
        return $this->UserLevel;
    }

    /**
     * 1 user,2 user
     *
     * Generated from protobuf field <code>double UserLevel = 4;</code>
     * @param float $var
     * @return $this
     */
    public function setUserLevel($var)
    {
        GPBUtil::checkDouble($var);
        $this->UserLevel = $var;

        return $this;
    }

    /**
     * so du pvc (dung duyet don)
     *
     * Generated from protobuf field <code>double BalancePvc = 5;</code>
     * @return float
     */
    public function getBalancePvc()
    {
        return $this->BalancePvc;
    }

    /**
     * so du pvc (dung duyet don)
     *
     * Generated from protobuf field <code>double BalancePvc = 5;</code>
     * @param float $var
     * @return $this
     */
    public function setBalancePvc($var)
    {
        GPBUtil::checkDouble($var);
        $this->BalancePvc = $var;

        return $this;
    }

    /**
     * so du cod
     *
     * Generated from protobuf field <code>double BalanceCod = 6;</code>
     * @return float
     */
    public function getBalanceCod()
    {
        return $this->BalanceCod;
    }

    /**
     * so du cod
     *
     * Generated from protobuf field <code>double BalanceCod = 6;</code>
     * @param float $var
     * @return $this
     */
    public function setBalanceCod($var)
    {
        GPBUtil::checkDouble($var);
        $this->BalanceCod = $var;

        return $this;
    }

    /**
     * tien thu ho tam tinh
     *
     * Generated from protobuf field <code>double Provisional = 7;</code>
     * @return float
     */
    public function getProvisional()
    {
        return $this->Provisional;
    }

    /**
     * tien thu ho tam tinh
     *
     * Generated from protobuf field <code>double Provisional = 7;</code>
     * @param float $var
     * @return $this
     */
    public function setProvisional($var)
    {
        GPBUtil::checkDouble($var);
        $this->Provisional = $var;

        return $this;
    }

    /**
     * phi van chueyn tam tinh
     *
     * Generated from protobuf field <code>double Freeze = 8;</code>
     * @return float
     */
    public function getFreeze()
    {
        return $this->Freeze;
    }

    /**
     * phi van chueyn tam tinh
     *
     * Generated from protobuf field <code>double Freeze = 8;</code>
     * @param float $var
     * @return $this
     */
    public function setFreeze($var)
    {
        GPBUtil::checkDouble($var);
        $this->Freeze = $var;

        return $this;
    }

    /**
     * han muc cap cho khach hang
     *
     * Generated from protobuf field <code>double Quota = 9;</code>
     * @return float
     */
    public function getQuota()
    {
        return $this->Quota;
    }

    /**
     * han muc cap cho khach hang
     *
     * Generated from protobuf field <code>double Quota = 9;</code>
     * @param float $var
     * @return $this
     */
    public function setQuota($var)
    {
        GPBUtil::checkDouble($var);
        $this->Quota = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>double MoneyAvailable = 10;</code>
     * @return float
     */
    public function getMoneyAvailable()
    {
        return $this->MoneyAvailable;
    }

    /**
     * Generated from protobuf field <code>double MoneyAvailable = 10;</code>
     * @param float $var
     * @return $this
     */
    public function setMoneyAvailable($var)
    {
        GPBUtil::checkDouble($var);
        $this->MoneyAvailable = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>double BalanceConfig = 11;</code>
     * @return float
     */
    public function getBalanceConfig()
    {
        return $this->BalanceConfig;
    }

    /**
     * Generated from protobuf field <code>double BalanceConfig = 11;</code>
     * @param float $var
     * @return $this
     */
    public function setBalanceConfig($var)
    {
        GPBUtil::checkDouble($var);
        $this->BalanceConfig = $var;

        return $this;
    }

}

