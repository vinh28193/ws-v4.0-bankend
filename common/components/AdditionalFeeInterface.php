<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-02-18
 * Time: 10:19
 */

namespace common\components;

/**
 * Interface AdditionalFeeInterface
 *
 */

interface AdditionalFeeInterface
{

    /**
     * @return \common\components\StoreManager
     */
    public function getStoreManager();

    /**
     * @return string
     */
    public function getItemType();

    /**
     * @return integer
     */
    public function getTotalOriginPrice();

    /**
     * @return mixed
     */
    public function getCustomCategory();

    /**
     * @return integer
     */
    public function getShippingWeight();
    /**
     * @return integer
     */
    public function getShippingQuantity();
    /**
     * @return boolean
     */
    public function getIsForWholeSale();
    /**
     * @return integer
     */
    public function getExchangeRate();

    /**
     * @return mixed
     */
    public function getStoreAdditionalFee();

    /**
     * @param null $names
     * @param array $except
     * @return mixed
     */
    public function getAdditionalFees($names = null, $except = []);

    /**
     * @param $values
     * @return mixed
     */
    public function setAdditionalFees($values);
}
