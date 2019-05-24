<?php


namespace common\lib;


use common\models\Customer;
use Exception;
use frontend\modules\payment\providers\wallet\WalletClient;
use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;

class WalletBackendService extends BaseObject
{
    const MERCHANT_IP_PRO = 1;
    const MERCHANT_IP_DEV = 4;
    const WITHDRAW_MIN_AMOUNT = 100000;
    const TYPE_TOP_UP = 'TOP_UP';
    const TYPE_FREEZE = 'FREEZE';
    const TYPE_UN_FREEZE = 'UN_FREEZE';
    const TYPE_PAY_ORDER = 'PAY_ORDER';
    const TYPE_PAY_ADDFEE = 'ADDFEE';
    const TYPE_REFUND = 'REFUND';
    const TYPE_WITH_DRAW = 'WITH_DRAW';

    const STATUS_QUEUE = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETE = 2;
    const STATUS_CANCEL = 3;
    const STATUS_FAIL = 4;

    public $merchant_id = self::MERCHANT_IP_PRO;
    public $type;
    public $transaction_code;
    public $total_amount;
    public $payment_method;
    public $payment_provider;
    public $bank_code;
    public $payment_transaction;
    public $cardholderName;
    public $cardnumber;
    public $fee;
    public $amount;
    public $customer_id;
    public $description;
    public $baseUrl = 'http://wallet.weshop.v4.beta/v1/';


    public $otp_type;
    public $otp_code;
    /**
     * @var WalletClient
     */
    private $_walletClient;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->baseUrl = ArrayHelper::getValue(Yii::$app->params,'Url_wallet_api','http://wallet.weshop.v4.beta').'/v1/';
    }

    public function response($success, $message, $data = null)
    {
        return ['success' => $success, 'message' => $message, 'data' => $data];
    }

    public function callApiRequest($url, $params, $method = "POST")
    {
        try {
            $client = new Client();
            $response =  $client->createRequest()
                ->setMethod($method)
                ->setHeaders([
                    'Authorization' => str_replace('bearer ','', strtolower(Yii::$app->request->headers->get('Authorization')))
                ])
                ->setFormat('json')
                ->setUrl($this->baseUrl.$url)
                ->setData($params)->send()->getData();
            Yii::debug(Yii::$app->request->headers->get('Authorization'));
            Yii::debug($response);
            return $response;
        } catch (Exception $exception) {
            Yii::error($exception);
            return null;
        }
    }

    public function createSafePaymentTransaction()
    {
        $data['merchant_id'] = $this->merchant_id;
        $data['transaction_code'] = $this->payment_transaction;
        $data['total_amount'] = $this->total_amount;
        $data['payment_method'] = $this->payment_provider;
        $data['payment_provider'] = $this->payment_provider;
        $data['bank_code'] = $this->bank_code;
        $data['customer_id'] = $this->customer_id;
        $data['description'] = $this->description;
        $data['type'] = $this->type;
        return $this->callApiRequest('wallet-backend/create-transaction-add-fee', $data);
    }
}