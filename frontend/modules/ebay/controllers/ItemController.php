<?php


namespace frontend\modules\ebay\controllers;

use common\helpers\WeshopHelper;
use common\products\BaseProduct;
use Yii;
use common\products\forms\ProductDetailFrom;
use  frontend\modules\favorites\controllers\FavoriteObject as Favorite;

class ItemController extends EbayController
{

    public function actionDetail($id)
    {
        $form = new ProductDetailFrom();
        $form->load($this->request->getQueryParams(),'');
        $form->id = $id;
        $form->type = 'ebay';
        $item = $form->detail();
        if(Yii::$app->request->isPjax){
            if ($item === false) {
                return $this->renderAjax('@frontend/views/common/item_error', [
                    'errors' => $form->getErrors()
                ]);
            }
            return $this->renderAjax('index', [
                'item' => $item
            ]);
        }
        if ($item  === false) {
            return $this->render('@frontend/views/common/item_error', [
                'errors' => $form->getErrors()
            ]);
        }

        $favorite = null;
        // Queue get call Favorite to
        /*
        if(sleep(30)) {
            // Favorite
            $_favorite = new Favorite();
            $UUID = Yii::$app->user->getId();
            $uuid = isset($UUID) ? $UUID : \thamtech\uuid\helpers\UuidHelper::uuid();
            $_favorite->create($item, $id, $uuid);
        }
        */

        return $this->render('index', [
            'item' => $item,
            'favorite'=>$favorite
        ]);


    }

    public function actionVariation()
    {
        $response = ['success' => false, 'message' => 'can not call', 'content' => []];
        $form = new ProductDetailFrom();
        $post = Yii::$app->getRequest()->post();
        if ($form->load(Yii::$app->getRequest()->post(), '')) {
            $response['message'] = 'can not resolve request';
        }
        $form->type = 'ebay';
        if (($item = $form->detail()) === false) {
            $response['message'] = 'failed';
            $response['content'] = $form->getErrors();
        } else {
            /** @var $item BaseProduct */
            $fees = [];
            foreach ($item->getAdditionalFees()->keys() as $key) {
                $fees[$key] = $item->getAdditionalFees()->getTotalAdditionFees($key)[1];
            }
            $response['success'] = true;
            $response['message'] = 'success';
            $contentPrice = '<strong class="text-orange">' . WeshopHelper::showMoney($item->getLocalizeTotalPrice(), 1, '') . '<span class="currency">đ</span></strong>';
            if ($item->start_price) {
                $contentPrice .= '<b class="old-price">' . WeshopHelper::showMoney($item->getLocalizeTotalStartPrice(), 1, '') . '<span class="currency">đ</span></b>';
                $contentPrice .= '<span class="save">(Tiết kiệm: ' . WeshopHelper::showMoney($item->getLocalizeTotalStartPrice() - $item->getLocalizeTotalPrice(), 1, '') . 'đ)</span>';
            }
            $response['content'] = [
                'fees' => $fees,
                'queryParams' => $post,
                'sellPrice' => $item->getLocalizeTotalPrice(),
                'startPrice' => $item->getLocalizeTotalStartPrice(),
                'salePercent' => $item->getSalePercent(),
                'contentPrice' => $contentPrice,
            ];
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $response;
    }
}
