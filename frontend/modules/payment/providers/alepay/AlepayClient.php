<?php


namespace frontend\modules\payment\providers\alepay;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;

/**
 * Class AlepayClient
 * @package frontend\modules\payment\providers\alepay
 * @property-read AlepaySecurity $security
 * @property-read Client $httpClient
 */
class AlepayClient extends Component
{

    const ENV_PROD = 'PROD';
    const ENV_SANDBOX = 'SANDBOX';
    public $env = self::ENV_SANDBOX;
    public $baseUrl = 'https://alepay-sandbox.nganluong.vn/checkout/v1';
    public $apiKey = 'g84sF7yJ2cOrpQ88VbdZoZfiqX4Upx';
    public $checksumKey = 'lXntf6CIZbSgzMqTz1nQ11jPKhGfsF';
    public $encryptKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCKWYg7jKrTqs83iIvYxlLgMqIy4MErNsoBKU2MHaG5ccntzGeNcDba436ds+VWB4E9kaL+D2wTuaiU+4Hx7DcyJ3leksXXM85koV/97f8Gn4nd3epxucaurcXmcEvU/VfqU7bKTdLdLwB7yPaZ45ilmBh/GqGJnmfq9csVuyZ0cwIDAQAB';
    public $callbackUrl = '';


    /**
     * @var AlepaySecurity
     */
    private $_security;

    /**
     * @return AlepaySecurity
     * @throws \yii\base\InvalidConfigException
     */
    public function getSecurity()
    {
        if ($this->_security === null) {
            $this->_security = Yii::createObject([
                'class' => AlepaySecurity::className(),
                'publicKey' => $this->encryptKey,
            ]);
        }
        return $this->_security;
    }

    private $_httpClient;

    public function getHttpClient()
    {
        if ($this->_httpClient === null) {
            $this->_httpClient = Yii::createObject([
                'class' => Client::className(),
                'baseUrl' => $this->baseUrl,
            ]);
        }
        return $this->_httpClient;
    }

    public function createHttpRequest($url, $data)
    {
        // Todo Alepay Log
        $dataJson = json_encode($data);
        $dataEncrypt = $this->security->encrypt($dataJson);
        $checksum = $this->security->md5Data($dataEncrypt . $this->checksumKey);
        $items = [
            'token' => $this->apiKey,
            'data' => $dataEncrypt,
            'checksum' => $checksum
        ];
        $data_string = json_encode($items);
        $ch = curl_init($this->getUrl($url));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        $result = json_decode($result);
        $success = $result->errorCode === '000';
        return ['success' => $result->errorCode === '000', 'message' => $result->errorDescription, 'data' => $success ? $this->security->decrypt($result->data) : null];
    }

    protected function getUrl($url)
    {
        $baseUrl = $this->baseUrl;
        if ($this->env === self::ENV_SANDBOX) {
            $baseUrl = str_replace('https://alepay.vn', 'https://alepay-sandbox.nganluong.vn', $baseUrl);
        }
        return $baseUrl . '/' . $url;
    }

    public function getInstallmentInfo($amount, $currencyCode)
    {
        return $this->createHttpRequest('get-installment-info', [
            'amount' => $amount,
            'currencyCode' => $currencyCode
        ]);
    }

    public function requestOrder($params)
    {
        return $this->createHttpRequest('request-order',$params);
    }

}