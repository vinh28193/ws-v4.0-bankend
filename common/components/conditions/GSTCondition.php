<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-02-23
 * Time: 08:13
 */

namespace common\components\conditions;


class GSTCondition extends BaseCondition
{
    public $name = 'GSTCondition';

    public function execute($value, $additionalFee, $storeAdditionalFee)
    {
        return $value;
    }
}