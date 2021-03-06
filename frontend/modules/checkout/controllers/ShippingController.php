<?php


namespace frontend\modules\checkout\controllers;


use common\components\cart\CartManager;
use common\components\cart\storage\MongodbCartStorage;
use common\components\gaSetting;
use common\components\UserCookies;
use common\models\PaymentTransaction;
use common\models\Category;
use common\models\SystemCountry;
use common\models\SystemDistrict;
use common\models\SystemZipcode;
use common\models\User;
use frontend\modules\payment\models\Order;
use frontend\modules\payment\PaymentService;
use frontend\modules\payment\providers\wallet\WalletService;
use frontend\models\LoginForm;
use frontend\models\SignupForm;
use Yii;
use common\components\cart\CartHelper;
use frontend\modules\payment\models\ShippingForm;
use frontend\modules\payment\Payment;
use common\models\SystemStateProvince;
use common\components\cart\CartSelection;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\products\BaseProduct;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;

class ShippingController extends CheckoutController
{

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    public function gaCheckout(Payment $payment)
    {
        gaSetting::gaCheckout($payment);
    }

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'sub-district' => [
                'class' => 'common\actions\DepDropAction',
                'useAction' => 'common\models\SystemDistrict::select2Data'
            ]
        ]);
    }

    public function init()
    {
        parent::init();
        $this->site_name = 'Checkout page';
        if (($type = Yii::$app->request->get('type')) !== null) {
            $this->site_title = Yii::t('frontend', 'Product in {type} cart', [
                'type' => $type
            ]);
        }

    }

    public function actionIndex()
    {
        $queryParams = $this->request->queryParams;

        $uuid = $this->filterUuid();

        $shippingForm = new ShippingForm();
        $shippingForm->setDefaultValues();

        $page = Payment::PAGE_CHECKOUT;
        $type = ArrayHelper::getValue($queryParams, 'ref');
        $code = ArrayHelper::getValue($queryParams, 'code');

        $payment = new Payment([
            'page' => $page,
            'uuid' => $uuid,
            'type' => $type
        ]);

        $orders = [];
        if ($type !== null && CartSelection::isValidType($type) && ($keys = CartSelection::getSelectedItems($type)) !== null && !empty($keys)) {
            foreach ($keys as $key) {
                $order = new Order();
                $order->checkoutType = $type;
                $order->uuid = $uuid;
                $order->cartId = $key;
                if ($order->createOrderFromCart() === false) {
                    continue;
                }
                $orders[$order->cartId] = $order;
            }
            $shippingForm->setDefaultValues();
        } elseif ($code !== null && strpos($code, 'PM', 0) !== false) {
            $payment->page = Payment::PAGE_BILLING;
            if (($parent = PaymentService::findParentTransaction($code))) {
                $payment->transaction_code = $code;
                foreach ($parent->childPaymentTransaction as $childPaymentTransaction) {
                    if (($orderParam = $childPaymentTransaction->order) !== null) {
                        $order = new Order($orderParam->getAttributes());
                        $order->getAdditionalFees()->loadFormActiveRecord($orderParam, 'order');
                        $order->getAdditionalFees()->remove('international_shipping_fee');
                        $orders[$order->ordercode] = $order;
                        $shippingForm->loadAddressFormOrder($order);
                    }
                }
            } elseif ($parent === null && ($child = PaymentService::findChildTransaction($code))) {
                $payment->page = Payment::PAGE_ADDITION;
                $payment->transaction_code = $code;
                $order = new Order($child->order->getAttributes());
                $order->setTotalAmount($child->transaction_amount_local);
                $orders[$order->ordercode] = $order;
                $shippingForm->loadAddressFormOrder($order);
            }
        } else if ($code !== null && ($order = Order::findOne(['ordercode' => $code])) !== null) {
            $payment->page = Payment::PAGE_BILLING;
            $payment->transaction_code = $order->payment_transaction_code;
            $order = new Order($order->getAttributes());
            $order->getAdditionalFees()->loadFormActiveRecord($order, 'order');
            $order->getAdditionalFees()->remove('international_shipping_fee');
            $orders[$order->ordercode] = $order;
            $shippingForm->loadAddressFormOrder($order);
        }

        if (count($orders) === 0) {
            throw new NotFoundHttpException("Not found");
        }

        $payment->initDefaultMethod();
        $payment->setOrders($orders);

        $siteName = Yii::t('frontend', 'Checkout');
        $titleCollection = [];
        if ($payment->type === CartSelection::TYPE_BUY_NOW) {
            $siteName = Yii::t('frontend', 'Buy now');

        } elseif ($payment->type === CartSelection::TYPE_SHOPPING) {
            $siteName = Yii::t('frontend', 'Shopping');
        }

        if($payment->page === Payment::PAGE_ADDITION){
            $siteName = Yii::t('frontend', 'Addition');
        }
        $titleCollection[] = $siteName;
        if($code !== null && $payment->page === Payment::PAGE_ADDITION){
            $titleCollection[] = $code;
        }
        $titleCollection[] = $shippingForm->getStoreManager()->store->name;

        foreach ($payment->getOrders() as $order) {

            $titleCollection[] = Yii::t('frontend', 'Seller:{portal} {seller}', [
                'portal' => strtoupper($order->seller->portal) === 'EBAY' ? 'eBay' : 'Amazon',
                'seller' => Inflector::camelize($order->seller->seller_name)
            ]);
            foreach ($order->products as $product) {
                $titleCollection[] = $product->product_name;
            }
        }
        $this->site_name = $siteName;
        $this->site_title = implode(' | ', $titleCollection);
        $this->site_description = 'checkout | products | payment | visa | master |bank transfer';
        $this->gaCheckout($payment);
        return $this->render('index', [
            'shippingForm' => $shippingForm,
            'payment' => $payment,
        ]);
    }

    public function actionValidate()
    {
        $model = new ShippingForm();
        $model->on(ShippingForm::EVENT_AFTER_VALIDATE, function ($event) {
            /** @var  $event yii\base\Event */
            $sender = $event->sender;
        });
        $request = $this->request;
        Yii::info($request->post(), __METHOD__);
        // Todo save info of guest user when whole typing on form
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($request->isAjax && $request->isPost && $model->load($request->post())) {
            return \yii\bootstrap4\ActiveForm::validate($model);
        }
        return [];
    }

    public function actionLogin()
    {
        Yii::$app->response->format = Yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->user->isGuest) {
            return ['success' => true];
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $key = CartSelection::getSelectedItems(CartSelection::TYPE_BUY_NOW);
            if ($key) {
                $this->module->cartManager->setMeOwnerItem($key[0]);
            }
            $wallet = new WalletService();
            $wallet->login($model->password);
            return ['success' => true, 'message' => Yii::t('frontend', 'Login success')];
        } else {
            return ['success' => false, 'message' => Yii::t('frontend', 'Login fail'), 'data' => $model->errors];
        }
    }

    public function actionSignup()
    {
        Yii::$app->response->format = Yii\web\Response::FORMAT_JSON;
        Yii::info('register new');
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                Yii::info('register new 002');
                Yii::$app->session->setFlash('success', Yii::t('frontend', 'Check your email for further instructions.'));
                $model->sendEmail();
                Yii::info('register new 003');
                if (Yii::$app->getUser()->login($user)) {
                    $key = CartSelection::getSelectedItems(CartSelection::TYPE_BUY_NOW);
                    if ($key) {
                        $this->module->cartManager->setMeOwnerItem($key[0]);
                    }
                    $wallet = new WalletService();
                    $wallet->login($model->password);
                    return ['success' => true, 'message' => Yii::t('frontend', 'Sign up success')];
                }
            }
        }
        return ['success' => false, 'message' => 'Sign up fail', 'data' => $model->errors];
    }

    public function actionAddCartCheckout()
    {
        $bodyParams = $this->request->bodyParams;
        if (($type = ArrayHelper::getValue($bodyParams, 'type')) === null) {
            return false;
        }
        if (($cartIds = ArrayHelper::getValue($bodyParams, 'cartIds')) === null || !is_array($cartIds) || empty($cartIds)) {
            return false;
        }
        if (($params = ArrayHelper::getValue($bodyParams, 'params')) === null || !is_array($params) || empty($params)) {
            return false;
        }
        if (!empty($cartIds) && !empty($params)) {
            foreach ($cartIds as $cartIt) {
                $this->getCart()->updateSafeItem($type, $cartIt, $params);
            }
            return true;
        }
        return false;


    }

    /**
     * @return \common\components\cart\CartManager
     */
    protected function getCart()
    {
        return Yii::$app->cart;
    }


}
