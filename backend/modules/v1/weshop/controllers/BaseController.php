<?php
/**
 * Created by PhpStorm.
 * User: galat
 * Date: 23/02/2019
 * Time: 10:46
 */

namespace backend\modules\v1\weshop\controllers;


use backend\modules\v1\controllers\RestController;

class BaseController extends RestController
{
    function init()
    {
        \Yii::$app->user->identityClass = 'common\models\db\Customer';
        parent::init(); // TODO: Change the autogenerated stub
    }
}