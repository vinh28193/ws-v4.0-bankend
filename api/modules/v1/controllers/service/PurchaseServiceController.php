<?php

namespace api\modules\v1\controllers\service;


use api\controllers\BaseApiController;
use common\lib\WalletBackendService;
use common\models\db\ListAccountPurchase;
use common\models\Order;
use common\models\PaymentTransaction;
use common\models\Product;
use common\models\User;
use common\models\weshop\FormPurchaseItem;
use common\modelsMongo\ChatMongoWs;
use frontend\modules\payment\PaymentService;
use frontend\modules\payment\providers\wallet\WalletService;
use Yii;

class PurchaseServiceController extends BaseApiController
{
    public function rules()
    {
        return [
            [
                'allow' => true,
                'actions' => ['list-account'],
                'roles' => $this->getAllRoles(true),
                'permissions' => ['canView']
            ],
            [
                'allow' => true,
                'actions' => ['send-notify-changing', 'confirm-changing-price'],
                'roles' => $this->getAllRoles(true),

            ],
        ];
    }

    public function verbs()
    {
        return [
            'list-account' => ['GET'],
            'send-notify-changing' => ['POST'],
            'confirm-changing-price' => ['POST'],
        ];
    }

    public function actionListAccount()
    {
        $type = Yii::$app->request->get('type', 'all');
        $account = ListAccountPurchase::find()->where(['active' => 1]);
        if ($type !== 'all') {
            $account->andWhere(['type' => strtolower($type)]);
        }
        $account = $account->asArray()->all();
        return $this->response(true, "Success", $account);
    }

