<?php
/**
 * Created by PhpStorm.
 * User: galat
 * Date: 23/03/2019
 * Time: 8:42 SA
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseApiController;
use common\models\db\Customer;
use common\models\db\PurchaseProduct;
use common\models\Order;
use common\models\Product;
use common\models\User;
use common\models\weshop\FormPurchaseItem;
use Yii;
use yii\db\ActiveQuery;

class PurchaseController extends BaseApiController
{
    public $post;
    public $get;
    public $session;
    public function init()
    {
        $this->post = \Yii::$app->request->post();
        $this->get = \Yii::$app->request->get();
        parent::init(); // TODO: Change the autogenerated stub
    }

    protected function rules()
    {
        return [
            [
                'allow' => true,
                'actions' => ['index', 'view', 'create', 'update','delete'],
                'roles' => $this->getAllRoles(true),

            ],
            [
                'allow' => true,
                'actions' => ['view'],
                'roles' => $this->getAllRoles(true),
                'permissions' => ['canView']
            ],
            [
                'allow' => true,
                'actions' => ['create'],
                'roles' => $this->getAllRoles(true, 'user'),
                'permissions' => ['canCreate']
            ],
            [
                'allow' => true,
                'actions' => ['update', 'delete'],
                'roles' => $this->getAllRoles(true, 'user'),
            ],
        ];
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'POST'],
            'create' => ['POST'],
            'update' => ['PATCH', 'PUT'],
            'view' => ['GET'],
            'delete' => ['DELETE']
        ];
    }

    public function actionIndex(){
        die("Action Test");
    }

    /**
     *Add cart purchase .
     */
    public function actionUpdate(){
        if(isset($this->get['id'])){
            $listId = [$this->get['id']];
            return $this->getCart($listId);
        }
        return $this->response(false,"not have Id");
    }

    public function actionDelete(){
        if(isset($this->post['idItem'])){
            /** @var Order $order */
            $order = Order::find()->where( [
                'purchase_assignee_id'=>Yii::$app->user->getId(),
                'current_status' => Order::STATUS_PURCHASING,
                'id' => $this->post['idItem']
            ])->limit(1)->one();
            $order->current_status = $order->total_purchase_quantity == 0 ?  Order::STATUS_READY_PURCHASE : Order::STATUS_PURCHASE_PART;
            $order->purchase_assignee_id = null;
            $order->save(0);
//            $item = Order::find()->where(['id' => $this->post['idItem']])->limit(1)->one();
//            $mess = "Remove soi ".$this->post['idItem']." to cart. ";
//            OrderLogs::log($item->id,"action",$mess,Yii::$app->user->getIdentity()->username,$mess,$item->id);
            return $this->getCart();
        }
        return $this->response(false,"not have Id");
    }

    function getCart($listId = []){
        $type = Yii::$app->request->post('type','addtocart');
        $data = [];
        $listId_cancel = [];
        $mess = '';
        $success = false;
        $addfee_amount = 0;
        /** @var Order[] $orders */
        if($listId && $type == 'addtocart'){
            $orders = Order::find()->with(['products'])
                ->where(['id'=>$listId,'current_status' => [Order::STATUS_READY_PURCHASE,Order::STATUS_PURCHASE_PART]])
                ->orWhere(['purchase_assignee_id'=>Yii::$app->user->getId(),'current_status' => Order::STATUS_PURCHASING])
                ->all();
        }elseif($listId){
            $orders = Order::find()->with('products')
                ->where(['id'=>$listId,'current_status' => [Order::STATUS_READY_PURCHASE,Order::STATUS_PURCHASE_PART]])
                ->all();
        }else{
            $success = true;
            $orders = Order::find()->with('products')
                ->where(['purchase_assignee_id'=>Yii::$app->user->getId(),'current_status' => Order::STATUS_PURCHASING])
                ->all();
        }
        /** @var User $user */
        $user = Yii::$app->user->getIdentity();
        foreach ($orders as $key => $order){
            $data[$key]['order_id'] = $order->id;
            $data[$key]['seller'] = $order->seller_name;
            $data[$key]['total_amount'] = $order->total_final_amount_local;
            $data[$key]['portal'] = $order->portal;
            foreach ($order->products as $item){
                if($item->quantity_customer > $item->quantity_purchase || $order->current_status == Order::STATUS_PURCHASING) {
                    $addfee_amount = 0;
//                /** @var OrderPaymentRequest[] $order_request_change */
//                $order_request_change = OrderPaymentRequest::find()
//                    ->where([
//                        'reason'=>'INCREASE PRICE',
//                        'order_item_id'=> $item->id,
//                        'type' => 'ADDFEE',
//                        'status'=>[
//                            OrderPaymentRequest::STATUS_APPROVED,
//                            OrderPaymentRequest::STATUS_REQUESTED,
//                            OrderPaymentRequest::STATUS_COMPLETED,
//                        ]
//                    ])->all();
//                foreach ($order_request_change as $addfee){
//                    $addfee_amount += $addfee->weshop_fee ? floatval($addfee->amount) - floatval($addfee->weshop_fee) : floatval($addfee->amount);
//                }
                    /** @var PurchaseProduct[] $list_purchased */
                    $list_purchased = PurchaseProduct::find()->where(['product_id' => $item->id])->all();
                    $amount_purchased = 0;
                    $quantity_purchased = 0;
                    foreach ($list_purchased as $purchaseOrderItem) {
                        $quantity_purchased += $purchaseOrderItem->purchase_quantity;
                        $amount_purchased += $purchaseOrderItem->paid_to_seller;
                    }
                    /** @var Customer $cus */
                    $cus = $order->customer_id ? Customer::findOne($order->customer_id) : false;
                    $tmp = new FormPurchaseItem();
                    $tmp->id = $item->id;
                    $tmp->order_id = $order->id;
                    $tmp->condition = $item->condition;
                    $tmp->sellerId = $order->seller_name;
                    $tmp->ItemType = $order->portal;
                    $tmp->image = $item->link_img;
                    $tmp->Name = $item->product_name;
                    $tmp->typeCustomer = $cus && $cus->type_customer ? $cus->type_customer : 1;
                    $tmp->price = $tmp->price_purchase = $item->price_amount_origin;// $item->ItemFeeServiceAmount;
                    $tmp->us_ship = $tmp->us_ship_purchase = $item->usShippingFee ? $item->usShippingFee->amount : 0;
                    $tmp->us_tax = $tmp->us_tax_purchase = $item->usTax ? $item->usTax->amount : 0;
                    $tmp->ParentSku = $item->parent_sku;
                    $tmp->sku = $item->sku;
                    $tmp->quantity = $item->quantity_customer - $quantity_purchased;
                    $tmp->quantityPurchase = $item->quantity_customer - $quantity_purchased;
                    $tmp->variation = $item->variations;
                    $tmp->paidTotal = $tmp->paidToSeller = ($tmp->price + $tmp->us_ship + $tmp->us_tax) * $tmp->quantity ;
                    $data[$key]['products'][] = $tmp->toArray();
                    if ($type == 'addtocart') {
//                    $mess = "Add soi ".$item->id." to cart. Total ".count($order)." items!";
//                    OrderLogs::log($item->orderId,"action",$mess,$user->username,$mess,$item->id);
                    }
                    $success = true;
                }else{
                    $listId_cancel[] = $item->id;
                }
            }
            $order->purchase_assignee_id = $user->id;
            $order->current_status = Order::STATUS_PURCHASING;
            $order->save(0);
        }

        $mess .= "Add to cart success. ";
        if(count($listId_cancel) > 0){
            $mess .="And this is list soi cancel add to card: ".implode(' ,',$listId_cancel);
            $success = true;
        }
        if(!$success){
            $mess .="can not find anything soi in list id";
        }
        return $this->response($success,$mess,$data);
    }
    public function actionCreate(){
//        print_r($this->post);
//        die;
        $list_item = Yii::$app->request->post('Items',null);
        $po = Yii::$app->request->post('TotalPurchase',null);
        if(!$list_item || !$po){
            return $this->response(false,'Item or TotalPurchase was null');
        }
        $list_item = (json_decode($list_item,true));
        $po = (json_decode($po,true));
//        print_r($po);
//        print_r($list_item);
//        die;
        $po['purchaseAccount'] = str_replace(' ','',$po['purchaseAccount']);
        $po['orderId'] = str_replace(' ','',$po['orderId']);
        $po['purchaseTransaction'] = str_replace(' ','',$po['purchaseTransaction']);
        if(!$po['warehouse'] || !$po['orderId'] || !$po['purchaseAccount'] || !$po['purchaseCard'] || !$po['purchaseTransaction'] || !$po['totalAmount'] || !$po['totalAmountPaid'] ){
            return $this->response(false,'Enter all field required');
        }
        if(count($list_item) == 0){
            return $this->response(false,'Not have item purchase!');
        }
        $item_type = strtolower($list_item[0]['ItemType']);
        $account = ListAccountBuyer::find()->where(['like','email',$po['purchaseAccount']])
            ->andWhere(['like','type',$item_type])->one();
        if(!$account){
            $account = new ListAccountBuyer();
            $account->account = $po['purchaseAccount'];
            $account->email = $po['purchaseAccount'];
            $account->active = 1;
            $account->type = $item_type;
            $account->save(0);
        }
        $listid = [];
        foreach ($list_item as $item) {
            $listid[] = $item['id'] ;
        }
        $countItem = OrderItem::find()->where(['id' => $listid,'status' => [OrderItem::STATUS_PURCHASING,OrderItem::STATUS_READY2PURCHASE]])->count();
        if($countItem == 0){
            return $this->response(false,'Not have item invalid!');
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $PurchaseOrder = new PurchaseOrder();
            $PurchaseOrder->Description = $po['note'];
            $PurchaseOrder->OrderTime = date('Y-m-d H:i:s');
            $PurchaseOrder->TotalItemOrder = $countItem;
            $PurchaseOrder->TransactionCode = $po['purchaseTransaction'];
            $PurchaseOrder->PaymentMethodId = 1;
            $PurchaseOrder->Status = 0;
            $PurchaseOrder->PaymentTime = date('Y-m-d H:i:s');
            $PurchaseOrder->PaymentCardId = $po['purchaseCard'];
            $PurchaseOrder->TotalOrder = $po['totalAmount'];
            $PurchaseOrder->PurchaseOrderCode = $po['orderId'];
            $PurchaseOrder->PaypalAmount = $po['totalAmountPaid'];
            $PurchaseOrder->viaChannelId = 2;
            $PurchaseOrder->MerchantEmail = $account->email;
            $PurchaseOrder->isNoTrackingCode = 0;
            $PurchaseOrder->paypalCurrencyId = 3;
            $PurchaseOrder->NotePurchaseWarehouseId = $po['warehouse'];
            $PurchaseOrder->accountPurchaseId = $account->id;
            $PurchaseOrder->save(0);
            /** @var User $user */
            $user = Yii::$app->user->getIdentity();
            $shippingFee = 0;
            $storeId = 1;
            foreach ($list_item as $item){
                $order = OrderItem::findOne($item['id']);
                if($order && in_array($order->status,[OrderItem::STATUS_READY2PURCHASE,OrderItem::STATUS_PURCHASING])){
                    $storeId = $order->storeId ? $order->storeId : 1;
                    $order->purchaseOrderCode = $PurchaseOrder->PurchaseOrderCode;
                    $order->purchase_assignee_id = $user->getId();
                    $order->purchaseUnitPrice = $item['pricePurchase'];
                    $order->purchaseQuantity = $order->purchaseQuantity ? $order->purchaseQuantity +  $item['quantity'] : $item['quantity'];
                    if($order->purchaseQuantity >= $order->quantity) {
                        $order->purchaseStatus = OrderItem::STATUS_PURCHASED;
                        $order->status = OrderItem::STATUS_PURCHASED;
                        $order->purchaseCompleteTime = date('Y-m-d H:i:s');
                    }else{
                        $order->purchaseStatus = OrderItem::STATUS_PURCHASED_PART;
                        $order->status = OrderItem::STATUS_PURCHASED_PART;
                    }
                    $order->purchaseTax = $order->purchaseTax ? floatval($order->purchaseTax) + $item['taxPurchase'] : $item['taxPurchase'];
                    $order->purchaseShippingFee = $order->purchaseShippingFee ? floatval($order->purchaseShippingFee) + $item['shipPurchase'] : $item['shipPurchase'];
                    $order->purchasePaidAmount = $order->purchasePaidAmount? floatval($order->purchasePaidAmount) + $item['paidToSeller'] : $item['paidToSeller'];
                    $order->purchasePaypalAmount = $order->purchasePaypalAmount? floatval($order->purchasePaypalAmount) + $item['paidToSeller'] : $item['paidToSeller'];
                    $order->purchaseTransactionCode = $PurchaseOrder->TransactionCode;
                    $order->purchaseOrderId = $PurchaseOrder->id;
                    $order->purchaseMerchantEmail = $PurchaseOrder->MerchantEmail;
                    $order->NotePurchaseWarehouseId = $PurchaseOrder->NotePurchaseWarehouseId;
                    $order->accountPurchaseId = $PurchaseOrder->accountPurchaseId;
                    $order->purchaseStartTime = date('Y-m-d H:i:s');
                    $order->skuChange = $item['sku'];
                    $order->save();
                    if(strtolower($order->ItemType) == 'amazon' || strtolower($order->ItemType) == 'amazon-jp' || strtolower($order->ItemType) == 'amazon-uk'){
                        $typeWesShopFee = 2;
                    }else{
                        $typeWesShopFee = 1;
                    }
                    if($order->currencyId){
                        /** @var Customer $cus */
                        $cus = Customer::findOne($order);
                        if($cus && $cus->customerGroupId == 2){
                            $typeWesShopFee = 3;
                        }
                    }
                    $newPurchaseItem = new PurchaseOrderItem();
                    $newPurchaseItem->PurchaseOrderId = $PurchaseOrder->id;
                    $newPurchaseItem->orderItemId = $order->id;
                    $newPurchaseItem->sku = $order->skuChange;
                    $newPurchaseItem->ProductName = $order->Name;
                    $newPurchaseItem->Description = $po['note'];
                    $newPurchaseItem->Price = $item['pricePurchase'];
                    $newPurchaseItem->TaxRate = $item['taxRatePurchase'];
                    $newPurchaseItem->TaxAmount = $item['taxPurchase'];
                    $newPurchaseItem->ShipFeeAmount = $item['shipPurchase'];
                    $newPurchaseItem->CustomFeeAmount = $order->ItemCustomFee;
                    $newPurchaseItem->InspectionFee = $order->ItemCustomAdditionFee;
                    $newPurchaseItem->Weight = $order->weight;
                    $newPurchaseItem->Quantity = $item['quantity'];
                    $newPurchaseItem->ExRate = Yii::$app->store->tryStore($order->storeId ? $order->storeId : 1)->exchangeRate();
                    $newPurchaseItem->weshopFee = $newPurchaseItem->getWeShopFee($typeWesShopFee);
                    $newPurchaseItem->SubTotalAmount = $newPurchaseItem->getSubTotalAmount();
                    $newPurchaseItem->TotalAmountInLocalCurreny = $newPurchaseItem->ExRate * $newPurchaseItem->SubTotalAmount;
                    $newPurchaseItem->ShippingStatus = 1;
                    $newPurchaseItem->MerchantId = $account->id;
                    $newPurchaseItem->IsRequestInspection = 0;
                    $newPurchaseItem->ItemLine = $PurchaseOrder->id;
                    $newPurchaseItem->NoteForInspection = 0;
                    $newPurchaseItem->PaypalAmount = $item['paidToSeller'];
                    $newPurchaseItem->MerchantEmail = $account->email;
                    $newPurchaseItem->viaChannelId = 2;
                    $newPurchaseItem->localProductName = $order->localProductName;
                    $changingPrice = $newPurchaseItem->getSubTotalAmount() - (($order->quantity * $order->UnitPriceExclTax) + floatval($order->ItemTax) + floatval($order->ItemLocalShippingAmount) + floatval($order->ItemFeeServiceAmount));
                    $order->changingPrice += $changingPrice;
                    $order->save(0);
                    $newPurchaseItem->changingPrice = $order->changingPrice;
                    $newPurchaseItem->save(0);
                    $shippingFee += $newPurchaseItem->ShipFeeAmount;
                    $p = PurchasePaymentCard::findOne($po['purchaseCard']);
                    $po['CardCode'] = $p->CardCode;
                    $po['Items'] = $item;
//                try{
                    LogTrackingOrderItem::createLogTracking(
                        null,
                        $order->id,
                        null,
                        null,
                        LogTrackingOrderItem::STATUS_CODE_PURCHASED,
                        "Purchased",
                        $po
                    );
                    OrderLogs::log($order->orderId,"action",$order->status,$user->username,$po,$order->id);
                    OrderLogs::log($order->orderId,"operation",$order->status,$user->username,$po,$order->id);
//                }catch (\Exception $e){

//                }
                    if($po['sendmailPrice']){
                        //#ToDo: send mail to customer for changing price
                    }
                    if($po['sendmailFragile']){
                        //#ToDo: send mail to customer for fragile
                    }

                }
            }
            $PurchaseOrder->PurchaseOrderNumber = 'PO'.$PurchaseOrder->id;
            $PurchaseOrder->localCurencyId   = $storeId == 6 ? 5 : $storeId;
            $PurchaseOrder->StoreId = $storeId;
            $PurchaseOrder->ShippingFee = $shippingFee;
            $PurchaseOrder->save(0);
            $tran->commit();
            return $this->response(true,'Purchase success! '.$PurchaseOrder->PurchaseOrderNumber);
        }catch (\Exception $exception){
            $tran->rollBack();
            return $this->response(false,'something error');
        }
    }
    public function actionGetListAccount(){
        $type = Yii::$app->request->get('type','all');
        $account = ListAccountBuyer::find()->where(['active' => 1]);
        if($type !== 'all'){
            $account->andWhere(['type' => strtolower($type)]);
        }
        $account = $account->asArray()->all();
        return $this->response(true,"SUccess" , $account);
    }
    public function actionGetListCardPayment(){
        $storeId = Yii::$app->request->get('store',1);
        $storeId = $storeId ? $storeId : 1;
//        $list_data = PurchasePaymentCard::find()->where(['store_id' => $storeId , 'status' => 1])->asArray()->all();
        $list_data = PurchasePaymentCard::find()->where(['status' => 1])->asArray()->all();
        return $this->response(true,"Success" , $list_data);
    }
    public function actionMakeStatus(){
        $id = Yii::$app->request->post('id');
        $note = Yii::$app->request->post('note');
        $type = Yii::$app->request->post('type');

        $data = [
            'status' => OrderItem::STATUS_PURCHASE_PENDING,
            'purchaseFailReason' => 'OUT_OF_STOCK',
            //'note' => 'Out of Stock',
            //'purchaseNote' => 'Out of Stock by user ' . $this->request->get('userId'),
            'purchaseStatus' => 'Out_of_Stock',
            'purchaseStartTime' => date('Y-m-d H:i:s')
        ];
        $order = OrderItem::findOne($id);
        $order->setAttributes($data);
        $order->save(0);

        $order_Support = new OrderSupportLog();
        $order_Support->created_at = date("Y-m-d H:i:s");
        $order_Support->SupportedDate = date("Y-m-d H:i:s");
        $order_Support->OrderId = $order->orderId;
        $order_Support->OrderItemId = $order->id;
        $order_Support->EmployeeId = Yii::$app->user->getId();
        $order_Support->user_id = Yii::$app->user->getId();
        $order_Support->Note = 'Action OutOfStock, update ' . OrderItem::STATUS_PURCHASE_PENDING . ' with note: ' . $note;
        $order_Support->LevelSupport = OrderItem::STATUS_PURCHASE_PENDING;
        $order_Support->save(0);

        OrderLogs::log($order->orderId, 'action', "SOI-$order->id: Update status", Yii::$app->user->getIdentity()->username, 'Action OutOfStock, update ' . OrderItem::STATUS_PURCHASE_PENDING . ' with note: ' . $note, $order->id);
        OrderLogs::log($order->orderId, 'operation', "SOI-$order->id: Update status", Yii::$app->user->getIdentity()->username, 'Action OutOfStock, update ' . OrderItem::STATUS_PURCHASE_PENDING . ' with note: ' . $note, $order->id);
        return $this->response(true,"Make Out Of Stock Success!",['id' => $id, 'status' => OrderItem::STATUS_PURCHASE_PENDING]);
    }
    public function actionCreateAddfee(){
        $item = Yii::$app->request->post('item',null);
        $note = Yii::$app->request->post('note',null);
        $addFee = Yii::$app->request->post('addfee',null);
        $wsFee = Yii::$app->request->post('wsFee',0);

        if(!$item){
            return $this->response(false,"Not have item");
        }
        if(!$addFee || $addFee < 0){
            return $this->response(false,"Not have addfee");
        }
        $item = json_decode($item);
        $tran = Yii::$app->db->beginTransaction();
        try{
            $order = OrderItem::findOne($item->id);
            if($order){
                $order->setAttributes([
                    'purchaseUnitPrice' => $item->pricePurchase,
                    'purchaseShippingFee' => $item->shipPurchase,
//                    'purchaseQuantity' => $item->quantityPurchase,
                    'purchaseTax' => $item->taxPurchase,
                    'purchaseNote' => $note ? $note : "",
                    'status' => OrderItem::STATUS_PURCHASE_PENDING,
                    'purchaseFailReason' => "ADD_FEE",
                ]);
                $order->save(0);
                /** @var SystemCurrencyRate $currency */
                $currency = SystemCurrencyRate::GetCurrencyRate($order->currencyId);
                $rateEx = $order->ExRate ? $order->ExRate : $currency->Rate;
                $data = [
                    'amount' => $addFee + $wsFee,
                    'amount_local' => round($rateEx * ($addFee + $wsFee),($order->currencyId == 1 ? -3 : 2)),
                    'exchange_rate' => $rateEx,
                    'currency_id' => $order->currencyId,
                    'create_at' => date('Y-m-d H:i:s'),
                    'create_by' => Yii::$app->user->getIdentity()->username,
                    'type' => OrderPaymentRequest::TYPE_ADDFEE,
                    'reason' => 'INCREASE PRICE',
                    'process_note' => 'ADDFEE FROM HIGHER PRICE $' . $addFee." & WSFEE $".$wsFee,
                    'order_id' => $order->orderId,
                    'order_item_id' => $order->id,
                    'status' => OrderPaymentRequest::STATUS_NEW,
                    'weshop_fee' => $wsFee,
                ];
                $orderRequest = new OrderPaymentRequest();
                $orderRequest->setAttributes($data);
                $orderRequest->save(0);
                OrderLogs::log($order->orderId, 'action', "SOI-$order->id: $orderRequest->process_note", Yii::$app->user->getIdentity()->username, 'Action addfee changingPrice, update ' . OrderItem::STATUS_PURCHASE_PENDING . ' with note: ' . $note, $order->id);
                OrderLogs::log($order->orderId, 'operation', "SOI-$order->id: $orderRequest->process_note", Yii::$app->user->getIdentity()->username, 'Action addfee changingPrice, update ' . OrderItem::STATUS_PURCHASE_PENDING . ' with note: ' . $note, $order->id);
            }
            $tran->commit();
            return $this->response(true,"Create addFee success!");
        }catch (\Exception $exception){
            $tran->rollBack();
            return $this->response(false,"Create addFee fail!");
        }
    }
}

