<?php

namespace frontend\modules\payment\models;

use common\components\StoreManager;
use common\components\UserCookies;
use common\helpers\WeshopHelper;
use common\models\SystemDistrict;
use common\models\SystemDistrictMapping;
use common\models\SystemStateProvince;
use common\models\SystemZipcode;
use Yii;
use yii\base\Model;
use common\models\Address;
use common\models\User;
use common\components\GetUserIdentityTrait;
use yii\db\Query;

/**
 * Class ShippingForm
 * @package frontend\modules\payment\models
 *
 * @property User|null $user
 */
class ShippingForm extends Model
{

    use GetUserIdentityTrait;

    const YES = 1;
    const NO = 0;

    public $buyer_address_id;

    public $buyer_name;
    public $buyer_email;
    public $buyer_phone;
    public $buyer_address;
    public $buyer_post_code;
    public $buyer_country_id;
    public $buyer_province_id;
    public $buyer_district_id;

    public $note_by_customer;

    public $receiver_address_id;

    public $receiver_name;
    public $receiver_email;
    public $receiver_phone;
    public $receiver_address;
    public $receiver_post_code;
    public $receiver_country_id;
    public $receiver_province_id;
    public $receiver_district_id;

    public $enable_buyer = self::NO;
    public $enable_receiver = self::NO;
    public $save_buyer_address = self::NO;
    public $save_receiver_address = self::NO;
    public $other_receiver = self::NO;
    public $customer_id;


    public function attributes()
    {
        return [
            'buyer_address_id', 'buyer_name', 'buyer_email', 'buyer_phone', 'buyer_address', 'buyer_post_code', 'buyer_country_id', 'buyer_province_id', 'buyer_district_id',
            'receiver_address_id', 'receiver_name', 'receiver_email', 'receiver_phone', 'receiver_address', 'receiver_post_cod', 'receiver_country_id', 'receiver_province_id', 'receiver_district_id',
        ];
    }
    public function rules()
    {
        return parent::rules(); // TODO: Change the autogenerated stub
    }

    /**
     * @var StoreManager
     */
    private $_storeManager;

    /**
     * @return StoreManager
     */
    public function getStoreManager()
    {
        if (!is_object($this->_storeManager)) {
            $this->_storeManager = Yii::$app->storeManager;
        }
        return $this->_storeManager;
    }

    /**
     * @var Address[]|null
     */
    private $_receiverAddress = [];

    /**
     * @return Address[]|null
     */
    public function getReceiverAddress()
    {
        if (empty($this->_receiverAddress) && $this->getUser() !== null && !empty($receivers = $this->getUser()->shippingAddress)) {
            $this->_receiverAddress = $receivers;
        }
        return $this->_receiverAddress;
    }

    /**
     * @var Address|null
     */
    private $_buyerAddress;

    /**
     * @return Address|null
     */
    public function getBuyerAddress()
    {
        if (empty($this->_buyerAddress) && $this->getUser() !== null && !empty($buyers = $this->getUser()->defaultPrimaryAddress)) {
            $this->_buyerAddress = $buyers;
        }
        return $this->_buyerAddress;
    }

    public function setDefaultValues()
    {
        /** @var  $store  StoreManager */
        $store = $this->getStoreManager();
        $this->buyer_country_id = $store->store->country_id;
        $this->receiver_country_id = $store->store->country_id;
        // shipping Address
        $this->other_receiver = self::NO;
        if ($this->getUser() !== null) {
            $this->customer_id = $this->getUser()->id;
            $this->save_receiver_address = self::NO;
            if (WeshopHelper::isEmpty($this->getBuyerAddress())) {
                $this->enable_buyer = self::YES;
                $this->save_buyer_address = self::YES;
                $this->buyer_name = $this->getUser()->last_name .' '.$this->getUser()->first_name;
                $this->buyer_phone = $this->getUser()->phone;
                $this->buyer_email = $this->getUser()->email;
            }
            if (WeshopHelper::isEmpty($this->getReceiverAddress())) {
                $this->enable_receiver = self::YES;
                $this->save_receiver_address = self::YES;
                $this->receiver_name = $this->getUser()->last_name .' '.$this->getUser()->first_name;
                $this->receiver_email = $this->getUser()->email;
                $this->receiver_phone = $this->getUser()->phone;
            }
        }else {
            $this->enable_buyer = self::YES;
            $userCookies = new UserCookies();
            $userCookies->setUser();
            $this->receiver_name = $userCookies->name;
            $this->buyer_name = $userCookies->name;
            $this->receiver_email = $userCookies->email;
            $this->buyer_email = $userCookies->email;
            $this->receiver_phone = $userCookies->phone;
            $this->buyer_phone = $userCookies->phone;
            $this->receiver_country_id = $userCookies->country_id;
//            $this->buyer_country_id = $userCookies->country_id;
            $this->buyer_province_id = $userCookies->province_id;
            $this->receiver_province_id = $userCookies->province_id;
            $this->receiver_district_id = $userCookies->district_id;
            $this->buyer_district_id = $userCookies->district_id;
            $this->receiver_address = $userCookies->address;
            $this->buyer_address = $userCookies->address;
        }
        if (!WeshopHelper::isEmpty($this->getBuyerAddress())) {
            $this->enable_buyer = self::NO;
            $buy_default = $this->getBuyerAddress();
            $this->buyer_address_id = $buy_default->id;
            $this->buyer_district_id = $buy_default->district_id;
            $this->buyer_province_id = $buy_default->province_id;
            $this->buyer_name = $buy_default->last_name.' '.$buy_default->first_name;
            $this->buyer_phone = $buy_default->phone;
            $this->buyer_email = $buy_default->email;
            $this->buyer_address = $buy_default->address;
        }
        if (!WeshopHelper::isEmpty($this->getReceiverAddress())) {
            $this->enable_receiver = self::NO;
            $re_default = $this->getReceiverAddress()[0];
            $this->receiver_address_id = $re_default->id;
            $this->receiver_district_id = $re_default->district_id;
            $this->receiver_province_id = $re_default->province_id;
            $this->receiver_name = $re_default->last_name.' '.$re_default->first_name;
            $this->receiver_phone = $re_default->phone;
            $this->receiver_email = $re_default->email;
            $this->receiver_address = $re_default->address;
        }
    }

