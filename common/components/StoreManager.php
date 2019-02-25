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
    public $_store;

    /**
     * initialize store
     */

    public function init()
    {
        parent::init();
        if($this->storeClass === null){
            throw new InvalidConfigException(get_class($this).":storeClass can not be null");
        }
    }

    /**
     * getter
     * @return StoreInterface
     * @throws NotFoundHttpException
     */
    public function getStore(){
        if($this->_store === null){
            /** @var $class StoreInterface */
            $class = $this->storeClass;
            $this->_store = $class::getActiveStore(['url' => $this->domain]);
            if($this->_store === null){
                throw new NotFoundHttpException("not found store {$this->domain}");
            }
        }
        return $this->_store;
    }

    /**
     * setter
     * @param $store
     */
    public function setStore($store){
        /** @var $class StoreInterface */
        $class = $this->storeClass;
        $this->_store = $class::getActiveStore(['id' => $store]);
    }

    /**
     * @return mixed
     */
    public function getDomain(){
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else if(isset($_SERVER['HOSTNAME'])) {
            $host = $_SERVER['HOSTNAME'];
        } else {
            $host = $_SERVER['SERVER_NAME'];
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
        return $this->getStore()->id;
    }

    /**
     * @return mixed
     */
    public function getExcludeTables()
    {
        return $this->excludeTables;
    }
}