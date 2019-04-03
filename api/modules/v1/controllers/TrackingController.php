<?php
/**
 * Created by PhpStorm.
 * User: galat
 * Date: 01/04/2019
 * Time: 2:59 CH
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseApiController;
use common\models\db\PurchaseOrder;
use common\models\db\PurchaseProduct;
use common\models\Product;
use common\models\TrackingCode;

class TrackingController extends BaseApiController
{
    /**
     * @inheritdoc
     */
    protected function rules()
    {
        return [
            [
                'allow' => true,
                'actions' => ['index'],
                'roles' => ['operation', 'master_operation']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function verbs()
    {
        return [
            'index' => ['GET'],
            'create' => ['POST']
        ];
    }

    public function actionCreate(){
        $tranId = \Yii::$app->request->post("tran_id");
        $trackingCode = \Yii::$app->request->post("tracking_code");
        $sku = \Yii::$app->request->post("sku");
        $estimate = \Yii::$app->request->post("estimate");
        $quantity = \Yii::$app->request->post("quantity");
        $status = \Yii::$app->request->post("status");
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = PurchaseOrder::find()->where(['purchase_order_number' => $tranId])->one();
        if(!$purchaseOrder){
            return $this->response(false,'can not find tranid '.$tranId.' in data!');
        }
        /** @var PurchaseProduct[] $purchaseProducts */
        $purchaseProducts = PurchaseProduct::find()->where(['sku' => $sku,'purchase_order_id' => $purchaseOrder->id])->all();
        $tracking = new TrackingCode();
        $tracking->store_id = 1;
        $tracking->tracking_code = $trackingCode;
        $tracking->warehouse_alias = $purchaseOrder->receive_warehouse_id;
        $tracking->status = $status;
        $tracking->remove = 0;
        $tracking->created_by = \Yii::$app->user->id;
        $tracking->updated_by = \Yii::$app->user->id;
        $tracking->created_at = time();
        $tracking->updated_at = time();
        //#Todo set tracking_code get by extensions
        foreach ($purchaseProducts as $purchaseProduct){
            $tracking->quantity = $purchaseProduct->purchase_quantity;
            $tracking->order_ids = $purchaseProduct->order_id;
            $tracking->CreateOrUpdate(false);
//            $purchaseProduct->receive_quantity = $staus == 'shipped' ?
        }
    }
}