    private $_provinces;

    public function getProvinces()
    {
        if ($this->_provinces === null) {
            $this->_provinces = SystemStateProvince::select2Data($this->buyer_country_id);
        }
        return $this->_provinces;
    }
    public function getZipCodes()
    {
        $zipcodes = SystemZipcode::loadZipCode(101);
        $data = [];
        foreach ($zipcodes  as  $zipcode){
            $data[$zipcode['zip_code']] = $zipcode['label'];
        }
        return $data;
    }

    public function ensureReceiver()
    {
        if ((int)$this->other_receiver === false) {
            if($this->buyer_address_id !== null){
                $this->receiver_address_id = $this->buyer_address_id;
            }
            $this->receiver_name = $this->buyer_name;
            $this->receiver_phone = $this->buyer_phone;
            $this->receiver_address = $this->buyer_address;
            $this->receiver_post_code = $this->buyer_post_code;
            $this->receiver_district_id = $this->buyer_district_id;
            $this->setReceiverDistrictName($this->getBuyerDistrictName());
            $this->receiver_province_id = $this->buyer_province_id;
            $this->setBuyerProvinceName($this->getBuyerDistrictName());
            $this->receiver_country_id = $this->buyer_country_id;
        }
    }

    private $_buyerDistrictName;

    public function getBuyerDistrictName()
    {
        if ($this->_buyerDistrictName === null && $this->buyer_district_id != 0) {
            $this->_buyerDistrictName = $this->createQuery('name', SystemDistrict::className(), ['id' => $this->buyer_district_id]);
        }
        return $this->_buyerDistrictName;
    }

    public function setBuyerDistrictName($name)
    {
        $this->_buyerDistrictName = $name;
    }

    private $_receiverDistrictName;

    public function getReceiverDistrictName()
    {
        if ($this->_receiverDistrictName === null && $this->receiver_district_id != 0) {
            $this->_receiverDistrictName = $this->createQuery('name', SystemDistrict::className(), ['id' => $this->receiver_district_id]);
        }
        return $this->_receiverDistrictName;
    }

    public function setReceiverDistrictName($name)
    {
        $this->_receiverDistrictName = $name;
    }

    private $_buyerProvinceName;

    public function getBuyerProvinceName()
    {
        if ($this->_buyerProvinceName === null && $this->buyer_province_id != 0) {
            $this->_buyerProvinceName = $this->createQuery('name', SystemStateProvince::className(), ['id' => $this->buyer_province_id]);
        }
        return $this->_buyerProvinceName;
    }

    public function setBuyerProvinceName($name)
    {
        $this->_buyerProvinceName = $name;
    }

    private $_receiverProvinceName;

    public function getReceiverProvinceName()
    {
        if ($this->_receiverProvinceName === null && $this->receiver_province_id != 0) {
            $this->_receiverProvinceName = $this->createQuery('name', SystemStateProvince::className(), ['id' => $this->receiver_province_id]);
        }
        return $this->_receiverProvinceName;
    }

    public function setReceiverProvinceName($name)
    {
        $this->_receiverProvinceName = $name;
    }

    private function createQuery($selectColumn, $refClass, $condition)
    {
        /** @var  $class yii\db\ActiveRecord */
        $class = $refClass;
        $q = new Query();
        $q->from(['q' => $class::tableName()]);
        $q->select("[[$selectColumn]]");
        $q->where($condition);
        return $q->column($class::getDb())[0];
    }
}