<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/Seller.proto

namespace Seller;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * create cashin in by weshop
 *
 * Generated from protobuf message <code>Seller.WsCreateCahinRequest</code>
 */
class WsCreateCahinRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string Param = 1;</code>
     */
    private $Param = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $Param
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\Seller::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string Param = 1;</code>
     * @return string
     */
    public function getParam()
    {
        return $this->Param;
    }

    /**
     * Generated from protobuf field <code>string Param = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setParam($var)
    {
        GPBUtil::checkString($var, True);
        $this->Param = $var;

        return $this;
    }

}

