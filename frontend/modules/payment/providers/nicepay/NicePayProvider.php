<?php

namespace frontend\modules\payment\providers\nicepay;

use Exception;
use common\models\logs\PaymentGatewayLogs;
use common\models\PaymentTransaction;
use frontend\modules\payment\Payment;
use frontend\modules\payment\PaymentProviderInterface;
use frontend\modules\payment\PaymentResponse;
use frontend\modules\payment\PaymentService;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class NicePayProvider extends BaseObject implements PaymentProviderInterface
{

    public $iMid;
    public $iMidInstallment;
    public $merchantKey;
    public $merchantKeyInstallment;

    public $isInstallment = false;
    public $identity = 'NicepayLite';
    public $version = '1.11';
    public $buildDate = '20160309';

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        if (($yiiParams = PaymentService::getClientConfig('nicepay')) !== null && !empty($yiiParams)) {
            Yii::configure($this, $yiiParams); // reconfig with env
        }
        if ((!$this->isInstallment && $this->iMid === null) || ($this->isInstallment && $this->iMidInstallment === null)) {
            throw new InvalidConfigException("missing require parameter");
        }
    }

    /**
     * @var NicePayClient
     */
    private $_client;

    /**
     * @return NicePayClient
     */
    public function getClient()
    {
        if (!is_object($this->_client)) {
            $this->_client = new NicePayClient();
        }
        return $this->_client;
    }

    /**
     * @var NotificationCollection
     */
    private $_notifications;

    /**
     * @return NotificationCollection
     */
    public function getNotifications()
    {
        if ($this->_notifications === null) {
            $this->_notifications = new NotificationCollection();
            foreach ($_REQUEST as $name => $value) {
                $this->_notifications->set($name, $value);
            }
        }
        return $this->_notifications;
    }

    /**
     * @return string
     */
    public function merchantTokenInstallment()
    {
        // Concatenate(iMid + referenceNo + amt + merchantKey)
        return NicePayUtils::hashData($this->iMidInstallment .
            $this->getClient()->getData()->get('referenceNo') .
            $this->getClient()->getData()->get('amt') .
            $this->merchantKeyInstallment
        );
    }

    /**
     * @return string
     */
    public function merchantTokenC()
    {
        // Concatenate(iMid + referenceNo + amt + merchantKey)
        return NicePayUtils::hashData($this->iMid .
            $this->getClient()->getData()->get('tXid') .
            $this->getClient()->getData()->get('amt') .
            $this->merchantKey
        );
    }

    public function merchantToken()
    {
        if ($this->isInstallment) {
            return NicePayUtils::hashData($this->iMidInstallment .
                $this->getClient()->getData()->get('tXid') .
                $this->getClient()->getData()->get('amt') .
                $this->merchantKeyInstallment
            );
        }
        //Concatenate(iMid + referenceNo + amt + merchantKey)
        return NicePayUtils::hashData($this->iMid .
            $this->getClient()->getData()->get('referenceNo') .
            $this->getClient()->getData()->get('amt') .
            $this->merchantKey
        );
    }

    // Request VA
    public function requestVA()
    {

        $this->getClient()->getData()->set('iMid', $this->iMid);
        $this->getClient()->getData()->set('merchantToken', $this->merchantToken());
        // Populate data
        $this->getClient()->getData()->set('instmntMon', '1');
        $this->getClient()->getData()->set('instmntType', '1');
        $this->getClient()->getData()->set('vat', '0');
        $this->getClient()->getData()->set('fee', '0');
        $this->getClient()->getData()->set('notaxAmt', '0');
        if ($this->getClient()->getData()->get('cartData', '') === '') {
            $this->getClient()->getData()->set('cartData', '{}');
        }

        if (($response = $this->getClient()->requestVA()) !== false) {

            return $response;
        }
        return false;
    }

    // Charge Credit Card
    public function chargeCard()
    {
        $this->getClient()->getData()->set('iMid', $this->iMidInstallment);
        $this->getClient()->getData()->set('merchantToken', $this->merchantTokenInstallment());

        // Populate data
        //$this->getClient()->getData()->set('instmntMon', '1');
        //$this->getClient()->getData()->set('instmntType', '0');
        //$this->getClient()->getData()->set('vat', '0');
        //$this->getClient()->getData()->set('fee', '0');
        $this->getClient()->getData()->set('notaxAmt', '0');
        if ($this->getClient()->getData()->get('fee') == "") {
            $this->getClient()->getData()->setDefault('fee', '0');
        }
        if ($this->getClient()->getData()->get('vat') == "") {
            $this->getClient()->getData()->setDefault('vat', '0');
        }
        if ($this->getClient()->getData()->get('cartData') == "") {
            $this->getClient()->getData()->setDefault('cartData', '{}');
        }

        // Send Request
        if (($response = $this->getClient()->chargeCard()) !== false) {
            return $response;
        }
        return false;
    }

    public function checkPaymentStatus($tXid, $referenceNo, $amt)
    {
        // Populate data
        $this->getClient()->getData()->setDefault('tXid', $tXid);
        $this->getClient()->getData()->setDefault('referenceNo', $referenceNo);
        $this->getClient()->getData()->setDefault('amt', $amt);
        if ($this->isInstallment) {
            $this->getClient()->getData()->setDefault('iMid', $this->iMidInstallment);
        } else {
            $this->getClient()->getData()->setDefault('iMid', $this->iMid);
        }
        $this->getClient()->getData()->setDefault('merchantToken', $this->merchantToken());
        // Send Request
        if (($response = $this->getClient()->checkPaymentStatus()) !== false) {
            return $response;
        }
        return false;
    }

    // Cancel VA (VA can be canceled only if VA status is not paid)
    public function cancelVA($tXid, $amt)
    {

        // Populate data
        $this->getClient()->getData()->set('tXid', $tXid);
        $this->getClient()->getData()->set('amt', $amt);

        $this->getClient()->getData()->setDefault('iMid', $this->iMid);
        $this->getClient()->getData()->setDefault('merchantToken', $this->merchantTokenC());
        // Send Request
        if (($response = $this->getClient()->requestVa()) !== false) {
            return $response;
        }
        return false;
    }

    public function create(Payment $payment)
    {
        $bankCd = !empty($payment->payment_bank_code) ? $payment->payment_bank_code : '';
        if (strpos($bankCd, 'ATM_') !== false) {
            $bankCd = str_replace('ATM_', '', $bankCd);
        }
        $dateNow = date('Ymd');
        $vaExpiryDate = date('Ymd', strtotime($dateNow . ' +1 day')); // Set VA expiry date +1 day (optional)
        // Populate Mandatory parameters to send
        $this->getClient()->getData()->set('currency', 'IDR');
        $this->getClient()->getData()->set('amt', round($payment->getTotalAmountDisplay())); // Total gross amount
        $this->getClient()->getData()->set('dbProcessUrl', $payment->return_url); // Total gross amount
        $this->getClient()->getData()->set('referenceNo', $payment->transaction_code);
        $this->getClient()->getData()->set('callBackUrl', $payment->cancel_url);
        $decs = 'Payment of orders ' . $payment->getOrderCodes();
        $this->getClient()->getData()->set('description', $decs); // Transaction description
        $this->getClient()->getData()->set('goodsNm', $decs);

        $this->getClient()->getData()->set('billingNm', $payment->customer_name); // Customer name
        $this->getClient()->getData()->set('billingPhone', $payment->customer_phone); // Customer phone number
        $this->getClient()->getData()->set('billingEmail', $payment->customer_email); //
        $this->getClient()->getData()->set('billingAddr', $payment->customer_address);
        $this->getClient()->getData()->set('billingCity', $payment->customer_district);
        $this->getClient()->getData()->set('billingState', $payment->customer_city);
        $this->getClient()->getData()->set('billingPostCd', $payment->customer_postcode);
        $this->getClient()->getData()->set('billingCountry', $payment->customer_country);
        $this->getClient()->getData()->set('deliveryNm', $payment->customer_name); // Delivery name
        $this->getClient()->getData()->set('deliveryPhone', $payment->customer_phone);
        $this->getClient()->getData()->set('deliveryEmail', $payment->customer_email);
        $this->getClient()->getData()->set('deliveryAddr', $payment->customer_address);
        $this->getClient()->getData()->set('deliveryCity', $payment->customer_district);
        $this->getClient()->getData()->set('deliveryState', $payment->customer_city);
        $this->getClient()->getData()->set('deliveryPostCd', $payment->customer_postcode);
        $this->getClient()->getData()->set('deliveryCountry', $payment->customer_country);
        if ($payment->payment_method == NicePayUtils::PAYMENT_METHOD && $payment->getTotalAmountDisplay() < NicePayUtils::AMOUNT_REQUIRED) {
            return new PaymentResponse(false, "Amount lesser than installment require", 'nicepay');
        }
        $logPaymentGateway = new PaymentGatewayLogs();
        $logPaymentGateway->transaction_code_ws = $payment->transaction_code;
        $logPaymentGateway->type = PaymentGatewayLogs::TYPE_CREATED;
        $logPaymentGateway->response_time = date('Y-m-d H:i:s');
        $logPaymentGateway->create_time = date('Y-m-d H:i:s');
        $logPaymentGateway->store_id = 7;
        $logPaymentGateway->payment_method = $payment->payment_method;
        $logPaymentGateway->payment_bank = $payment->payment_bank_code;
        $logPaymentGateway->amount = $payment->getTotalAmountDisplay();


        try {
            if ($payment->instalment_type == 2 && !empty($payment->installment_bank) && !empty($payment->installment_method)) {
                $this->getClient()->getData()->set('payMethod', '01');
                $this->getClient()->getData()->set('instmntMon', $payment->installment_month);
                $this->getClient()->getData()->set('instmntType', $payment->instalment_type);
                if (($response = $this->chargeCard()) === false) {
                    $logPaymentGateway->type = PaymentGatewayLogs::TYPE_CREATED_FAIL;
                    $logPaymentGateway->request_content = $this->getClient()->getData()->toArray();
                    $logPaymentGateway->response_content = $this->getClient()->getErrorMsg();
                    $logPaymentGateway->save(false);
                    return new PaymentResponse(false, $this->getClient()->getErrorMsg(), 'nicepay');
                }
                if (isset($response['data']) && ($data = $response['data']) !== null && !empty($data) && isset($data['resultCd']) && $data['resultCd'] == '0000') {
                    $checkoutUrl = $data['requestURL'] . '?tXid=' . $data['tXid'];
                    $logPaymentGateway->request_content = $this->getClient()->getData()->toArray();
                    $logPaymentGateway->response_content = $response;
                    $logPaymentGateway->url = $checkoutUrl;
                    $logPaymentGateway->save(false);
                    return new PaymentResponse(true, 'Success', 'nicepay', $payment->transaction_code, null,PaymentResponse::TYPE_NORMAL, PaymentResponse::METHOD_GET, $data['tXid'], $checkoutUrl);
                }
                $logPaymentGateway->type = PaymentGatewayLogs::TYPE_CREATED_FAIL;
                $logPaymentGateway->request_content = $this->getClient()->getData()->toArray();
                $logPaymentGateway->response_content = $response;
                $logPaymentGateway->save(false);
                return new PaymentResponse(false, Yii::t('frontend','Payment gateway error `{message}`',[
                    'message' => Yii::t('frontend','Unknown error')
                ]), 'nicepay');

            } elseif ($payment->instalment_type == 1) {
                $this->getClient()->getData()->set('payMethod', '02');
                $this->getClient()->getData()->set('bankCd', $bankCd);
                $this->getClient()->getData()->set('vacctValidDt', $vaExpiryDate); // Set VA expiry date example: +1 day
                $this->getClient()->getData()->set('vacctValidTm', date('His')); // Set VA Expiry Time
                if (($response = $this->requestVA()) === false) {
                    $logPaymentGateway->type = PaymentGatewayLogs::TYPE_CREATED_FAIL;
                    $logPaymentGateway->request_content = $this->getClient()->getData()->toArray();
                    $logPaymentGateway->response_content = $this->getClient()->getErrorMsg();
                    $logPaymentGateway->save(false);
                    return new PaymentResponse(false, Yii::t('frontend','Payment gateway error `{message}`',[
                        'message' => $this->getClient()->getErrorMsg()
                    ]), 'nicepay');
                }
                if (isset($response['resultCd']) && $response['resultCd'] == '0000') {
                    $logPaymentGateway->request_content = $this->getClient()->getData()->toArray();
                    $logPaymentGateway->response_content = $response;
                    $logPaymentGateway->save(false);
                    return new PaymentResponse(true, 'Success', 'nicepay');
                }
                $logPaymentGateway->type = PaymentGatewayLogs::TYPE_CREATED_FAIL;
                $logPaymentGateway->request_content = $this->getClient()->getData()->toArray();
                $logPaymentGateway->response_content = $response;
                $logPaymentGateway->save(false);
                return new PaymentResponse(false, Yii::t('frontend','Payment gateway error `{message}`',[
                    'message' => Yii::t('frontend','Unknown error')
                ]), 'nicepay');
            } else {
                $this->getClient()->getData()->set('payMethod', '02');
                $this->getClient()->getData()->set('bankCd', $bankCd);
                $this->getClient()->getData()->set('vacctValidDt', $vaExpiryDate); // Set VA expiry date example: +1 day
                $this->getClient()->getData()->set('vacctValidTm', date('His')); // Set VA Expiry Time
                if (($response = $this->requestVA()) === false) {
                    $logPaymentGateway->type = PaymentGatewayLogs::TYPE_CREATED_FAIL;
                    $logPaymentGateway->response_content = $this->getClient()->getErrorMsg();
                    $logPaymentGateway->save(false);
                    return new PaymentResponse(false, Yii::t('frontend','Payment gateway error `{message}`',[
                        'message' => $this->getClient()->getErrorMsg()
                    ]), 'nicepay');
                }

                if (isset($response['resultCd']) && (string)$response['resultCd'] === '0000') {
                    $checkoutUrl = Url::toRoute([
                        '/checkout/notify/nice-pay-success',
                        'code' => $response['referenceNo'],
                        'token' => $response['tXid'],
                        'billingNm' => $response['billingNm'],
                        'transTm' => $response['transTm'],
                        'transDt' => $response['transDt'],
                        'description' => $response['description'],
                        'bankVacctNo' => $response['bankVacctNo'],
                        'vacctValidDt' => $response['vacctValidDt'],
                        'vacctValidTm' => $response['vacctValidTm'],
                        'bankCd' => $response['bankCd'],
                        'currency' => $response['currency'],
                        'amount' => $response['amount']
                    ], true);
                    $logPaymentGateway->request_content = $this->getClient()->getData()->toArray();
                    $logPaymentGateway->response_content = $response;
                    $logPaymentGateway->url = $checkoutUrl;
                    $logPaymentGateway->save(false);

                    return new PaymentResponse(true, Yii::t('frontend','Success'), 'nicepay', $payment->transaction_code, null,PaymentResponse::TYPE_REDIRECT, PaymentResponse::METHOD_GET, $response['tXid'], $response['resultCd'], $checkoutUrl);
                }

                $logPaymentGateway->type = PaymentGatewayLogs::TYPE_CREATED_FAIL;
                $logPaymentGateway->request_content = $this->getClient()->getData()->toArray();
                $logPaymentGateway->response_content = $response;
                $logPaymentGateway->save(false);
                return new PaymentResponse(false, Yii::t('frontend','Payment gateway error `{message}`',[
                    'message' => Yii::t('frontend','Unknown error')
                ]), 'nicepay');
            }
        } catch (Exception $exception) {
            $logPaymentGateway->response_content = $exception->getMessage() . " \n " . $exception->getFile() . " \n " . $exception->getTraceAsString();
            $logPaymentGateway->type = PaymentGatewayLogs::TYPE_CREATED_FAIL;
            $logPaymentGateway->save(false);
            return new PaymentResponse(false, Yii::t('frontend','Payment gateway error `{message}`',[
                'message' => Yii::t('yii','An internal server error occurred.')
            ]), 'nicepay');

        }
    }

    public function handle($data)
    {
        $logCallback = new PaymentGatewayLogs();
        $logCallback->response_time = date('Y-m-d H:i:s');
        $logCallback->create_time = date('Y-m-d H:i:s');
        $logCallback->request_content = $data;
        $logCallback->type = PaymentGatewayLogs::TYPE_CALLBACK;
        $logCallback->transaction_code_request = "NICE PAY CALLBACK";
        $logCallback->store_id = 7;

        $tXid = $this->getNotifications()->get('tXid');
        $referenceNo = $this->getNotifications()->get('referenceNo');
        $amt = $this->getNotifications()->get('amt');


        if ($referenceNo === null) {
            $logCallback->type = PaymentGatewayLogs::TYPE_CALLBACK_FAIL;
            $logCallback->response_content = $this->getNotifications()->toArray();
            $logCallback->type = PaymentGatewayLogs::TYPE_CALLBACK_FAIL;
            $logCallback->save(false);
            return new PaymentResponse(false, Yii::t('yii','Missing required parameters: {params}',[
               'params' => 'referenceNo'
            ]), 'nicepay');
        }
        if (($transaction = PaymentService::findParentTransaction($referenceNo)) === null) {
            $logCallback->response_content = "Không tìm thấy transaction";
            $logCallback->type = PaymentGatewayLogs::TYPE_CALLBACK_FAIL;
            $logCallback->save(false);
            return new PaymentResponse(false, Yii::t('frontend','Transaction not found'), 'nicepay');
        }
        try {

            if (($response = $this->checkPaymentStatus($tXid, $referenceNo, $amt)) === false) {
                $logCallback->response_content = $this->getClient()->getErrorMsg();
                $logCallback->type = PaymentGatewayLogs::TYPE_CALLBACK_FAIL;
                $logCallback->save(false);
                return new PaymentResponse(false, $this->getClient()->getErrorMsg(), 'nicepay');
            }

            if (isset($response['status']) && $response['status'] == 0) {
                $transaction->transaction_status = PaymentTransaction::TRANSACTION_STATUS_SUCCESS;
                $transaction->save(false);
                $logCallback->response_content = $response;
                $logCallback->save(false);
                return new PaymentResponse(true, Yii::t('frontend','Success'), 'nicepay', $transaction);
            } else {
                $this->isInstallment = true;
                if (($response = $this->checkPaymentStatus($tXid, $referenceNo, $amt)) === false) {
                    $logCallback->response_content = $this->getClient()->getErrorMsg();
                    $logCallback->type = PaymentGatewayLogs::TYPE_CALLBACK_FAIL;
                    $logCallback->save(false);
                    return new PaymentResponse(false, $this->getClient()->getErrorMsg(), 'nicepay');
                }
                if (isset($response['resultCd']) && isset($response['resultMsg']) && $response['resultCd'] == '0000' && $response['resultMsg'] == 'paid') {
                    $transaction->transaction_status = PaymentTransaction::TRANSACTION_STATUS_SUCCESS;
                    $transaction->save(false);
                    $logCallback->response_content = $response;
                    $logCallback->save(false);
                    return new PaymentResponse(true, 'Installment Success', 'nicepay', $transaction);
                }
                $logCallback->response_content = $response;
                $logCallback->save(false);
                return new PaymentResponse(false, 'Failed', 'nicepay');
            }

        } catch (Exception $e) {
            $logCallback->response_content = $e->getMessage() . " \n " . $e->getFile() . " \n " . $e->getTraceAsString();
            $logCallback->type = PaymentGatewayLogs::TYPE_CALLBACK_FAIL;
            $logCallback->save(false);
            return new PaymentResponse(false, 'Call back fail', 'nicepay');
        }
    }
}
