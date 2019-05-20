<?php


namespace frontend\modules\payment;;

use common\helpers\WeshopHelper;
use common\models\PaymentMethodProvider;
use Yii;
use common\models\PaymentProvider;

class PaymentService
{

    public static function loadPaymentByStoreFromDb($store,$provider_id = 42)
    {
        $query = PaymentProvider::find();
        $query->with('paymentMethodProviders', 'paymentMethodProviders.paymentMethod', 'paymentMethodProviders.paymentMethod.paymentMethodBanks', 'paymentMethodProviders.paymentMethod.paymentMethodBanks.paymentBank');
        $query->where([
            'AND',
            ['store_id' => $store],
            ['id' => $provider_id],
            ['status' => 1]
        ]);
        return $query->asArray()->all();
    }

    /**
     * @param $provider
     * @param $method
     * @return PaymentMethodProvider|null
     */
    public static function getMethodProvider($provider, $method)
    {
        return PaymentMethodProvider::find()
            ->with(['paymentProvider', 'paymentMethod'])
            ->where([
                'AND',
                ['payment_provider_id' => $provider],
                ['payment_method_id' => $method],
            ])
            ->one();
    }

    public static function getGroupName($group)
    {
        switch ((int)$group) {
            case Payment::PAYMENT_GROUP_MASTER_VISA:
                return 'Credit Card';
            case Payment::PAYMENT_GROUP_BANK:
                return 'Bank Transfer';
            case Payment::PAYMENT_GROUP_NL_WALLET:
                return 'NganLuong E-Wallet';
            case Payment::PAYMENT_GROUP_WSVP:
                return 'Over WeShop\'s counter';
            case Payment::PAYMENT_GROUP_WS_WALLET:
                return 'WeShop E-Wallet';
            case Payment::PAYMENT_GROUP_COD:
                return 'COD';
            case Payment::PAYMENT_GROUP_DRAGON:
                return 'Dragon Pay';
            case Payment::PAYMENT_GROUP_PAYNAMIC:
                return 'Paynamic';
            case Payment::PAYMENT_GROUP_MOLMY:
                return 'MOL';
            case Payment::PAYMENT_GROUP_C2P2:
                return 'C2P2 Account';
            case Payment::PAYMENT_GROUP_ALIPAY_INSTALMENT:
                return 'Thanh toán trả góp';
            case Payment::PAYMENT_GROUP_MANDIRI_INSTALMENT:
                return 'Cicilan Bank';
            case Payment::PAYMENT_GROUP_MCPAY:
                return 'Kartu Kredit';
            case Payment::PAYMENT_GROUP_WEPAY:
                return 'Wepay';
            case Payment::PAYMENT_GROUP_DOKU:
                return 'Doku';
            case Payment::PAYMENT_GROUP_NL_QRCODE:
                return 'QR Code';
            case Payment::PAYMENT_GROUP_MY_BANK_TRANSFER:
                return 'Bank Transfer';
            default:
                return 'Unknown';
        }
    }

    public static function createCheckPromotionParam(Payment $payment)
    {
        $items = $payment->getOrders();
        if (empty($items)) {
            return [];
        }
        $orders = [];
        foreach ($items as $idx => $order) {
            $item = [];
            $item['itemType'] = strtolower($order['portal']);
            $item['shippingWeight'] = self::toNumber($order['total_weight_temporary']);
            $item['shippingQuantity'] = self::toNumber($order['total_quantity']);
            $item['totalAmount'] = self::toNumber($order['totalAmount']);
            if (count($order['products']) === 0) {
                continue;
            }
            $products = [];
            foreach ($order['products'] as $pid => $product) {
                $pItem = [];
                $pItem['itemType'] = strtolower($product['portal']);
                $pItem['shippingWeight'] = self::toNumber($product['total_weight_temporary']);
                $pItem['shippingQuantity'] = self::toNumber($product['quantity_customer']);
                $pItem['categoryId'] = isset($product['category']['alias']) ? self::toNumber($product['category']['alias']) : null;
                $pItem['totalAmount'] = self::toNumber($product['total_price_amount_local']);
                if (count($product['fees']) === 0) {
                    continue;
                }
                $fees = [];
                foreach ($product['fees'] as $key => $fee) {
                    $fees[$key] = isset($fee['local_amount']) ? self::toNumber($fee['local_amount']) : 0;
                }
                $pItem['additionalFees'] = $fees;
                $products[$pid] = $pItem;
            }
            $item['products'] = $products;
            $orders[$idx] = $item;
        }
        return [
            'couponCode' => $payment->coupon_code,
            'paymentService' => implode('_', [$payment->payment_method, $payment->payment_bank_code]),
            'totalAmount' => $payment->total_amount,
            'customerId' => Yii::$app->getUser()->getId(),
            'orders' => $orders
        ];
    }

    public static function toNumber($value)
    {
        return (integer)$value;
    }

    public static function generateTransactionCode($prefix = 'PM')
    {
        return WeshopHelper::generateTag(time(), 'PM', 16);
    }

}