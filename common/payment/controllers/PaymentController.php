<?php


namespace common\payment\controllers;

use common\components\cart\CartHelper;
use common\components\cart\CartSelection;
use common\helpers\WeshopHelper;
use common\models\Address;
use common\models\Category;
use common\models\Order;
use common\models\PaymentTransaction;
use common\models\Product;
use common\models\ProductFee;
use common\models\Seller;
use common\products\BaseProduct;
use common\promotion\PromotionResponse;
use common\payment\models\ShippingForm;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Response;
use common\payment\Payment;

class PaymentController extends BasePaymentController
{


    public function actionProcess()
    {
        $start = microtime(true);
        $bodyParams = $this->request->bodyParams;
        if (($customer = $this->user) === null) {
            return $this->response(false, 'not login');
        }
        $payment = new Payment($bodyParams['payment']);
        $shippingForm = new ShippingForm($bodyParams['shipping']);
        $shippingForm->setDefaultValues(); // remove it get from POST pls
        $shippingForm->ensureReceiver();

        $items = [];
        foreach (CartSelection::getSelectedItems($payment->payment_type) as $key) {
            $items[] = $this->cartManager->getItem($key);
        }
        if (empty($items)) {
            return $this->response(false, 'empty cart');
        }
        if (count($bodyParams) !== 2 || !isset($bodyParams['payment']) || empty($bodyParams['payment']) || !isset($bodyParams['shipping']) || empty($bodyParams['shipping'])) {
            return $this->response(false, 'invalid parameter');
        }

        $payment->customer_name = $shippingForm->buyer_name;
        $payment->customer_email = $shippingForm->buyer_email;
        $payment->customer_phone = $shippingForm->buyer_phone;
        $payment->customer_address = $shippingForm->buyer_address;
        $payment->customer_city = $shippingForm->buyer_province_id;
        $payment->customer_postcode = $shippingForm->buyer_post_code;
        $payment->customer_district = $shippingForm->buyer_district_id;
        $payment->customer_country = $shippingForm->buyer_country_id;
        $payment->createTransactionCode();
        $orderParams = CartHelper::createOrderParams($items);
        $params = ArrayHelper::getValue($orderParams, 'orders');
        $totalAmount = ArrayHelper::getValue($orderParams, 'totalAmount', 0);
        if ($params === null || count($params) !== count($payment->orders) || (float)$totalAmount !== (float)$payment->total_amount) {
            return $this->response(false, 'something wrong');
        }
        $payment->orders = $params;
        $payment->total_amount = $totalAmount;
        /* @var $results PromotionResponse */
        $payment->checkPromotion();
        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->customer_id = $this->user->getId();
        $paymentTransaction->store_id = $payment->storeManager->getId();
        $paymentTransaction->transaction_type = PaymentTransaction::TRANSACTION_TYPE_PAYMENT;
        $paymentTransaction->transaction_status = PaymentTransaction::TRANSACTION_STATUS_CREATED;
        $paymentTransaction->transaction_code = $payment->transaction_code;
        $paymentTransaction->transaction_customer_name = $payment->customer_name;
        $paymentTransaction->transaction_customer_email = $payment->customer_email;
        $paymentTransaction->transaction_customer_phone = $payment->customer_phone;
        $paymentTransaction->transaction_customer_address = $payment->customer_address;
        $paymentTransaction->transaction_customer_postcode = $payment->customer_postcode;
        $paymentTransaction->transaction_customer_address = $payment->customer_address;
        $paymentTransaction->transaction_customer_district = $payment->customer_district;
        $paymentTransaction->transaction_customer_city = $payment->customer_city;
        $paymentTransaction->transaction_customer_country = $payment->customer_country;
        $paymentTransaction->payment_provider = $payment->payment_provider_name;
        $paymentTransaction->payment_method = $payment->payment_method_name;
        $paymentTransaction->payment_bank_code = $payment->payment_bank_code;
        $paymentTransaction->coupon_code = $payment->coupon_code;
        $paymentTransaction->used_xu = $payment->use_xu;
        $paymentTransaction->bulk_point = $payment->bulk_point;
        $paymentTransaction->total_discount_amount = $payment->total_discount_amount;
        $paymentTransaction->before_discount_amount_local = $payment->total_amount;
        $paymentTransaction->transaction_amount_local = $payment->total_amount - $payment->total_discount_amount;
        $paymentTransaction->payment_type = $payment->payment_type;
        $paymentTransaction->shipping = $shippingForm->receiver_address_id;
        $paymentTransaction->save(false);
        $res = $payment->processPayment();
        $time = $time = sprintf('%.3f', microtime(true) - $start);
        Yii::info("action time : $time", __METHOD__);
        return $this->response(false, 'create success', $res);
    }

