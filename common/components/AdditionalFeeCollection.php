<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-03-07
 * Time: 15:59
 */

namespace common\components;


use common\models\StoreAdditionalFee;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class AdditionalFeeCollection
 * @package common\components
 */
class AdditionalFeeCollection extends ArrayCollection
{
    use StoreAdditionalFeeRegisterTrait;

    /**
     * cái gì
     * @var
     */
    protected $owner = null;

    /**
     * @param $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        $this->loadFormOwner($owner);
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function loadFormOwner($owner)
    {
        $this->owner = $owner;
        if ($owner instanceof ActiveRecord) {
            $tableName = $owner::tableName();
            $ownerClass = get_class($owner);
            $ownerId = $owner->getPrimaryKey(false);
            $query = new Query();
            $query->select(['c.id', 'c.type_fee', 'c.amount', 'c.amount_local', 'c.currency', 'c.discount_amount']);
            $query->from(['c' => 'order_fee']);
            $query->where(['and', ['c.' . 'product_id' => $ownerId]]);
            $additionalFees = $query->all($ownerClass::getDb());
            Yii::info($additionalFees,'loadFormOwner');
            $this->mset($additionalFees,false,false);
        }
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->toArray());
    }

    public function mget($keys = null, $except = [])
    {
        if ($keys === null) {
            $keys = array_keys($this->storeAdditionalFee);
        }
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $results = [];

        foreach ($keys as $key) {
            if (in_array($key, $except)) {
                continue;
            }
            $results[$key] = $this->get($key, [], false);
        }
        return $results;
    }

    public function mset($values, $withCondition = false, $ensureReadOnly = true)
    {
        if (is_array($values)) {
            $this->removeAll();
            foreach ($values as $key => $value) {
                if (is_array($value) && isset($value['type_fee']) && $this->hasStoreAdditionalFeeByKey($value['type_fee'])) {
                    parent::set($value['type_fee'], $value);
                } elseif (!is_array($value) && is_string($key) && isset($this->storeAdditionalFee[$key]) && ($storeAdditionalFee = $this->storeAdditionalFee[$key]) !== null && $storeAdditionalFee instanceof StoreAdditionalFee) {
                    if ($ensureReadOnly && $storeAdditionalFee->is_read_only) {
                        continue;
                    }
                    $this->set($key, $value, $withCondition);
                } else {
                    Yii::warning("failed when set unknown additional fee '$key'", __METHOD__);
                }
            }
            if ($ensureReadOnly) {
                $breaks = $this->keys();
                foreach ($this->storeAdditionalFee as $name => $storeAdditionalFee) {
                    /** @var $storeAdditionalFee StoreAdditionalFee */
                    if (in_array($name, $breaks)) {
                        continue;
                    }
                    $this->set($name, $storeAdditionalFee->fee_rate, $withCondition);
                }
            }
        }
    }

    public function has($key)
    {
        return parent::has($key);
    }

    public function set($key, $value, $withCondition = false)
    {

        if (($storeAdditionalFee = $this->getStoreAdditionalFeeByKey($key)) !== null && $storeAdditionalFee instanceof StoreAdditionalFee) {
            if (is_array($value) && count($value) === 5 && isset($value['amount']) && isset($value['amount_local'])) {
               parent::set($key, $value);
            } else {
                $localValue = $value;
                /** @var $owner AdditionalFeeInterface|null|\yii\db\ActiveRecord */
                if (($owner = $this->getOwner()) === null) {
                    $withCondition = false;
                }
                if ($withCondition && $owner instanceof AdditionalFeeInterface && $storeAdditionalFee->hasMethod('executeCondition')) {
                    $value = $storeAdditionalFee->executeCondition($value, $owner);
                    if ($storeAdditionalFee->is_convert) {
                        $localValue = $value * $owner->getExchangeRate();
                    }
                }

                $additionalFee = [
                    'type_fee' => $key,
                    'amount' => $value,
                    'amount_local' => $localValue,
                    'currency' => $storeAdditionalFee->currency,
                    'discount_amount' => $owner !== null && $owner->hasProperty('discount_amount') ? $owner->discount_amount : 0,
                ];
                parent::set($key, $additionalFee);
            }
        } else {
            Yii::warning("failed when set unknown additional fee '$key'", __METHOD__);
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasStoreAdditionalFeeByKey($key)
    {
        return isset($this->storeAdditionalFee[$key]);
    }

    /**
     * @param $key
     * @param null $default
     * @return StoreAdditionalFee|null
     */
    public function getStoreAdditionalFeeByKey($key, $default = null)
    {
        return isset($this->storeAdditionalFee[$key]) ? $this->storeAdditionalFee[$key] : $default;
    }


    /**
     * @param null $names
     * @param array $except
     * @return array
     */
    public function getTotalAdditionFees($names = null, $except = [])
    {
        $totalFees = 0;
        $totalLocalFees = 0;
        foreach ((array)$this->mget($names) as $name => $array) {
            if (in_array($name, $except)) {
                continue;
            }
            $totalFees += isset($array['amount']) ? $array['amount'] : 0;
            $totalLocalFees += isset($array['amount_local']) ? $array['amount_local'] : 0;
        }
        return [$totalFees, $totalLocalFees];

    }

}