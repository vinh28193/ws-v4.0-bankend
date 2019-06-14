<?php


namespace frontend\controllers;

use common\components\cart\CartHelper;
use common\components\cart\CartManager;
use common\helpers\WeshopHelper;
use common\models\Store;
use common\models\User;
use common\promotion\PromotionForm;
use frontend\modules\payment\providers\mcpay\McPayProvider;
use frontend\modules\payment\providers\nganluong\ver3_2\NganLuongClient;
use frontend\modules\payment\providers\nganluong\ver3_2\NganluongHelper;
use frontend\modules\payment\providers\nicepay\NicePayClient;
use Yii;
use common\components\cart\storage\MongodbCartStorage;
use frontend\modules\payment\PaymentService;
use frontend\modules\payment\providers\alepay\AlepayClient;
use yii\helpers\ArrayHelper;

class TestController extends FrontendController
{

    public function actionTestMe()
    {
        echo PaymentService::createReturnUrl(42);
        die;
    }

    public function actionTestRsa()
    {

    }

    public function actionAlepay()
    {
        $alepay = new AlepayClient();
        echo "<pre>";
        var_dump($alepay->getInstallmentInfo(10000000.00, 'VND'));
    }

    public function actionTestCart()
    {
        /** @var  $cartManager CartManager */

        $cartManager = Yii::$app->cart;

        $cartManager->addItem('shopping', [
            'source' => 'ebay',
            'sku' => '100-99800902-NRC',
            'id' => '163586118957',
            'sellerId' => 'amFicmEtY29tcGFueS1zdG9yZS0t',
            'quantity' => 2,
            'image' => 'https://i.ebayimg.com/00/s/MTQwMFgxNDAw/z/fCkAAOSwuN9cgrz3/$_1.JPG'
        ]);
//
//        $cartManager->addItem('shopping', [
//            'source' => 'ebay',
//            'sku' => '100-99000000-02',
//            'id' => '163189059666',
//            'sellerId' => 'amFicmEtY29tcGFueS1zdG9yZS0t',
//            'quantity' => 1,
//            'image' => 'https://d3d71ba2asa5oz.cloudfront.net/12022392/images/elite_65t_earbuds_rgb_72dpi.jpg'
//        ]);
//
//        $cartManager->addItem('shopping', [
//            'source' => 'ebay',
//            'sku' => '204151',
//            'id' => '163655512954',
//            'sellerId' => 'amFicmEtY29tcGFueS1zdG9yZS0t',
//            'quantity' => 1,
//            'image' => 'https://i.ebayimg.com/00/s/MTQwMFgxNDAw/z/Qr0AAOSwuCVcuKWl/$_1.JPG'
//        ]);
//        $cartManager->addItem('shopping', [
//            'source' => 'ebay',
//            'sku' => '100-99600900-02',
//            'id' => '163314595720',
//            'sellerId' => 'amFicmEtY29tcGFueS1zdG9yZS0t',
//            'quantity' => 1,
//            'image' => 'https://i.ebayimg.com/00/s/MTQwMFgxNDAw/z/kGwAAOSwnFpbwy9H/$_1.JPG'
//        ]);
//        $cartManager->addItem('shopping', [
//            'source' => 'ebay',
//            'sku' => 'YC1060-I;0',
//            'id' => '232738139862',
//            'sellerId' => 'dGVtcG9yZXgtaW50ZXJuYXRpb25hbC0t',
//            'quantity' => 1,
//            'image' => 'https://i.ebayimg.com/00/s/MTYwMFgxNjAw/z/3hAAAOSwQ~ha1u4Q/$_1.JPG'
//        ]);
//        var_dump($cartManager->filterItem('shopping', ['source' => 'ebay', 'seller' => 'Y2hvb3Nlc21hcnQtLQ==']));

//        $item1 = $cartManager->updateItem('shopping', '5ced1365e419ac1fb80057b9', ['id' => '163586118957', 'sku' => '100-99800902-NRC'], ['quantity' => 1]);
//        $cartManager->removeItem('shopping', '5cee2e6ce419ac05a00007eb', ['id' => '163655512954', 'sku' => '204151']);
        $item2 = $cartManager->getItem('shopping', '5cef4645e419ac46000075a0');

        var_dump($item2);
        die;
    }

