<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-03-01
 * Time: 13:57
 */

namespace weshop\payment;

use \weshop\payment\PaymentAssets;

class PaymentWidget extends \yii\base\Widget
{

    public function init()
    {
        $payment = new Payment();
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub
    }
}