    public function actionReturn($merchant)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $now = Yii::$app->getFormatter()->asDatetime('now');

        $post = Yii::$app->request->post();
        $get = Yii::$app->request->get();

        $start = microtime(true);

        $promotionDebug = [];

        $payment = new Payment();
        $receiverAddress = Address::findOne(1);
        /* @var $results PromotionResponse */
        $results = $payment->checkPromotion();
        $transaction = Order::getDb()->beginTransaction();
        try {
            foreach ($payment->orders as $key => $params) {
                $orderPromotions = []; // chứa toàn tộ những promotion được áp dụng cho order có key là $key
                if ($results->success === true && count($results->orders)) {
                    foreach ($results->orders as $promotion => $data) {
                        if (($discountForMe = ArrayHelper::getValue($data, $key)) === null) {
                            continue;
                        }
                        $orderPromotions[$promotion] = $discountForMe;
                    }
                }
                // 1 order
                $order = new Order();
                $order->type_order = Order::TYPE_SHOP;
                $order->portal = isset($params['portal']) ? $params['portal'] : explode(':', $key)[0];
                $order->customer_type = 'Retail';
                $order->exchange_rate_fee = $this->storeManager->getExchangeRate();
                $order->payment_type = 'online_payment';
                $order->receiver_email = $receiverAddress->email;
                $order->receiver_name = $receiverAddress->last_name . ' ' . $receiverAddress->last_name;
                $order->receiver_phone = $receiverAddress->phone;
                $order->receiver_address = $receiverAddress->address;
                $order->receiver_country_id = $receiverAddress->country_id;
                $order->receiver_country_name = $receiverAddress->country_name;
                $order->receiver_province_id = $receiverAddress->province_id;
                $order->receiver_province_name = $receiverAddress->province_name;
                $order->receiver_district_id = $receiverAddress->district_id;
                $order->receiver_district_name = $receiverAddress->district_name;
                $order->receiver_post_code = $receiverAddress->post_code;
                $order->receiver_address_id = $receiverAddress->id;
                $order->total_paid_amount_local = 0;

                if (($sellerParams = ArrayHelper::getValue($params, 'seller')) === null || !isset($sellerParams['seller_name']) || $sellerParams['seller_name'] === null || $sellerParams['seller_name'] === '') {
                    $transaction->rollBack();
                    return ['success' => false, 'message' => 'can not create order from not found seller'];
                }
                // 2 .seller
                if (($seller = Seller::find()->where(['AND', ['seller_name' => $sellerParams['seller_name']], ['portal' => isset($sellerParams['portal']) ? $sellerParams['portal'] : $order->portal]])->one()) === null) {
                    $seller = new Seller();
                    $seller->seller_name = $sellerParams['seller_name'];
                    $seller->portal = isset($sellerParams['portal']) ? $sellerParams['portal'] : $order->portal;
                    $seller->seller_store_rate = isset($sellerParams['seller_store_rate']) ? $sellerParams['seller_store_rate'] : null;
                    $seller->seller_link_store = isset($sellerParams['seller_link_store']) ? $sellerParams['seller_link_store'] : null;
                    $seller->save(false);
                }
                // 3. update seller for order
                $order->seller_id = $seller->id;
                $order->seller_name = $seller->seller_name;
                $order->seller_store = $seller->seller_link_store;
                if (!$order->save(false)) {
                    $transaction->rollBack();
                    return ['success' => false, 'message' => 'can not create order'];
                }
                $orderDiscount = 0; // số tiền discout cho toàn bộ order (không cho phí nào)
                $productPromotions = []; // discount cho các phí của product
                if (!empty($orderPromotions)) {
                    foreach ($orderPromotions as $promotion => $data) {
                        $value = (int)ArrayHelper::getValue($data, 'totalDiscountAmount', 0);
                        $orderDiscount += $value;

                        if (($discountForProduct = ArrayHelper::getValue($data, 'products')) !== null) {
                            $productPromotions[$promotion] = $discountForProduct;
                            continue;
                        }
                        $promotionDebug[] = [
                            'apply' => $now,
                            'code' => $promotion,
                            'level' => 'order',
                            'level_id' => $order->id,
                            'value' => $value
                        ];

                    }
                }
                if ($orderDiscount > 0) {
                    $order->updateAttributes([
                        'total_promotion_amount_local' => $orderDiscount
                    ]);
                }
                $updateOrderAttributes = [];
                // 4 products
                if (($products = ArrayHelper::getValue($params, 'products')) === null) {
                    $transaction->rollBack();
                    return ['success' => false, 'message' => 'an item is invalid'];
                }
                foreach ($products as $id => $item) {
                    $myDiscounts = [];
                    if (!empty($productPromotions)) {
                        foreach ($productPromotions as $promotion => $data) {
                            if (($current = ArrayHelper::getValue($data, $id)) === null) {
                                continue;
                            }
                            $myDiscounts[$promotion] = $current;
                        }
                    }
//                    Yii::info($myDiscounts, $id);
                    // 5 create product
                    $product = new Product();
                    $product->order_id = $order->id;
                    $product->portal = $item['portal'];
                    $product->sku = $item['sku'];
                    $product->parent_sku = $item['parent_sku'];
                    $product->link_img = $item['link_img'];
                    $product->link_origin = $item['link_origin'];
                    $product->product_link = $item['product_link'];
                    $product->product_name = $item['product_name'];
                    $product->quantity_customer = $item['quantity_customer'];
                    $product->total_weight_temporary = $item['total_weight_temporary'];
                    $product->price_amount_origin = $item['price_amount_origin'];
                    $product->total_fee_product_local = $item['total_fee_product_local'];
                    $product->price_amount_local = $item['price_amount_local'];
                    $product->total_price_amount_local = $item['total_price_amount_local'];
                    $product->quantity_purchase = null;
                    /** Todo */
                    $product->quantity_inspect = null;
                    /** Todo */
                    $product->variations = null;
                    /** Todo */
                    $product->variation_id = null;
                    $product->remove = 0;
                    $product->version = '4.0';
                    // 6. // step 4: create category for each item
                    if (($categoryParams = ArrayHelper::remove($item, 'category')) === null) {
                        $transaction->rollBack();
                        return ['success' => false, 'message' => 'invalid param for an item'];
                    }
                    if (($category = Category::findOne(['AND', ['alias' => $categoryParams['alias']], ['site' => isset($categoryParams['portal']) ? $categoryParams['portal'] : $product->portal]])) === null) {
                        $category = new Category();
                        $category->alias = $categoryParams['alias'];
                        $category->site = isset($categoryParams['portal']) ? $categoryParams['portal'] : $product->portal;
                        $category->origin_name = ArrayHelper::getValue($categoryParams, 'origin_name', null);
                        $category->save(false);
                    }
                    // 7. set category id for product
                    $product->category_id = $category->id;
                    // 8. set seller id for product
                    $product->seller_id = $seller->id;
                    // 9. product discount amount
                    // save total product discount here
                    if (!$product->save(false)) {
                        $transaction->rollBack();
                        return ['success' => false, 'message' => 'can not save a product'];
                    }
                    $productDiscount = 0;
                    $feeDiscounts = []; // chứa discount của toàn bộ phí
                    if (!empty($myDiscounts)) {
                        foreach ($myDiscounts as $promotion => $discount) {
                            $value = (int)ArrayHelper::getValue($discount, 'totalDiscountAmount', 0);
                            $productDiscount += $value;
                            $feeDiscounts[$promotion] = ArrayHelper::getValue($discount, 'discountFees', []);
                            $promotionDebug[] = [
                                'apply' => $now,
                                'code' => $promotion,
                                'level' => 'product',
                                'level_id' => $product->id,
                                'value' => $value
                            ];
                        }
                    }
//                    $product->updateAttributes(['total_discount_amount' => $productDiscount]);
                    // 9. product fee
                    if (($productFees = ArrayHelper::getValue($item, 'fees')) === null || count($productFees) === 0) {
                        $transaction->rollBack();
                        return ['success' => false, 'message' => 'can not get fee for an item'];
                    }
                    foreach ($productFees as $feeName => $feeValue) {
                        // 10. create each fee
                        $orderAttribute = '';
                        if ($feeName === 'product_price_origin') {
                            // Tổng giá gốc của các sản phẩm tại nơi xuất xứ
                            $orderAttribute = 'total_origin_fee_local';
                        }
                        if ($feeName === 'tax_fee_origin') {
                            // Tổng phí tax của các sản phẩm tại nơi xuất xứ
                            $orderAttribute = 'total_origin_tax_fee_local';
                        }
                        if ($feeName === 'origin_shipping_fee') {
                            // Tổng phí ship của các sản phẩm tại nơi xuất xứ
                            $orderAttribute = 'total_origin_shipping_fee_local';
                        }
                        if ($feeName === 'weshop_fee') {
                            // Tổng phí phí dịch vụ wéhop fee của các sản phẩm
                            $orderAttribute = 'total_weshop_fee_local';
                        }
                        if ($feeName === 'intl_shipping_fee') {
                            // Tổng phí phí vận chuyển quốc tế của các sản phẩm
                            $orderAttribute = 'total_intl_shipping_fee_local';
                        }
                        if ($feeName === 'custom_fee') {
                            // Tổng phí phụ thu của các sản phẩm
                            $orderAttribute = 'total_custom_fee_amount_local';
                        }
                        if ($feeName === 'packing_fee') {
                            // Tổng phí đóng gói của các sản phẩm
                            $orderAttribute = 'total_packing_fee_local';
                        }
                        if ($feeName === 'inspection_fee') {
                            // Tổng đóng hàng của các sản phẩm
                            $orderAttribute = 'total_inspection_fee_local';
                        }
                        if ($feeName === 'insurance_fee') {
                            // Tổng bảo hiểm của các sản phẩm
                            $orderAttribute = 'total_insurance_fee_local';
                        }
                        if ($feeName === 'vat_fee') {
                            // Tổng vat của các sản phẩm
                            $orderAttribute = 'total_vat_amount_local';
                        }
                        if ($feeName === 'delivery_fee_local') {
                            // Tổng vận chuyển tại local của các sản phẩm
                            $orderAttribute = 'total_delivery_fee_local';
                        }

                        $productFee = new ProductFee();
                        $productFee->type = $feeName;
                        $productFee->name = $feeValue['name'];
                        $productFee->order_id = $order->id;
                        $productFee->product_id = $product->id;
                        $productFee->amount = $feeValue['amount'];
                        $productFee->local_amount = $feeValue['local_amount'];
                        $productFee->discount_amount = 0;
                        $productFee->currency = $feeValue['currency'];
                        if (!$productFee->save(false)) {
                            $transaction->rollBack();
                            return ['success' => false, 'message' => 'can not deploy an fee'];
                        }
                        // 10. update discount each fee
                        $discountForFeeAmount = 0;
                        if (!empty($feeDiscounts)) {
                            foreach ($feeDiscounts as $promotion => $data) {
                                if (($forCurrentFee = ArrayHelper::getValue($data, $productFee->type)) === null) {
                                    continue;
                                }
                                $discountForFeeAmount += $forCurrentFee;
                                $promotionDebug[] = [
                                    'apply' => $now,
                                    'code' => $promotion,
                                    'level' => 'fee',
                                    'level_id' => $productFee->id,
                                    'value' => $forCurrentFee
                                ];
                            }
                        }
                        if ($discountForFeeAmount > 0) {
                            $productFee->updateAttributes(['discount_amount' => $discountForFeeAmount]);
                        }
                        if ($orderAttribute !== '') {
                            if ($orderAttribute === 'total_origin_fee_local') {
                                // Tổng giá gốc của các sản phẩm tại nơi xuất xứ (giá tại nơi xuất xứ)
                                $oldAmount = isset($updateOrderAttributes['total_price_amount_origin']) ? $updateOrderAttributes['total_price_amount_origin'] : 0;
                                $oldAmount += $productFee->amount;
                                $updateOrderAttributes['total_price_amount_origin'] = $oldAmount;
                            }
                            $value = isset($updateOrderAttributes[$orderAttribute]) ? $updateOrderAttributes[$orderAttribute] : 0;
                            $value += $productFee->local_amount;
                            $updateOrderAttributes[$orderAttribute] = $value;
                        }
                    }

                }
                $updateOrderAttributes['ordercode'] = WeshopHelper::generateTag($order->id, 'WSVN', 16);
                $order->updateAttributes($updateOrderAttributes);
            }
            $transaction->commit();
        } catch (Exception $exception) {
            $transaction->rollBack();
            Yii::error($exception, __METHOD__);
        }
    }
}