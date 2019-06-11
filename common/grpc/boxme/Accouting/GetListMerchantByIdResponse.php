<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: accounting.proto

namespace common\grpc\boxme\Accouting;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;
use common\grpc\boxme\Accouting\Merchantinfo;
use common\grpc\boxme\GPBMetadata\Accounting as GPBMetadataAccounting;

/**
 * Generated from protobuf message <code>Accouting.GetListMerchantByIdResponse</code>
 */
class GetListMerchantByIdResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bool Error = 1;</code>
     */
    private $Error = false;
    /**
     * Generated from protobuf field <code>repeated .Accouting.Merchantinfo Data = 2;</code>
     */
    private $Data;
    /**
     * Generated from protobuf field <code>string Message = 3;</code>
     */
    private $Message = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type bool $Error
     *     @type Merchantinfo[]|\Google\Protobuf\Internal\RepeatedField $Data
     *     @type string $Message
     * }
     */
    public function __construct($data = NULL) {
        GPBMetadataAccounting::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bool Error = 1;</code>
     * @return bool
     */
    public function getError()
    {
        return $this->Error;
    }

    /**
     * Generated from protobuf field <code>bool Error = 1;</code>
     * @param bool $var
     * @return $this
     */
    public function setError($var)
    {
        GPBUtil::checkBool($var);
        $this->Error = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .Accouting.Merchantinfo Data = 2;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getData()
    {
        return $this->Data;
    }

    /**
     * Generated from protobuf field <code>repeated .Accouting.Merchantinfo Data = 2;</code>
     * @param Merchantinfo[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setData($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, Merchantinfo::class);
        $this->Data = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string Message = 3;</code>
     * @return string
     */
    public function getMessage()
    {
        return $this->Message;
    }

    /**
     * Generated from protobuf field <code>string Message = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setMessage($var)
    {
        GPBUtil::checkString($var, True);
        $this->Message = $var;

        return $this;
    }

}

