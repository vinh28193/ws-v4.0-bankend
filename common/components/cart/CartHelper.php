<?php


namespace common\components\cart;


use common\components\StoreManager;
use common\helpers\WeshopHelper;
use common\models\Order;
use common\models\Product;
use common\models\Seller;
use common\models\User;
use common\products\BaseProduct;
use common\products\Provider;
use common\products\VariationMapping;
use common\products\VariationOption;
use Yii;
use DateTime;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class CartHelper
{

    /**
     * @return CartManager
     */
    public static function getCartManager()
    {
        return Yii::$app->cart;
    }

    public static function mapCartKeys($items)
    {
        $keys = ArrayHelper::map($items, function ($item) {
            return (string)$item['_id'];
        }, 'key.products');
        return array_map(function ($key) {
            return array_map(function ($e) {
                return [
                    'id' => $e['id'],
                    'sku' => $e['sku']
                ];
            }, $key);
        }, $keys);

    }

    /**
     * @param $item BaseProduct
     * @param $sellerId
     * @param $currentImage
     */
    public static function createItem($item, $sellerId = null, $currentImage = null)
    {
        $postt = Yii::$app->request->post();
        /** @var  $user  User */
        $user = Yii::$app->user->identity;
        /** @var  $storeManager StoreManager */
        $storeManager = Yii::$app->storeManager;
        $order = [];
        $order['type_order'] = Order::TYPE_SHOP;
        $order['ordercode'] = null;
        if (isset($postt['link_payment'])) {
            $order['link_payment'] = $postt['link_payment'];
        }
        $order['portal'] = $item->type;
        $order['current_status'] = Order::STATUS_NEW;
        $order['new'] = Yii::$app->getFormatter()->asTimestamp('now');
        $order['mark_supporting'] = null;
        $order['supporting'] = null;
        $order['supported'] = null;
        $order['cancelled'] = null;
        $order['customer_type'] = 'Retail';
        $order['store_id'] = $storeManager->getId();
        $order['exchange_rate_fee'] = $storeManager->getExchangeRate();
        $order['sale_support_id'] = null;
        $order['support_email'] = null;
        $order['check_insurance'] = 0;
        $order['is_special'] = $item->getIsSpecial() ? 1 : 0;
//        $order['check_inspection'] = 0;
//        $order['check_inspection'] = 0;
//        $order['total_insurance_fee_local'] = 0; // b???o hi???m ???? m???c ?????nh k chuy???n
        $order['saleSupport'] = null;
        if (Yii::$app->user->getId()) {
            $order['potential'] = 1; // sale ??u ti??n ch??m ????n
        } else {
            $order['potential'] = 0; // kh??ch h??ng kh??ng d??ng nh???p
        }
        $order['customer_id'] = $user ? $user->id : null;

        $order['customer'] = $user ? [
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone
        ] : null;
        /* @var $provider Provider */
        $provider = $item->provider ? $item->provider : $item->getCurrentProvider($sellerId);

//        if ($sellerId) {
//            foreach ((array)$item->providers as $pro) {
//                /* @var $pro Provider */
//                if ($pro->name === $sellerId) {
//                    $provider = $pro;
//                    break;
//                }
//            }
//        }
        if ($provider) {
            $order['seller'] = [
                'seller_name' => $provider->name,
                'portal' => $item->type,
                'seller_store_rate' => $provider->rating_score,
                'seller_link_store' => $provider->website,
                'location' => $provider->location,
                'country_code' => $provider->country_code
            ];
        }
        $product = [];
        $product['portal'] = $item->type;
        $product['sku'] = $item->item_sku;
        $product['parent_sku'] = $item->item_id;
        $product['link_img'] = $currentImage !== null ? $currentImage : ($item->current_image !== null ? $item->current_image : $product->primary_images[0]->main);
        $product['link_origin'] = $item->item_origin_url;
        $product['remove'] = 0;
        $product['condition'] = $item->condition;
        $product['is_special'] = $item->getIsSpecial() ? 1 : 0;
        $variations = [];
        if (strtolower($item->type) === 'ebay') {
            foreach ((array)$item->variation_mapping as $v) {
                /** @var $v VariationMapping */
                if ($v->variation_sku === $item->item_sku) {

                    $specific = [];
                    foreach ($v->options_group as $option) {
                        $specific = array_merge($specific, [$option->name => $option->value]);
                    }
                    $variations = $specific;
                    break;
                }
            }
        } else {
            $specific = [];
            foreach ($item->variation_options as $variation_option) {
                /** @var $variation_option VariationOption */
                if ($variation_option->option_link && !empty($variation_option->sku)) {
                    $specific = array_merge($specific, [$variation_option->name => $variation_option->value_current]);
                }
            }
            $variations = $specific;
        }

        $product['variations'] = $variations;

        $product['available_quantity'] = $item->available_quantity;
        $product['quantity_sold'] = $item->quantity_sold;

        $product['product_link'] = $item->ws_link;
        $product['product_name'] = $item->item_name;
        $product['quantity_customer'] = $item->getShippingQuantity();
        $product['total_weight_temporary'] = $item->getShippingWeight();

        $product['is_special'] = $item->getIsSpecial() ? 1 : 0;

        $product['category'] = [
            'alias' => $item->category_id,
            'site' => $item->type,
            'origin_name' => ArrayHelper::getValue($item, 'category_name', 'Unknown'),
        ];
        $additionalFees = $item->getAdditionalFees();
        $productPrice = $additionalFees->getTotalAdditionalFees('product_price');
        // T???ng ti???n c??c ph??, tr??? ti???n g???c s???n ph???m (ch??? c?? c??c ph??)
        $product['total_fee_product_local'] = $additionalFees->getTotalAdditionalFees(['tax_fee', 'shipping_fee'])[1];         // T???ng Ph?? theo s???n ph???m
        // T???ng ti???n local g???c s???n ph???m (ch??? c?? ti???n g???c c???a s???n ph???m)
        list($product['price_amount_origin'], $product['price_amount_local']) = $productPrice;
        $product['price_amount_origin'] = $product['price_amount_origin'] / $item->getShippingQuantity();
        $product['price_amount_local'] = $product['price_amount_local'] / $item->getShippingQuantity();

        $product['total_price_amount_local'] = $productPrice[1];
        // T???ng ti???n local t???t t???n t???n
        list($product['total_final_amount_origin'], $product['total_final_amount_local']) = $additionalFees->getTotalAdditionalFees(['product_price', 'shipping_fee', 'tax_fee']);
        $productFees = [];
        $product['additionalFees'] = $additionalFees->toArray();
        foreach ($additionalFees->keys() as $feeName) {
            $fee = [];
            list($fee['amount'], $fee['local_amount']) = $additionalFees->getTotalAdditionalFees($feeName);
            if ($feeName === 'product_price') {
                // T???ng gi?? g???c c???a c??c s???n ph???m t???i n??i xu???t x???
                $order['total_price_amount_origin'] = $fee['amount'];
                $order['total_origin_fee_local'] = $fee['local_amount'];
            } elseif ($feeName === 'tax_fee') {
                // T???ng ph?? tax c???a c??c s???n ph???m t???i n??i xu???t x???
                $order['total_origin_tax_fee_amount'] = $fee['amount'];
                $order['total_origin_tax_fee_local'] = $fee['local_amount'];
            } elseif ($feeName === 'shipping_fee') {
                // T???ng ph?? tax c???a c??c s???n ph???m t???i n??i xu???t x???
                $order['total_intl_shipping_fee_amount'] = $fee['amount'];  // phis tax amount
                $order['total_origin_shipping_fee_local'] = $fee['local_amount'];
            } elseif ($feeName === 'purchase_fee') {
                // T???ng ph?? mua h???
                $order['total_weshop_fee_amount'] = $fee['amount'];
                $order['total_weshop_fee_local'] = $fee['local_amount'];
            } elseif ($feeName === 'international_shipping_fee') {
                // T???ng vat c???a c??c s???n ph???m
                $order['total_intl_shipping_fee_local'] = $fee['local_amount'];
            } elseif ($feeName === 'vat_fee') {
                // T???ng v???n chuy???n t???i local c???a c??c s???n ph???m
                $order['total_vat_amount_local'] = $fee['local_amount'];
            } else if ($feeName === 'delivery_fee') {
                $order['total_vat_amount_local'] = $fee['local_amount'];
            }
            // Ti???n Ph??
            $productFees[$feeName] = $fee;
        }

        // T???ng ti???n Discount
        $order['total_promotion_amount_local'] = 0;
        // T???ng ti???n paid
        $order['total_paid_amount_local'] = 0;
        // T???ng c??c ph?? c??c s???n ph???m (tr??? gi?? g???c t???i n??i xu???t x???)
        $order['total_fee_amount_local'] = $product['total_fee_product_local'];
        // T???ng ti???n (bao g???m ti???n gi?? g???c c???a c??c s???n ph???m v?? c??c lo???i ph??)
        $order['total_amount_local'] = $product['total_price_amount_local'];
        $order['total_final_amount_local'] = $order['total_amount_local'] + $order['total_fee_amount_local'];
        $order['total_weight_temporary'] = $product['total_weight_temporary'];
        $order['total_quantity'] = $product['quantity_customer'];
        $order['products'] = [$product];
        return $order;
    }


    public static function mergeItem($source, $target)
    {
        $start = microtime(true);
        $orders = func_get_args();
        $order = array_shift($orders);
        while (!empty($orders)) {
            foreach (array_shift($orders) as $key => $value) {

                if (strpos($key, 'total_') !== false) {
                    $oldValue = floatval($order[$key]);
                    $newValue = floatval($value);
                    $oldValue += $newValue;
                    $order[$key] = $oldValue;
                } elseif ($key === 'products') {
                    $products = $order['products'];
                    $products[] = reset($value);
                    $order['products'] = $products;
                } elseif ($key === 'is_special' && $value === 1) {
                    $order['is_special'] = 1;
                }
            }
        }
        $time = sprintf('%.3f', microtime(true) - $start);
        Yii::info("time: $time s", __METHOD__);
        return $order;
    }


    public static function createOrderParams($type, $keys, $uuid = null)
    {
        $start = microtime(true);
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $orders = [];
        $totalFinalAmount = 0;
        $items = self::getCartManager()->getItems($type, $keys, $uuid);
        foreach ($items as $item) {
            $order = $item['value'];
            $totalFinalAmount += (int)$order['total_amount_local'];
            $orders[] = $order;
        }
        $time = sprintf('%.3f', microtime(true) - $start);
        Yii::info("time: $time s", __METHOD__);

        return [
            'countKey' => count($keys),
            'countItem' => count($items),
            'orders' => $orders,
            'totalAmount' => $totalFinalAmount
        ];
    }

    public static function getTimeEndOfDay($value = 'now')
    {
        $dateTime = new DateTime($value);
        $dateTime->setTime(23, 59, 59);
        return Yii::$app->formatter->asDatetime($dateTime);
    }

    public static function beginSupportDay()
    {
        return self::getTimeEndOfDay('now - 1 days');
    }

    public static function endSupportDay()
    {
        return self::getTimeEndOfDay('now');
    }

    public static function getSupportAssign()
    {
        $userManager = Yii::$app->getAuthManager();
        $idSale = $userManager->getUserIdsByRole('sale');
        $idMasterSale = $userManager->getUserIdsByRole('master_sale');
        $query = new Query();
        $query->from(['u' => User::tableName()]);
        $query->select(['id', 'mail']);
        $query->where([
            'AND',
            ['remove' => 0],
            ['OR',
                ['id' => $idSale],
                ['id' => $idMasterSale]
            ]
        ]);
        $sales = $query->all(User::getDb());
    }
}