<?php

namespace frontend\modules\payment;


use Yii;
use yii\di\Instance;
use yii\web\IdentityInterface;
use common\components\cart\CartManager;
/**
 * payment module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\payment\controllers';



    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