    public function actionSendNotifyChanging()
    {
        /** @var  $exRate  \common\components\ExchangeRate */
        $exRate = \Yii::$app->exRate;
        $tran = Yii::$app->db->beginTransaction();
        try{
            $emailPrice = Yii::$app->request->post('emailPrice', false);
//        $emailFragile = Yii::$app->request->post('emailFragile',false);
            $orders = Yii::$app->request->post('cart', false);
            $message = "K??nh ch??o qu?? kh??ch";
            $messageOrder = "";
            /** @var User $user */
            $user = Yii::$app->user->getIdentity();
            if ($user) {
                foreach ($orders as $order) {
                    $messageProduct = "";
                    $amount = 0;
                    foreach ($order['products'] as $product) {
                        $form = new FormPurchaseItem();
                        $form->setAttributes($product, false);
                        $modelProduct = Product::findOne($form->id);
                        $mss = " t???i m?? s???n ph???m <b>" . (strtolower($form->ItemType) == 'ebay' ? $form->ParentSku : $form->sku) . "</b>";
                        $amountChange = 0;
                        if ($modelProduct) {
                            if (round($modelProduct->unitPrice->amount, 0) != round($form->price_purchase, 0)) {
                                $amountChange = round($form->price_purchase - $modelProduct->unitPrice->amount, 2);
                                $mss .= ' c?? s??? thay ?????i v??? <b>gi??</b>';
                                $mss .= $amountChange > 0 ? ' t??ng th??m <b>' . abs($amountChange) . '$</b>' : ' gi???m ??i <b>' . abs($amountChange) . '$</b>';
                            }
                            if (round($modelProduct->usShippingFee->amount, 0) != round($form->us_ship_purchase, 0)) {
                                $mss .= $amountChange != 0 ? ', v?? c?? s??? thay ?????i v??? <b>ph?? ship</b>' : ' c?? s??? thay ?????i v??? <b>ph?? ship</b>';
                                $amountChange = round($form->us_ship_purchase - $modelProduct->usShippingFee->amount, 2);
                                $mss .= $amountChange > 0 ? ' t??ng th??m <b>' . abs($amountChange) . '$</b>' : ' gi???m ??i <b>' . abs($amountChange) . '$</b>';
                            }
                            if (round($modelProduct->usTax->amount, 0) != round($form->us_tax_purchase, 0)) {
                                $mss .= $amountChange != 0 ? ', v?? c?? s??? thay ?????i v??? <b>ph?? tax</b>' : ' c?? s??? thay ?????i v??? <b>ph?? tax</b>';
                                $amountChange = round($form->us_tax_purchase - $modelProduct->usTax->amount, 2);
                                $mss .= $amountChange > 0 ? ' t??ng th??m <b>' . abs($amountChange) . '$</b>' : ' gi???m ??i <b>' . abs($amountChange) . '$</b>';
                            }
                            if ($amountChange != 0) {
                                $modelProduct->price_purchase = $form->price_purchase;
                                $modelProduct->shipping_fee_purchase = $form->us_ship_purchase;
                                $modelProduct->tax_fee_purchase = $form->us_tax_purchase;
                                $modelProduct->confirm_change_price = Product::STATUS_NEED_CONFIRM_CHANGE_PRICE;
                                $modelProduct->save();
                                $messageProduct .= $messageProduct ? '. V??' . $mss : $mss;
                            }
                        }
                        $amount = $amount + $amountChange;
                    }
                    if ($messageProduct != "" && $amount > 0) {
                        $orderDb = Order::findOne(['ordercode' => $order['ordercode']]);
                        $orderDb->confirm_change_price = Order::STATUS_NEED_CONFIRM_CHANGE_PRICE;
                        $orderDb->save(false);
                        /** @var PaymentTransaction[] $paymentTransactions */
                        $paymentTransactions = PaymentTransaction::find()
                            ->where([
                                'order_code' => $orderDb->ordercode,
                                'transaction_status' => [
                                    PaymentTransaction::TRANSACTION_STATUS_CREATED,
                                    PaymentTransaction::TRANSACTION_STATUS_QUEUED
                                ],
                                'transaction_type' => PaymentTransaction::TRANSACTION_ADDFEE
                            ])->all();
                        $listIdChange = [];
                        $transaction_description = '';
                        $amount = $exRate->usdToVnd($amount,23500);
                        foreach ($paymentTransactions as $item){
                            $listIdChange[] = $item->id;
                            $amount += $item->transaction_amount_local;
                            $transaction_description = $transaction_description ? $transaction_description.'<br><b>*</b> '.$item->transaction_description : $item->transaction_description;
                        }
                        Yii::debug($amount,'amount_change');
                        $paymentTransaction = new PaymentTransaction();
                        $paymentTransaction->store_id = $orderDb->store_id;
                        $paymentTransaction->customer_id = $orderDb->customer_id;
                        $paymentTransaction->transaction_type = PaymentTransaction::TRANSACTION_ADDFEE;
                        $paymentTransaction->transaction_status = PaymentTransaction::TRANSACTION_STATUS_QUEUED;
                        $paymentTransaction->transaction_customer_name = $orderDb->receiver_name;
                        $paymentTransaction->transaction_customer_email = $orderDb->receiver_email;
                        $paymentTransaction->transaction_customer_phone = $orderDb->receiver_phone;
                        $paymentTransaction->transaction_customer_address = $orderDb->receiver_address;
                        $paymentTransaction->transaction_customer_city = $orderDb->receiver_province_name;
                        $paymentTransaction->transaction_customer_postcode = $orderDb->receiver_post_code;
                        $paymentTransaction->transaction_customer_district = $orderDb->receiver_district_name;
                        $paymentTransaction->transaction_customer_country = $orderDb->receiver_country_name;
                        $paymentTransaction->order_code = $orderDb->ordercode;
                        $paymentTransaction->shipping = 0;
                        $paymentTransaction->payment_type = PaymentTransaction::PAYMENT_TYPE_ADDFEE;
                        $paymentTransaction->carts = '';
                        $paymentTransaction->transaction_description = $transaction_description ? $transaction_description.'<br>'.$messageProduct : $messageProduct;
                        $paymentTransaction->total_discount_amount = 0;
                        $paymentTransaction->before_discount_amount_local = $amount;
                        $paymentTransaction->transaction_amount_local = $amount;
                        $paymentTransaction->payment_provider = 'WS WALLET';
                        $paymentTransaction->payment_method = 'WALLET_WESHOP';
                        $paymentTransaction->payment_bank_code = 'WALLET_WESHOP';
                        $paymentTransaction->created_at = time();
                        $paymentTransaction->save(0);
                        $paymentTransaction->transaction_code = PaymentService::generateTransactionCode('PM' . $paymentTransaction->id);
                        $paymentTransaction->save(0);
                        PaymentTransaction::updateAll(
                            ['transaction_status' => PaymentTransaction::TRANSACTION_STATUS_REPLACED,'transaction_reference_code' => $paymentTransaction->transaction_code],
                            ['id' => $listIdChange]);
                        $messageOrder = ". M?? ????n h??ng <b>" . $orderDb->ordercode . "</b>" . $messageProduct . ".<br>Qu?? kh??ch c?? mu???n ti???p t???c ?????t ????n n??y kh??ng ???.<br>N???u c?? vui l??ng x??c nh???n v???i WeShop s???m nh???t ????? tr??nh s??? c??? h???t h??ng ho???c t??ng gi?? ti???p t???c s???y ra.";
                        $messageOrder .= "<br>B???n c?? th??? th???c hi???n thanh to??n ngay <a href='/my-weshop/addfee-$paymentTransaction->transaction_code.html'>t???i ????y</a>.";
                        $messageOrder .= "<br>Xin c???m ??n.";
                        if ($emailPrice) {
                            //#ToDo G???i mail th??ng b??o thay ?????i v??? gi??
                        }
                        if (!ChatMongoWs::SendMessage($message . $messageOrder, $order['ordercode'])) {
                            $tran->rollBack();
                            return $this->response(false, "G???i th??ng b??o th???t b???i");
                        }
                    }
                }
                if ($messageOrder != "") {
                    $message .= $messageOrder;
                    $tran->commit();
                    return $this->response(true, "???? g???i th??ng b??o t???i kh??ch h??ng!", $message);
                } else {
                    $tran->rollBack();
                    return $this->response(false, "Kh??ng c?? thay ?????i g?? v??? s??? ti???n, vui l??ng ki???m tra l???i.");
                }
            } else {
                return $this->response(false, "Vui l??ng ????ng nh???p.");
            }
        }catch (\Exception $exception){
            $tran->rollBack();
            Yii::error($exception);
            return $this->response(false, "T???o th??ng b??o th???t b???i");
        }
    }

