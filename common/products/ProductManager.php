<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-03-29
 * Time: 15:55
 */

namespace common\products;

use Yii;
use yii\base\Component;

/**
 * Class ProductManager
 * @package common\products
 * @property array|BaseGate $gates
 */
class ProductManager extends Component
{
    /**
     * @var array
     */
    private $_gates = [
        'ebay' => [
            'class' => 'common\products\ebay\EbayGate',
            'baseUrl' => 'https://api-lbc.weshop.asia/v3', //'https://ebay-api-wshopx-v3.weshop.com.vn/v3',
            'searchUrl' => 'search',
            'lookupUrl' => 'product'
        ],
        'amazon' => [
            'class' => 'common\products\amazon\AmazonGate',
            'baseUrl' => 'http://amazonapiv2.weshop.asia/amazon',
            'store' => \common\products\amazon\AmazonProduct::STORE_US
        ],
        'amazon-jp' => [
            'class' => 'common\products\amazon\AmazonGate',
            'baseUrl' => 'http://amazonapiv2.weshop.asia/amazon',
            'store' => \common\products\amazon\AmazonProduct::STORE_JP
        ]
    ];

    /**
     * create object
     * @param $config
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    protected function createGate($config)
    {
        return Yii::createObject($config);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasGate($name)
    {
        return array_key_exists($name, $this->_gates);
    }

    /**
     * get a driver
     * @param $name string
     * @return BaseGate
     */
    public function getGate($name)
    {
        if (!$this->hasGate($name)) {
            throw new \yii\base\InvalidParamException("Unknown gate '{$name}'.");
        }
        $this->setGate($name, $this->gates[$name]);
        return $this->gates[$name];
    }

    /**
     * set a driver
     * @param $name string
     * @param $config string|array|BaseGate
     * @throws \yii\base\InvalidConfigException
     */
    public function setGate($name, $config)
    {
        if (is_array($config) || is_string($config)) {
            $config = $this->createGate($config);
        }
        if ($config instanceof BaseGate) {
            $this->_gates[$name] = $config;
        } else {
            Yii::warning("can not set: " . get_class($config) . " not instanceof LoggingDriverInterface");
        }
    }

    /**
     * set gates
     * @param $gates string|array
     * @throws \yii\base\InvalidConfigException
     */
    public function setGates($gates)
    {
        $this->_gates = $gates;
    }

    /**
     * get gates
     * @return array
     */
    public function getGates()
    {
        return $this->_gates;
    }

    /**
     * php magic get
     * @param string $name
     * @return mixed
     * @throws \yii\base\UnknownPropertyException
     */
    public function __get($name)
    {
        if ($this->hasGate($name)) {
            return $this->getGate($name);
        }
        return parent::__get($name);
    }

    /**
     * php magic set
     * @param string $name
     * @param mixed $value
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UnknownPropertyException
     */
    public function __set($name, $value)
    {
        if (isset($this->_gates[$name])) {
            if (is_array($value) || is_string($value)) {
                $value = $this->createObject($value);
            }
            $this->_gates[$name] = $value;
        }
        return parent::__set($name, $value); // TODO: Change the autogenerated stub
    }

    /**
     * php magic method isset
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_gates[$name]) || parent::__isset($name);
    }

}
