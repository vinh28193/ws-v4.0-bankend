<?php
namespace api\modules\v1\controllers;

use api\controllers\BaseApiController;
use common\components\cart\CartManager;


/** Dữ liệu Order **/
use common\models\Seller;
use common\models\Order;

/** Sản Phẩm 1-1 , 1-n **/
use common\models\Product;
use common\models\OrderFee as ProductFee;

/** Tính Phụ Thu danh mục**/
use common\models\db\Category;

/** Payment + Wallet Log **/

/** Nhân Viên Support **/
/** Role :
    case 'cms':
    case 'warehouse':
    case 'operation':
    case 'sale':
    case 'master_sale':
    case 'master_operation':
    case 'superAdmin' :
 **/

/** Gói Kiện hàng về : Từ người bán gửi về Kho Mỹ **/
use common\models\Package;
use common\models\PackageItem;

/** Package Tạm + Dữ liệu Kiểm thực tế với BOXME **/


use Yii;
use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class DataFixedController extends BaseApiController
{

    /**
     * @var string|Connection
     */
    protected $db = 'db';

    /**
     * @var string|CartManager
     */
    protected $cart = 'cart';

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
        $this->cart = Instance::ensure($this->cart, CartManager::className());
    }

    public function verbs()
    {
        return [
            'index' => ['GET'],
            'create' => ['POST']
        ];
    }

    public function actionIndex()
    {

    }

    /** Dữ liệu giả lập add Card **/
    protected function CartData($dataPost = [])
    {
        $this->cart->removeItems();
        $this->cart->addItem('IF_739F9D0E', 'cleats_blowout_sports', 1, 'ebay', 'https://i.ebayimg.com/00/s/MTYwMFgxMDY2/z/cAQAAOSwMn5bzly6/$_12.JPG?set_id=880000500F', '252888606889');
        //$this->cart->addItem('IF_6C960C53', 'cleats_blowout_sports', 1, 'ebay', 'https://i.ebayimg.com/00/s/MTYwMFgxMDY2/z/nrsAAOSw7Spbzlyw/$_12.JPG?set_id=880000500F', '252888606889');
        //$this->cart->addItem('261671375738', 'luv4everbeauty', 1, 'ebay', 'https://i.ebayimg.com/00/s/NTk3WDU5Nw==/z/FjMAAOSwscNbK5~0/$_57.JPG');

        // $sku, $seller, $quantity, $source, $image, $parentSku
        /** Todo : Thiếu link Gốc sản phẩm
          * Thieu Mã giảm giá , Phương thức thanh toán
         **/
        $this->cart->addItem(
            $dataPost['sku'],
            $dataPost['seller'],
            $dataPost['quantity'],
            $dataPost['source'],
            $dataPost['image'],
            $dataPost['parentSku']
            );
    }

    protected function SellerData($item,$key)
    {
        if (($providers = ArrayHelper::getValue($item, 'providers')) === null || ($providers !== null && !isset($providers['name']))) {
            $errors[$key][] = "can not create form null seller";
            //continue;
            Yii::$app->api->sendFailedResponse("can not create form null seller");
        }
        if (($seller = Seller::findOne(['seller_name' => $providers['name']])) === null) {
            $seller = new Seller();
            $seller->seller_name = $providers['name'];
            $seller->seller_link_store = $providers['website'];
            $seller->seller_store_rate = $providers['rating_score'];
            $seller->save(false);
        }

        return $seller;
    }

    protected function CategoryData($item,$key,$itemType)
    {
        if (($categoryId = ArrayHelper::getValue($item, 'category_id')) === null) {
            $errors[$key][] = "can not create form null category";
            //continue;
            Yii::$app->api->sendFailedResponse("can not create form null category . Check Data Category or tables Category ");
        }
        if (($category = Category::findOne(['AND', ['alias' => $categoryId], ['site' => $itemType]])) === null) {
            $category = new Category();
            $category->alias = $categoryId;
            $category->site = $itemType;
            $category->origin_name = ArrayHelper::getValue($item, 'category_name', 'Unknown');
            $category->save();
        }

        return $category;
    }

    protected function ProductData($propertyShopCart,$itemGetWayAPI,$category,$order,$seller)
   {

        $product = new Product;

        $product->order_id = $order->id;
        $product->seller_id =  $seller->id;
        $product->portal =  $propertyShopCart->source;
        $product->sku =  $itemGetWayAPI['item_sku'];
        $product->parent_sku =  $itemGetWayAPI['item_id'];
        $product->link_img =  $propertyShopCart->image;
        $product->link_origin = $itemGetWayAPI['item_origin_url'];
        $product->category_id = $category->id;
        $product->custom_category_id =  $category->id;

        $product->getAdditionalFees()->mset($itemGetWayAPI['additionalFees']);
        list($product->price_amount_origin, $product->total_price_amount_local) = $product->getAdditionalFees()->getTotalAdditionFees();

        //$product->price_amount_origin =  0;
        //$product->total_price_amount_local =  0;

        $product->price_amount_local =  0;  /** Todo */
        $product->quantity_customer =  $itemGetWayAPI['quantity'];
        $product->quantity_purchase =  null;  /** Todo */
        $product->quantity_inspect =  null;  /** Todo */
        $product->variations =  null;   /** Todo */
        $product->variation_id =  null;  /** Todo */
        $product->note_by_customer =  'Note By Customer';
        $product->total_weight_temporary =  'Total Weight Temporary';
        $product->remove =  0;
        $product->product_name =  null;  /** Todo */
        $product->product_link =  null;  /** Todo */
        $product->version =  '4.0';
        $product->condition =  null; /** Todo */

        $product->save();

        return $product;
    }

    protected function OrderData($itemType , $seller )
    {
        $order = new Order();
        $order->new = time();
        $order->store_id =  1;
        $order->type_order =  "SHOP";
        $order->portal =  $itemType;
        $order->is_quotation =  0;
        $order->quotation_status =  null;
        $order->quotation_note =  null;
        $order->customer_id =  13;
        $order->receiver_email =  "dieu.nghiem@hotmail.com";
        $order->receiver_name =  "Bạc Vĩ";
        $order->receiver_phone =  "022 511 1846";
        $order->receiver_address =  "3, Thôn Diệp Đoàn, Ấp Thạch Đình, Quận Khoát Anh Bắc Giang";
        $order->receiver_country_id =  1;
        $order->receiver_country_name =  "Việt Nam";
        $order->receiver_province_id =  3;
        $order->receiver_province_name =  "Hà Nội";
        $order->receiver_district_id =  1;
        $order->receiver_district_name =  "Phố Bì";
        $order->receiver_post_code =  "750214";
        $order->receiver_address_id =  1;
        $order->note_by_customer =  "Come on!\" So they sat down with wonder at the.";
        $order->note =  "As they walked off together, Alice heard the.";
        $order->payment_type =  "WALLET";
        $order->sale_support_id =  1;
        $order->support_email =  "dcn@yahoo.com";
        $order->coupon_id =  null;
        $order->revenue_xu =  0;
        $order->xu_count =  0;
        $order->xu_amount =  0;
        $order->is_email_sent =  0;
        $order->is_sms_sent =  0;
        $order->promotion_id =  1;
        $order->difference_money =  0;
        $order->utm_source =  null;
        $order->seller_id =  $seller->id;
        $order->seller_name =  "Em. Giao Luận";
        $order->seller_store =  "https://www.le.int.vn/sed-expedita-rerum-beatae-consectetur-commodi";
        $order->total_final_amount_local =  0;
        $order->total_paid_amount_local =  0;
        $order->total_refund_amount_local =  0;
        $order->total_amount_local =  10716000;
        $order->total_fee_amount_local =  0;
        $order->total_counpon_amount_local =  0;
        $order->total_promotion_amount_local =  0;
        $order->exchange_rate_fee =  23500;
        $order->exchange_rate_purchase =  2345;
        $order->currency_purchase =  "0";
        $order->purchase_order_id =  null;
        $order->purchase_transaction_id =  null;
        $order->purchase_amount =  null;
        $order->purchase_account_id =  null;
        $order->purchase_account_email =  null;
        $order->purchase_card =  null;
        $order->purchase_amount_buck =  null;
        $order->purchase_amount_refund =  null;
        $order->purchase_refund_transaction_id =  null;
        $order->total_weight =  3;
        $order->total_weight_temporary =  null;
        $order->purchased =  null;
        $order->seller_shipped =  null;
        $order->stockin_us =  null;
        $order->stockout_us =  null;
        $order->stockin_local =  null;
        $order->stockout_local =  null;
        $order->at_customer =  null;
        $order->returned =  null;
        $order->cancelled =  null;
        $order->lost =  null;
        $order->current_status =  "NEW";
        $order->remove =  0;
        $order->save();


        return $order;
    }

    public function actionCreate()
    {
        if (isset($this->post) == null) {  Yii::$app->api->sendFailedResponse("Invalid Record requested"); }

        $this->CartData($this->post);
        $items = $this->cart->getItems();

        $orders = [];
        $errors = [];
        foreach ($items as $key => $simpleItem) {
            /** @var  $simpleItem \common\components\cart\item\SimpleItem */
            $item = $simpleItem->item;

            $itemType = $this->post['source'];
            // Seller
            $seller = $this->SellerData($item,$key);

            // Category
            $category = $this->CategoryData($item,$key,$itemType);

            // Order
            $order = $this->OrderData($itemType ,$seller);

            // Product
            $product =  $this->ProductData( $simpleItem,$item,$category,$order,$seller);

            $orderUpdateFeeAttribute = [];
            foreach ($product->getAdditionalFees()->keys() as $key) {
                list($amount, $local) = $product->getAdditionalFees()->getTotalAdditionFees($key);
                $orderAttribute = "total_{$key}_local";
                if ($key === 'product_price_origin') {
                    $orderAttribute = 'total_origin_fee_local';
                } elseif ($key === 'tax_fee_origin') {
                    $orderAttribute = 'total_origin_tax_fee_local';
                } elseif ($key === 'delivery_fee_local') {
                    $orderAttribute = 'total_delivery_fee_local';
                }

                // Todo with OrderFee
                $_productFee = $this->ProductsFeeData($key,$amount,$local,$product->getPrimaryKey(),$order->getPrimaryKey(), $orderAttribute);

            }
            $orderUpdateFeeAttribute['total_fee_amount_local'] = $product->getAdditionalFees()->getTotalAdditionFees()[1];
            //$order->updateAttributes($orderUpdateFeeAttribute);
            $orders[] = $order->id;
        }


        $_itemRes = new \stdClass();
        $_itemRes->order = $orders;
        $_itemRes->product = $product;
        $_itemRes->productFee = $_productFee;

        $data = new \stdClass();
        $data->_items = $_itemRes;
        $data->_links = null;
        $data->_meta = null;
        Yii::$app->api->sendSuccessResponse($data);

    }

    protected function ProductsFeeData($key,$amount,$local,$product,$order_id , $orderAttribute)
    {
        $_productFee = new ProductFee();
        $_productFee->type = $key;
        $_productFee->name = $product->getAdditionalFees()->getStoreAdditionalFeeByKey($key)->label;
        $_productFee->order_id = $order_id;
        $_productFee->product_id = $product->getPrimaryKey();
        $_productFee->amount = $amount;
        $_productFee->local_amount = $local;
        $_productFee->currency = $product->getAdditionalFees()->getStoreAdditionalFeeByKey($key)->currency;
        if ($_productFee->save()) {
            $orderUpdateFeeAttribute[$orderAttribute] = $local;
        }
        return $_productFee;
    }
}