    public function actionConfirmChangingPrice()
    {
        $orders = Yii::$app->request->post('cart', false);
        /** @var  $exRate  \common\components\ExchangeRate */
        $exRate = \Yii::$app->exRate;
        /** @var User $user */
        $user = Yii::$app->user->getIdentity();
        if ($user) {
            $tran = Yii::$app->db->beginTransaction();
            try{
                foreach ($orders as $order) {
                    foreach ($order['products'] as $product) {
                        $model = Product::findOne($product['id']);
                        if ($model){
                            $check = false;
                            $order = $model->order;
                            if($model->price_purchase && $model->price_purchase > $model->unitPrice->amount){
                                $fee = $model->unitPrice;
                                $check = true;
                                $old_local_amount = $fee->local_amount;
                                $old_amount = $fee->amount;
                                $fee->amount = $model->price_purchase;
                                $fee->local_amount = $exRate->usdToVnd($model->price_purchase,23500);
                                $model->total_price_amount_local += $fee->local_amount - $old_local_amount ;
                                $model->price_amount_local += $fee->local_amount - $old_local_amount ;
                                $model->price_amount_origin += $fee->amount - $old_amount;
                                $model->updated_at = time();
                                $order->updated_at = time();
                                $order->total_price_amount_origin += $fee->amount - $old_amount;
                                $order->total_fee_amount_local += $fee->local_amount - $old_local_amount;
                                $order->total_final_amount_local += $fee->local_amount - $old_local_amount;
                                $order->total_amount_local += $fee->local_amount - $old_local_amount;
                                $fee->save(0);
                                $model->price_purchase = null;
                            }
                            if($model->shipping_fee_purchase && $model->shipping_fee_purchase > $model->usShippingFee->amount){
                                $check = true;
                                $fee = $model->usShippingFee;
                                $old_local_amount = $fee->local_amount;
                                $old_amount = $fee->amount;
                                $fee->amount = $model->shipping_fee_purchase;
                                $fee->local_amount = $exRate->usdToVnd($fee->amount,23500);
                                $model->total_fee_product_local += $fee->local_amount - $old_local_amount ;
                                $model->updated_at = time();

                                $order->updated_at = time();
                                $order->total_origin_shipping_fee_local += $fee->amount - $old_amount;
                                $order->total_fee_amount_local += $fee->local_amount - $old_local_amount;
                                $order->total_final_amount_local += $fee->local_amount - $old_local_amount;
                                $order->total_amount_local += $fee->local_amount - $old_local_amount;
                                $fee->save(0);
                                $model->shipping_fee_purchase = null;
                            }
                            if($model->tax_fee_purchase && $model->tax_fee_purchase > $model->usTax->amount){
                                $check = true;
                                $fee = $model->usTax;
                                $old_local_amount = $fee->local_amount;
                                $old_amount = $fee->amount;
                                $fee->amount = $model->tax_fee_purchase;
                                $fee->local_amount = $exRate->usdToVnd($fee->amount,23500);

                                $model->total_fee_product_local += $fee->local_amount - $old_local_amount ;
                                $model->updated_at = time();

                                $order->updated_at = time();
                                $order->total_origin_tax_fee_local += $fee->amount - $old_amount;
                                $order->total_fee_amount_local += $fee->local_amount - $old_local_amount;
                                $order->total_final_amount_local += $fee->local_amount - $old_local_amount;
                                $order->total_amount_local += $fee->local_amount - $old_local_amount;
                                $fee->save(0);
                                $model->tax_fee_purchase = null;
                            }
                            $model->confirm_change_price = Product::STATUS_CONFIRMED_CHANGE_PRICE;
                            $order->confirm_change_price = Order::STATUS_CONFIRMED_CHANGE_PRICE;
                            $order->save(false);
                            $model->save();
                            if($check){
                                Yii::$app->wsLog->push('order','updateFee', null, [
                                    'id' => $model->id,
                                    'request' => "Customer confirm changing price",
                                    'response' => "Customer confirm changing price success"
                                ]);
                            }
                        }
                    }
                    ChatMongoWs::SendMessage('X??c nh???n t??ng gi?? t??? kh??ch h??ng.', $order['ordercode']);
                }
                $tran->commit();
                return $this->response(true, "???? x??c nh???n th??nh c??ng.");
            }catch (\Exception $exception){
                $tran->rollBack();
                Yii::error($exception);
                return $this->response(false, "X??c nh???n th???t b???i");
            }
        }
        return $this->response(false, "Vui l??ng ????ng nh???p.");
    }

}
