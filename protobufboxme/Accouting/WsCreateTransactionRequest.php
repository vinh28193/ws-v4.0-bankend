<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/accounting.proto

namespace Accouting;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 *&#47;/// TAO GIAO DICH (THU PHI, BOI HOAN PHI)
 *
 * Generated from protobuf message <code>Accouting.WsCreateTransactionRequest</code>
 */
class WsCreateTransactionRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * LA CHUOI JSON.DUMPS
     *
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
     *           LA CHUOI JSON.DUMPS
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\Accounting::initOnce();
        parent::__construct($data);
    }

    /**
     * LA CHUOI JSON.DUMPS
     *
     * Generated from protobuf field <code>string Param = 1;</code>
     * @return string
     */
    public function getParam()
    {
        return $this->Param;
    }

    /**
     * LA CHUOI JSON.DUMPS
     *
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