    public function actionTime()
    {
        $formater = Yii::$app->formatter;
        $dateTime = new \DateTime('now');
        $dateTime->setTime(23, 59, 59, 59);
        var_dump($formater->asDatetime($dateTime));
        die;
    }

    public function actionTestCount()
    {
        $storage = new MongodbCartStorage();
        $authManager = Yii::$app->authManager;
        $saleIds = $authManager->getUserIdsByRole('sale');
        $masterSaleIds = $authManager->getUserIdsByRole('master_sale');
        $supporters = User::find()->indexBy('id')->select(['id', 'email', 'username'])->where(['or', ['id' => $saleIds], ['id' => $masterSaleIds]])->all();

        $ids = array_keys($supporters);
        $calculateToday = ArrayHelper::map($storage->calculateSupported($ids), '_id', function ($elem) {
            return ['count' => $elem['count'], 'price' => $elem['price']];
        });

        $countData = [];
        foreach ($ids as $id) {
            $c = 0;
            if (isset($calculateToday[$id]) && ($forSupport = $calculateToday[$id]) !== null && !empty($forSupport) && isset($forSupport['count'])) {
                $c = $forSupport['count'];
            }
            $countData[$id] = $c;
        }
        asort($countData);

        $sQMin = WeshopHelper::sortMinValueArray($countData);

        $priceResult = [];

        foreach ($sQMin as $id => $val) {
            $p = 0;
            if (isset($calculateToday[$id]) && ($forSupport = $calculateToday[$id]) !== null && !empty($forSupport) && isset($forSupport['price'])) {
                $p = $forSupport['price'];
            }
            $priceResult[$id] = $p;
        }
        $priceResult = array_keys($priceResult);
        $id = array_shift($priceResult);
        if (($assigner = ArrayHelper::getValue($supporters, $id)) === null) {
            $assigner = array_shift($supporters);
        }
        return ['id' => $assigner->id, 'email' => $assigner->email, 'username' => $assigner->username];
        var_dump($assigner);
        die;
    }

    public function actionI18n()
    {
        echo Yii::t('javascript', 'Hello {name}', ['name' => 'VINH']);
        die;
    }

    public function actionSql()
    {
        $user = User::find()->where(['id' => 1])->one();
        var_dump($user->getPrimaryKey());
        die;
    }

    public function actionCreate()
    {
        $provice = new McPayProvider();
        $provice->amount = '20000';
        $provice->orderId = 'asdasdasd';
        $provice->billName = 'asd asDSA';
        var_dump($provice->createCheckOutUrl());
        die;
    }

    public function actionPromotion()
    {
        $posts = require dirname(dirname(__DIR__)) . '/common/promotion/mock-post.php';
        $promotionForm = new PromotionForm();
        $promotionForm->load($posts, '');

        var_dump($promotionForm->checkPromotion());
        die;
    }

    public function actionNganLuong()
    {
        $client = new NganLuongClient();
        var_dump($client->GetRequestField('QRCODE_AGB'));
        die;
    }

    public function actionCheckPaymentStatus($token)
    {

        $client = new NganLuongClient();
        var_dump($client->GetTransactionDetail($token));
        die;
    }

    public function actionCustomFee()
    {
        $message = [];
        $data = require dirname(dirname(__DIR__)) . '\common\models\category_group.php';
        foreach ($data as $array) {
            $rules = [];
            if($array['condition_data'] !== null){
                foreach ($array['condition_data'] as $condition) {
                    $calc = new \common\calculators\Calculator();
                    $calc->register($condition);
                    $rules[] = $calc->deception();
                }
            }
            $str = "Group {$array['id']}: `{$array['name']}`";
            if (!empty($rules)) {
                $str .= ' calculator: ' . implode(', ', $rules);
            }
            $message[] = $str;
        }
        var_dump($message);
        die;
    }
    public function actionTestSale() {
        $sale = $this->actionTestCount();
        var_dump($sale);
        die();
    }
}