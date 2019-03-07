<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-03-06
 * Time: 17:24
 */

namespace api\modules\v1\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use api\controllers\BaseApiController;

class CartController extends BaseApiController
{


    public function actionIndex(){
        //$this->getCart()->removeItems();
        $this->getCart()->addItem('252888606889','cleats_blowout_sports',1,'ebay','test');
        $this->getCart()->addItem('IF_6C960C53','cleats_blowout_sports',1,'ebay','test','252888606889');
//        $dataProvider = new ArrayDataProvider([
//            'allModels' => $this->getCart()->getItems(),
//        ]);
        $this->response(true,"ok",$this->getCart()->getItems());
    }

    public function actionAddToCart(){

        $this->getCart()->addItem('252888606889','cleats_blowout_sports',1,'ebay','test');
        return $this->response(true,"ok",$this->getCart()->getItems());
    }

    /**
     * @return \common\components\cart\CartManager
     */
    protected function getCart(){
        return Yii::$app->cart;
    }
}