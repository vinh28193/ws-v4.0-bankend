<?php


namespace frontend\modules\checkout\controllers;


use common\components\cart\CartManager;
use frontend\modules\payment\providers\wallet\WalletService;
use frontend\models\LoginForm;
use frontend\models\SignupForm;
use Yii;
use common\components\cart\CartHelper;
use frontend\modules\payment\models\ShippingForm;
use frontend\modules\payment\Payment;
use common\models\SystemStateProvince;
use common\components\cart\CartSelection;
use yii\helpers\ArrayHelper;

class ShippingController extends CheckoutController
{

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    public function gaCheckout()
    {
        Yii::info("GA CHECKOUT");
        $request = Yii::$app->ga->request();

        // Build the order data programmatically, each product of the order included in the payload
        // First, general and required hit data
        if (!Yii::$app->user->isGuest) {
            $request->setClientId(Yii::$app->user->identity->getId() . 'WS' . Yii::$app->user->identity->email);
            $request->setUserId(Yii::$app->user->identity->getId());
        }

        // Then, include the transaction data
        $request->setTransactionId('7778922')
            ->setAffiliation('THE ICONIC')
            ->setRevenue(250.0)
            ->setTax(25.0)
            ->setShipping(15.0)
            ->setCouponCode('MY_COUPON');

        // Include a product, the only required fields are SKU and Name
        $productData1 = [
            'sku' => 'AAAA-6666',
            'name' => 'Test Product 2',
            'brand' => 'Test Brand 2',
            'category' => 'Test Category 3/Test Category 4',
            'variant' => 'yellow',
            'price' => 50.00,
            'quantity' => 1,
            'coupon_code' => 'TEST 2',
            'position' => 2
        ];

        $request->addProduct($productData1);

        // You can include as many products as you need, this way
        $productData2 = [
            'sku' => 'AAAA-5555',
            'name' => 'Test Product',
            'brand' => 'Test Brand',
            'category' => 'Test Category 1/Test Category 2',
            'variant' => 'blue',
            'price' => 85.00,
            'quantity' => 2,
            'coupon_code' => 'TEST',
            'position' => 4
        ];

        $request->addProduct($productData2);

        // Don't forget to set the product action, which is PURCHASE in the example below
        $request->setProductActionToPurchase();

        // Finally, you need to send a hit; in this example, we are sending an Event
        $request->setEventCategory('Checkout')
            ->setEventAction('Purchase')
            ->sendEvent();

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

    public function actionIndex($type)
    {
        $activeStep = 1;
        if (($keys = CartSelection::getSelectedItems($type)) === null) {
            return $this->goBack();
        }
        $payment = new Payment([
            'page' => Payment::PAGE_CHECKOUT,
            'uuid' => $this->filterUuid(),
            'carts' => $keys,
            'payment_type' => $type,
        ]);
        $payment->initDefaultMethod();
        $shippingForm = new ShippingForm();
        $shippingForm->setDefaultValues();
        $provinces = SystemStateProvince::select2Data(1);

        return $this->render('index', [
            'activeStep' => $activeStep,
            'shippingForm' => $shippingForm,
            'payment' => $payment,
            'provinces' => $provinces
        ]);
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

}
