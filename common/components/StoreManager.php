<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-02-15
 * Time: 16:58
 */

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Class Store
 * @package common\components
 *
 * @property-read  StoreInterface $store
 * @property-read  string $domain
 * @property-read  string $storeReferenceKey
 * @property-read  integer $id
 */
class StoreManager extends Component
{

    const STORE_VN = 1;
    const STORE_ID = 7;

    public $defaultDomain = 'weshop-4.0.frontend.vn';

    /**
     * @var string
     */
    public $storeClass = 'common\models\Store';
    /**
     * @var array
     */
    public $excludeTables = [
        'store',
        'store_additional_fee',
        'migration'
    ];

    /**
     * @var StoreInterface;
     */
    private $_store;

    /**
     * initialize store
     */

    public function init()
    {
        parent::init();
        if ($this->storeClass === null) {
            throw new InvalidConfigException(get_class($this) . ":storeClass can not be null");
        }
    }

    /**
     * getter
     * @return StoreInterface
     * @throws NotFoundHttpException
     */
    public function getStore()
    {
        if ($this->_store === null) {
            /** @var $class StoreInterface */
            $class = $this->storeClass;
            $this->_store = $class::getActiveStore(['url' => $this->getDomain()]);
            if ($this->_store === null) {
                throw new NotFoundHttpException("not found store {$this->getDomain()}");
            }
        }
        return $this->_store;
    }

    /**
     * setter
     * @param $store
     */
    public function setStore($store)
    {
        /** @var $class StoreInterface */
        $class = $this->storeClass;
        $this->_store = $class::getActiveStore(['id' => $store]);
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else if (isset($_SERVER['HOSTNAME'])) {
            $host = $_SERVER['HOSTNAME'];
        } else if (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
        } else {
            $host = $this->defaultDomain;
        }
        return $host;
    }


    /**
     * @return mixed
     */
    public function getStoreReferenceKey()
    {
        /** @var $class StoreInterface */
        $class = $this->storeClass;
        return $class::getStoreReferenceKey(); //only one
    }

    /**
     * @return integer
     */
    public function getId()
    {
        //return $this->getStore()->{$this->storeReferenceKey};
        return $this->store->id;
    }

    public function __get($name)
    {
        $country = 'VN';
        $getter = "is$country";
        $reg = "^/is[A-Z]{2}/$";
        if ($name === $getter) {

        }
        return parent::__get($name); // TODO: Change the autogenerated stub
    }

    /**
     * @return mixed
     */
    public function getExcludeTables()
    {
        return $this->excludeTables;
    }

    private $_exRate;

    public function getExchangeRate()
    {
        if (!$this->_exRate) {
            /** @var  $exRate ExchangeRate */
            $exRate = Yii::$app->exRate;
            $this->_exRate = $exRate->load('USD', $this->store->currency);
        }
        return $this->_exRate;
    }

    public function getLanguageId()
    {
        return 'vi';
    }

    public function showMoney($money)
    {
        return $money . ' ' . $this->getStore()->currency;
    }


    /**
     * Todo 1 trường trong db table store và rewrite hàm magic __get để magic property $isVN,$isID
     * @return bool
     */
    public function isVN()
    {
        return $this->getId() == self::STORE_VN;
    }

    public function isID()
    {
        return $this->getId() == self::STORE_ID;
    }
}