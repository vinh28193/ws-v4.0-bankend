<?php


namespace frontend\modules\payment;


use common\components\cart\CartHelper;
use common\helpers\WeshopHelper;
use common\models\Category;
use common\models\db\TargetAdditionalFee;
use common\models\PaymentMethodProvider;
use common\models\PaymentTransaction;
use common\models\Seller;
use common\models\User;
use frontend\modules\payment\models\Order;
use frontend\modules\payment\models\Product;
use Yii;
use common\models\PaymentProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class PaymentService
{

    const INSTALMENT_MIN_AMOUNT = 3500000;

    const PAYMENT_METHOD_GROUP_COD = 1;
    const PAYMENT_METHOD_GROUP_INSTALMENT = 2;
    const PAYMENT_METHOD_GROUP_MASTER_VISA = 3;
    const PAYMENT_METHOD_NL_WALLET = 4;
    const PAYMENT_METHOD_GROUP_ATM = 5;
    const PAYMENT_METHOD_GROUP_QRCODE = 6;
    const PAYMENT_METHOD_BANK_TRANSFER = 7;
    const PAYMENT_METHOD_BANK_MCPAY = 8;

    public static function getClientConfig($merchant, $env = null)
    {
        $params = ArrayHelper::getValue(Yii::$app->params, "paymentClientParams.{$merchant}", []);
        $env = $env === null ? $params['enable'] : $env;
        return isset($params['params'][$env]) ? $params['params'][$env] : (isset($params['params']) ? $params['params'] : []);
    }

    public static function loadPaymentByStoreFromDb($store, $provider_id = null)
    {
        $query = PaymentProvider::find();
        $where = [
            'AND',
            ['store_id' => $store],
            ['status' => 1]
        ];
        if ($provider_id !== null) {
            $where[] = ['id' => $provider_id];
        }
        $query->with('paymentMethodProviders', 'paymentMethodProviders.paymentMethod', 'paymentMethodProviders.paymentMethod.paymentMethodBanks', 'paymentMethodProviders.paymentMethod.paymentMethodBanks.paymentBank');
        $query->where($where);
        return $query->asArray()->all();
    }

    /**
     * @param $provider
     * @param $method
     * @return PaymentMethodProvider|null
     */
    public static function getMethodProvider($provider, $method)
    {
        return PaymentMethodProvider::find()
            ->with(['paymentProvider', 'paymentMethod'])
            ->where([
                'AND',
                ['payment_provider_id' => $provider],
                ['payment_method_id' => $method],
            ])
            ->one();
    }


    /**
     * @param $code
     * @return PaymentTransaction|null
     */
    public static function findParentTransaction($code)
    {
        $q = PaymentTransaction::find();
        $q->where([
            'AND',
            ['transaction_code' => $code],
            ['IS', 'order_code', new Expression('NULL')]
        ]);
        return $q->one();
    }

    /**
     * @param $tractionCode
     * @param null $orderCode
     * @return PaymentTransaction|null
     */
    public static function findChildTransaction($tractionCode, $orderCode = null)
    {
        $conditions = ['AND'];
        $conditions[] = ['transaction_code' => $tractionCode];
        if ($orderCode !== null) {
            $conditions[] = ['order_code' => $orderCode];
        } else {
            $conditions[] = ['IS NOT', 'order_code', new Expression('NULL')];
        }
        $q = PaymentTransaction::find();
        $q->where($conditions);
        return $q->one();
    }


    public static function findChildTransactionByTransactionCode($code)
    {
        $q = PaymentTransaction::find();
        $q->where([
            'AND',

        ]);
        return $q->one();
    }

    public
    static function toNumber($value)
    {
        return (integer)$value;
    }

    public
    static function generateTransactionCode($prefix = 'PM')
    {
        return WeshopHelper::generateTag(time(), 'PM', 16);
    }

    public
    static function createReturnUrl($provider)
    {
        // http://weshop.com.vn/payment/42/return.html
        // http://weshop.com.vn/payment/46/return.html
        // http://weshop.com.vn/payment/50/return.html
        // return Url::toRoute(["/payment/payment/return", 'merchant' => $provider], true);
        return Url::toRoute(["/payment/payment/return", 'merchant' => $provider], 'https');
    }

    public
    static function createCancelUrl($code)
    {
        return Url::to("/checkout/invoice/$code/fail.html", true);
    }

    public static function createSuccessUrl($code)
    {
        return Url::to("/checkout/invoice/$code/success.html", true);
    }

    public static function createBillingUrl($orderCode)
    {
        return Url::to("/order-$orderCode/billing.html", true);
    }

    public static function createCheckoutUrl($type = null, $code = null)
    {
        $route = ['/checkout/shipping'];
        if ($type !== null) {
            $route += ['ref' => $type];
        }
        if ($code !== null) {
            $route += ['code' => $code];
        }
        return Url::to($route, true);
    }

    public
    static function getInstallmentBankIcon($code)
    {
        $icons = [
            'VPBANK' => 'img/bank/vpbank.png', //NH TMCP Vi???t Nam Th???nh V?????ng (VPBANK)
            'TECHCOMBANK' => 'img/bank/techcombank.png', //NH TMCP K??? Th????ng Vi???t Nam (TECHCOMBANK)
            'ACB' => 'img/bank/acb.png', //NH TMCP ?? Ch??u (ACB)
            'ANZ' => 'img/bank/ANZ.png', //NH TNHH MTV ANZ Vi???t Nam (ANZ)
            'HSBC' => 'img/bank/hsbc.png', //NH TNHH MTV HSBC (Vi???t Nam) (HSBC)
            'SHINHANBANK' => 'img/bank/shinhanbank.png', // NH TNHH MTV Shinhan Vi???t Nam (SHINHANBANK)
            'EXIMBANK' => 'img/bank/eximbank.png',  //NH TMCP Xu???t Nh???p Kh???u (EXIMBANK)
            'MARITIMEBANK' => 'img/bank/maritime.png', //NH TMCP H??ng H???i (MARITIMEBANK)
            'VIB' => 'img/bank/vp.png', //NH Qu???c t??? (VIB)
            'SACOMBANK' => 'img/bank/sacombank.png', //NH TMCP S??i G??n Th????ng T??n (SACOMBANK)
            'CTB' => 'img/bank/citibank.png', //NH CitiBank Vi???t Nam (CTB)
            'SEABANK' => 'img/bank/seabank.png', //NH TMCP ????ng Nam ?? (SEABANK)
            'SC' => 'img/bank/standerd-charterd.png', //NH TNHH MTV Standard Chartered (Vi???t Nam) (SC)
            'TPB' => 'img/bank/tpb.png', //NH TMCP Ti??n Phong (TPB)
            'SCB' => 'img/bank/scb.png', //NH TMCP S??i G??n (SCB)
            'FE' => 'img/bank/fe.png', //FE CREDIT (FE)
            'NAB' => 'img/bank/nam-a.png', //NH TMCP Nam ?? (NAB)
            'OCB' => 'img/bank/ocb.png', //NH Ph????ng ????ng (OCB)
            'KLB' => 'img/bank/kien-long.png', //NH TMCP Ki??n Long (KLB)
            'SHB' => 'img/bank/shb.png', //NH TMCP S??i G??n H?? N???i (SHB)
            'BIDV' => 'img/bank/bidv.png', //NH TMCP ?????u T?? v?? Ph??t Tri???n Vi???t Nam (BIDV)
            'VCB' => 'img/bank/vietcombank.png', //NH TMCP Ngo???i Th????ng Vi???t Nam (VCB)
            'MB' => 'img/bank/mb.png' //NH TMCP Qu??n ?????i (MB)
        ];
        $icon = isset($icons[$code]) ? Url::to($icons[$code], true) : ArrayHelper::getValue(Yii::$app->params, 'unknownBankCode', '#');
        return $icon;
    }

    public
    static function getInstallmentMethodIcon($code)
    {
        $icons = [
            'VISA' => 'img/bank/visa.png',
            'MASTERCARD' => 'img/bank/master.png',
            'JCB' => 'img/bank/jcb.png',
        ];
        $icon = isset($icons[$code]) ? Url::to($icons[$code], true) : ArrayHelper::getValue(Yii::$app->params, 'unknownMethodCode', '#');
        return $icon;
    }

}
