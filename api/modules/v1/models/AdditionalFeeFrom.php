<?php


namespace api\modules\v1\models;

use common\additional\AdditionalFeeCollection;
use common\additional\AdditionalFeeInterface;
use common\components\db\ActiveRecord;
use common\components\GetUserIdentityTrait;
use common\components\InternationalShippingCalculator;
use common\components\PickUpWareHouseTrait;
use common\components\UserCookies;
use common\helpers\WeshopHelper;
use common\models\Order;
use common\models\Product;
use common\models\Store;
use common\models\User;
use common\modelsMongo\ActiveRecordUpdateLog;
use common\products\BaseProduct;
use common\products\forms\ProductDetailFrom;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class AdditionalFeeFrom extends Model implements AdditionalFeeInterface
{

    /**
     * @var string target name order/product
     */
    public $target_name;
    /**
     * @var string|integer
     */
    public $target_id;
    /**
     * @var string|integer
     */
    public $store_id;
    /**
     * @var null|string|integer
     */
    public $customer_id;

    /**
     * @var string type of target (ebay/amazon)
     */
    public $item_type;

    /**
     * Todo new Target 'gate'
     * @var string
     */
    public $item_id;

    /**
     * Todo new Target 'gate'
     * @var string
     */
    public $item_sku;

    /**
     * @var string
     */
    public $item_seller;
    /**
     * @var integer
     */
    public $shipping_weight;
    /**
     * @var integer
     */
    public $shipping_quantity;
    /**
     * @var null|string|integer
     */
    public $province;
    /**
     * @var null|string|integer
     */
    public $district;

    /**
     * @var string
     */
    public $post_code;

    /**
     * @var float
     */
    public $us_amount;
    /**
     * @var float
     */
    public $us_tax;
    /**
     * @var float
     */
    public $us_ship;
    /**
     * @var float
     */
    public $custom_fee;

    /**
     * @var bool
     */
    public $accept_insurance = 'N';

    public $is_special;

    public function attributes()
    {
        return ArrayHelper::merge(parent::attributes(), [
            'target_name', 'target_id', 'store_id', 'customer_id', 'custom_fee', 'item_type', 'item_id', 'item_sku',
            'province', 'district', 'post_code',
            'item_seller', 'shipping_weight', 'shipping_quantity', 'us_amount', 'us_tax', 'us_ship',
            'accept_insurance', 'is_special'
        ]);
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['target_name', 'target_id', 'store_id'], 'required'],
            [['target_id', 'store_id', 'customer_id', 'shipping_weight', 'shipping_quantity'], 'integer'],
            [['target_id', 'store_id', 'customer_id', 'shipping_weight', 'shipping_quantity'], 'filter', 'filter' => function ($value) {
                return (integer)$value;
            }],
            [['us_amount', 'us_tax', 'us_ship', 'custom_fee'], 'number'],
            [['target', 'item_type', 'item_id', 'item_sku', 'accept_insurance', 'item_seller', 'province', 'district', 'post_code', 'is_special'], 'string'],
            [['province', 'district'], 'filter', 'filter' => function ($value) {
                return (integer)$value;
            }],
        ]);
    }

    public function load($data, $formName = null)
    {
        return parent::load($data, $formName);
    }

    /** @var \common\components\db\ActiveRecord */
    private $_target;

    /**
     * @return \common\components\db\ActiveRecord
     */
    public function getTarget()
    {
        if (!$this->_target) {
            if ($this->target_name === 'gate') {
                $form = new ProductDetailFrom();
                $form->type = $this->item_type;
                $form->id = $this->item_id;
                $form->sku = $this->item_sku;
                $form->quantity = $this->getShippingQuantity();
                if (($product = $form->detail()) !== false) {
                    if ($this->item_seller !== null && $this->item_seller !== '') {
                        $product->updateBySeller($this->item_seller);
                    }
                    $this->_target = $product;
                }
            } else {
                $condition = ['id' => $this->target_id];
                $class = Product::className();
                if ($this->target_name == 'order') {
                    $condition = ['ordercode' => $this->target_id];
                    $class = Order::className();
                }
                if (($target = $class::findOne($condition)) !== null) {
                    ActiveRecordUpdateLog::register('beforeConfirm', $target);
                    $this->_target = $target;
                }

                if ($this->_target instanceof Order) {
                    $this->store_id = $this->_target->store_id;
                    $this->province = $this->_target->receiver_province_id;
                    $this->district = $this->_target->receiver_district_id;
                    $this->post_code = $this->_target->receiver_post_code ? $this->_target->receiver_post_code : '';
                } else if ($this->_target instanceof Product) {
                    $order = $this->_target->order;
                    $this->store_id = $order->store_id;
                    $this->province = $order->receiver_province_id;
                    $this->district = $order->receiver_district_id;
                    $this->post_code = $order->receiver_post_code ? $order->receiver_post_code : '';
                }
            }

        }
        return $this->_target;

    }

    private $_additionalFees;

    public function getAdditionalFees()
    {
        if ($this->_additionalFees === null) {
            $this->_additionalFees = new AdditionalFeeCollectionCustom();
            $this->_additionalFees->storeId = $this->store_id;
            $this->_additionalFees->userId = $this->customer_id;
            $this->_additionalFees->removeAll();
            $hasChange = false;

            $oldUsAmount = 0;
            $orderCode = null;
            if (($target = $this->getTarget()) !== null && $target instanceof ActiveRecord) {
                $this->_additionalFees->loadFormActiveRecord($this->getTarget(), $this->target_name);
                if ($target instanceof Order) {
                    $oldUsAmount = $target->total_price_amount_origin;
                    $orderCode = $target->ordercode;
                    $this->_additionalFees->setExRate($target->exchange_rate_fee);

                } elseif ($target instanceof Product) {
                    $orderCode = $target->order->ordercode;
                    $oldUsAmount = $target->total_final_amount_origin;
                    $this->_additionalFees->setExRate($target->order->exchange_rate_fee);
                }
            } elseif ($target instanceof BaseProduct) {
                $this->_additionalFees->fromArray($target->getAdditionalFees()->toArray());
            }

            $usAmount = $this->us_amount;
            $this->shipping_quantity = ($this->shipping_quantity !== null && $this->shipping_quantity !== '' && (int)$this->shipping_quantity > 0) ? $this->shipping_quantity : 1;
            Yii::info($usAmount && $usAmount !== '', (int)$this->shipping_quantity);
            if ($usAmount && $usAmount !== '') {

                $usAmount *= (int)$this->shipping_quantity;
            }
            if ($usAmount !== null && $usAmount !== '' && !WeshopHelper::compareValue($usAmount, $this->_additionalFees->getTotalAdditionalFees('product_price')[0])) {
                $this->_additionalFees->remove('product_price');
                $this->_additionalFees->withCondition($this, 'product_price', floatval($usAmount));
                $hasChange = true;
            }

            if ($this->us_ship !== null && $this->us_ship !== '' && !WeshopHelper::compareValue($this->us_ship, $this->_additionalFees->getTotalAdditionalFees('shipping_fee')[0])) {
                $this->_additionalFees->remove('shipping_fee');
                $this->_additionalFees->withCondition($this, 'shipping_fee', floatval($this->us_ship));
                $hasChange = true;
            }

            if ($this->us_tax !== null && $this->us_tax !== '' && !WeshopHelper::compareValue($this->us_tax, $this->_additionalFees->getTotalAdditionalFees('tax_fee')[0])) {
                $this->_additionalFees->remove('tax_fee');
                $this->_additionalFees->withCondition($this, 'tax_fee', floatval($this->us_tax));
                $hasChange = true;
            }

            if ($this->custom_fee !== null && $this->custom_fee !== '' && !WeshopHelper::compareValue($this->custom_fee, $this->_additionalFees->getTotalAdditionalFees('custom_fee')[0])) {
                $this->_additionalFees->remove('custom_fee');
                $this->_additionalFees->withCondition($this, 'custom_fee', floatval($this->custom_fee));
            }


            if ($hasChange) {

//                if ($oldUsAmount > 0 && WeshopHelper::compareValue($totalOrigin, $oldUsAmount, 'float')) {
//                    if (($currentPurchaseFeeAmount = $this->_additionalFees->getTotalAdditionalFees('purchase_fee')[0]) > 0){
//                        $convertPercent = round($currentPurchaseFeeAmount / $oldUsAmount);
//                        Yii::info($convertPercent,'$convertPercent');
//                    }
//                }
                $this->_additionalFees->remove('purchase_fee');
                if ($orderCode !== null && ArrayHelper::isIn($orderCode, $this->getBlackOrderCodeLists())) {
                    $amount = 0.1 *  $this->_additionalFees->getTotalOrigin();
                    $amountLocal = $this->_additionalFees->getStoreManager()->roundMoney($amount *  $this->_additionalFees->getStoreManager()->getExchangeRate());
                    if (($config =  $this->_additionalFees->getStoreAdditionalFeeByKey('purchase_fee')) !== null) {
                        $this->_additionalFees->set('purchase_fee', $this->_additionalFees->createItemParam($config, $amount, $amountLocal));
                    }
                } else {
                    $this->_additionalFees->withCondition($this, 'purchase_fee', null);
                }


            }

            if ($this->is_special === 'yes' && ($couriers = $this->getCalculateFee($this->_additionalFees->storeManager->store, true)) !== []) {
                $firstCourier = $couriers[0];
                Yii::info($couriers, '$couriers');
                $this->getAdditionalFees()->remove('special_fee');
                $this->getAdditionalFees()->withCondition($this, 'special_fee', $firstCourier['special_fee']);
            } elseif ($this->is_special === 'no' && $this->_additionalFees->has('special_fee')) {
                $specialFees = $this->_additionalFees->get('special_fee', [], false);
                $this->_additionalFees->remove('special_fee');
                foreach ($specialFees as $specialFee) {
                    $specialFee['amount'] = 0;
                    $specialFee['local_amount'] = 0;
                    $this->_additionalFees->add('special_fee', $specialFee);
                }

            }


        }
        return $this->_additionalFees;
    }


    /**
     * @inheritDoc
     */
    public function getUniqueCode()
    {
        return $this->store_id;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        /** @var $target Product|Order */
        return ($target = $this->getTarget() !== null) ? $target->portal : 'ebay';
    }

    /**
     * @inheritDoc
     */
    public function getTotalOrigin()
    {
        return $this->getAdditionalFees()->getTotalOrigin();
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return null;
    }


    private $_user;

    public function getUser()
    {
        if ($this->_user === null && ($this->customer_id !== null && $this->customer_id !== '')) {
            $this->_user = User::findOne($this->customer_id);
        }
        return $this->_user;
    }

    /**
     * @return string
     */
    public function getUserLevel()
    {
        if (($user = $this->getUser()) === null) {
            return User::LEVEL_NORMAL;
        }
        return $user->getUserLevel();
    }

    /**
     * @inheritDoc
     */
    public function getIsNew()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getIsSpecial()
    {

        return $this->is_special === 'yes';
    }

    /**
     * @inheritDoc
     */
    public function getShippingWeight()
    {
        return $this->shipping_weight;
    }

    /**
     * @inheritDoc
     */
    public function getShippingQuantity()
    {
        return $this->shipping_quantity;
    }

    private $_couriers = [];

    public function getCalculateFee($store, $refresh = false)
    {
        if ((empty($this->_couriers) || $refresh) && !empty($this->getShippingParams())) {

            $location = InternationalShippingCalculator::LOCATION_AMAZON;
            if (($target = $this->getTarget()) instanceof BaseProduct && $target->type === BaseProduct::TYPE_EBAY) {
                $location = InternationalShippingCalculator::LOCATION_EBAY_US;
                $currentSeller = $target->getCurrentProvider();
                if (strtoupper($currentSeller->country_code) !== 'US') {
                    $location = InternationalShippingCalculator::LOCATION_EBAY;
                }
            }
            $calculator = new InternationalShippingCalculator();
            $calculator->action_log = __METHOD__;
            list($ok, $couriers) = $calculator->CalculateFee($this->getShippingParams(), ArrayHelper::getValue($this->getPickUpWareHouse(), 'ref_user_id'), $store->country_code, $store->currency, $location);
            if ($ok && is_array($couriers) && count($couriers) > 0) {
                $this->_couriers = $couriers;
            }

        }
        return $this->_couriers;
    }

    /**
     * @return integer
     */
    public function getExchangeRate()
    {
        return $this->getAdditionalFees()->getStoreManager()->getExchangeRate();
    }

    public function getShippingParams()
    {
        if (($target = $this->getTarget()) === null || ($wh = $this->getPickUpWareHouse()) === null) {
            return [];
        }
        $parcel = [];
        $weight = 0;
        $totalAmount = 0;
        if ($target instanceof Order) {
            $totalAmount = $target->total_amount_local;
            $items = [];
            foreach ($target->products as $product) {
                $itemWeight = (int)$product->total_weight_temporary * 1000;
                if ($itemWeight <= 0) {
                    $itemWeight = $target->store_id === 1 ? 500 : 1000;
                }
                $weight += $itemWeight;
                $items[] = [
                    'sku' => implode('|', [$product->parent_sku, $product->sku]),
                    'label_code' => '',
                    'origin_country' => '',
                    'name' => $product->product_name,
                    'desciption' => '',
                    'weight' => WeshopHelper::roundNumber(($itemWeight / $product->quantity_customer)),
                    'amount' => WeshopHelper::roundNumber($product->total_price_amount_local),
                    'quantity' => $product->quantity_customer,
                ];
            }
            $parcel = [
                'weight' => $weight,
                'amount' => $totalAmount,
                'description' => $target->seller ? "order of seller `{$target->seller->seller_name}`" : "",
                'items' => $items
            ];
        } else if ($target instanceof Product) {
            $weight = $target->total_weight_temporary * 1000;
            $totalAmount = $target->total_price_amount_local;

            $sku = [$target->parent_sku];
            if ($target->sku !== null) {
                $sku[] = $target->sku;
            }
            $sku = count($sku) > 1 ? implode('|', $sku) : $sku[0];
            $parcel = [
                'weight' => $weight,
                'amount' => $totalAmount,
                'description' => "product $sku",
                'items' => [
                    [
                        'sku' => $sku,
                        'label_code' => '',
                        'origin_country' => '',
                        'name' => $target->product_name,
                        'desciption' => '',
                        'weight' => WeshopHelper::roundNumber(($weight / $target->quantity_customer)),
                        'amount' => WeshopHelper::roundNumber($target->total_price_amount_local),
                        'quantity' => $target->quantity_customer,
                    ]
                ]
            ];
        } else if ($target instanceof BaseProduct) {
            $weight = $target->getShippingWeight() * 1000;
            $totalAmount = $target->getLocalizeTotalPrice();
            $parcel = [
                'weight' => $weight,
                'amount' => $totalAmount,
                'description' => "{$target->type} {$target->getUniqueCode()}",
                'items' => [
                    [
                        'sku' => $target->getUniqueCode(),
                        'label_code' => '',
                        'origin_country' => '',
                        'name' => $target->item_name,
                        'desciption' => '',
                        'weight' => WeshopHelper::roundNumber(($weight / $target->getShippingQuantity())),
                        'amount' => WeshopHelper::roundNumber($totalAmount),
                        'quantity' => $target->getShippingQuantity(),
                    ]
                ]
            ];
        }
        if (
            ($pickUpId = ArrayHelper::getValue($wh, 'ref_pickup_id')) === null ||
            ($userId = ArrayHelper::getValue($wh, 'ref_user_id')) === null ||
            empty($parcel) ||
            $weight === 0 ||
            $totalAmount === 0
        ) {
            return [];
        }
        $store = $this->getAdditionalFees()->storeManager->store;
        $shipTo = ArrayHelper::merge([
            'contact_name' => 'ws calculator',
            'company_name' => '',
            'email' => '',
            'address' => 'ws auto',
            'address2' => '',
            'phone' => '0987654321',
            'phone2' => '',
        ], $this->getDefaultTo($store));
        $params = [
            'config' => [
                'insurance' => $this->accept_insurance,
                'include_special_goods' => $this->getIsSpecial() ? 'Y' : 'N'
            ],
            'ship_from' => [
                'country' => 'US',
                'pickup_id' => $pickUpId
            ],
            'ship_to' => $shipTo,
            'shipments' => [
                'content' => '',
                'total_parcel' => 1,
                'total_amount' => $totalAmount,
                'description' => '',
                'amz_shipment_id' => '',
                'chargeable_weight' => $weight,
                'parcels' => [$parcel]
            ],
        ];
        return $params;
    }

    /**
     * @param $store Store
     * @return array
     */
    private function getDefaultTo($store)
    {
        return [
            'province' => $this->province !== null ? $this->province : ($store->country_code === 'ID' ? 3464 : 1),
            'district' => $this->district !== null ? $this->district : ($store->country_code === 'ID' ? 28444 : 8),
            'country' => $store->country_code,
            'zipcode' => $store->country_code === 'ID' ? ($this->post_code !== null ? $this->post_code : '14340') : '',
        ];
    }

    public function calculator()
    {
        $store = $this->getAdditionalFees()->getStoreManager()->store;
        if (($couriers = $this->getCalculateFee($store, true)) !== null) {
            $firstCourier = $couriers[0];
            $this->getAdditionalFees()->remove('international_shipping_fee');
            $this->getAdditionalFees()->remove('insurance_fee');
            $this->getAdditionalFees()->withCondition($this, 'international_shipping_fee', $firstCourier['total_fee']);
            $this->getAdditionalFees()->withCondition($this, 'insurance_fee', $firstCourier['insurance_fee']);
            if ($this->getIsSpecial()) {
                $this->getAdditionalFees()->remove('special_fee');
                $this->getAdditionalFees()->withCondition($this, 'special_fee', $firstCourier['special_fee']);
            }
        }

        return [
            'store' => $store->name,
            'target_name' => $this->target_name,
            'target_identity' => $this->target_id,
            'shipping_weight' => $this->shipping_weight,
            'shipping_quantity' => $this->shipping_quantity,
            'exchange' => $this->getExchangeRate(),
            'ship_from' => $this->getPickUpWareHouse(),
            'ship_to' => $this->getDefaultTo($store),
            'additional_fees' => $this->getAdditionalFees()->toArray(),
            'couriers' => $this->_couriers
        ];
    }

    public function getPickUpWareHouse($store = null)
    {
        if ($store === null) {
            $store = $this->store_id;
            if ($store === null) {
                $store = $this->getAdditionalFees()->storeId;
            }
        }
        $user = $this->getUser();
        if ($user !== null && method_exists($user, 'getPickupWarehouse') && ($wh = call_user_func([$user, 'getPickupWarehouse'])) !== null) {
            return $wh;
        } elseif (($params = ArrayHelper::getValue(Yii::$app->params, 'pickupUSWHGlobal')) !== null) {
            $current = $params['default'];

            $current = $store !== null ? ($store === 1 ? (strpos($current, 'sandbox') !== false ? 'sandbox_vn' : 'ws_vn') : (strpos($current, 'sandbox') !== false ? 'sandbox_id' : 'ws_id')) : $current;

            return ArrayHelper::getValue($params, "warehouses.$current", false);
        }
        return null;
    }

    public function getBlackOrderCodeLists()
    {
        return [
            '171867', '170745', '149158', '126376', '117934', '900655', '865421', '835038',
            '830340', '821982', '808948', '763754', '762007', '754847', '740648', '738552',
            '736381', '735582', '734207', 'VN4867B4', 'ID4815B5', 'VN4767B2', 'VN4730B6',
            'VN4693B5',
        ];

    }
}
