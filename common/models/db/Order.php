<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id ID
 * @property string $ordercode ordercode : BIN Code Weshop : WSVN , WSINDO
 * @property int $store_id hàng của nước nào Weshop Indo hay Weshop VIET NAM
 * @property string $type_order Hình thức mua hàng: SHOP | REQUEST | POS | SHIP
 * @property int $customer_id
 * @property string $customer_type  Mã id của customer : Retail Customer : Khách lẻ . Wholesale customers 
 * @property string $portal portal ebay, amazon us, amazon jp ...: EBAY/ AMAZON_US / AMAZON_JAPAN / OTHER / WEBSITE NGOÀI 
 * @property string $utm_source Đơn theo viết được tạo ra bới chiến dịch nào : Facebook ads, Google ads , eomobi , etc ,,,, 
 * @property string $new time NEW
 * @property string $purchase_start
 * @property string $purchased time PURCHASED
 * @property string $seller_shipped time SELLER_SHIPPED
 * @property string $stockin_us time STOCKIN_US
 * @property string $stockout_us time STOCKOUT_US
 * @property string $stockin_local time STOCKIN_LOCAL
 * @property string $stockout_local time STOCKOUT_LOCAL
 * @property string $at_customer time AT_CUSTOMER
 * @property string $returned time RETURNED : null
 * @property string $cancelled  time CANCELLED : null :  Đơn hàng đã  thanh toán --> thì hoàn  tiền ; Đơn hàng chưa thanh toán --> thì Hủy
 * @property string $lost  time LOST : null : Hàng mất ở kho Mỹ hoặc hải quan hoặc kho VN hoặc trên đường giao cho KH 
 * @property string $current_status Trạng thái hiện tại của order : update theo trạng thái của sản phẩm cuối 
 * @property int $is_quotation Đánh dấu đơn báo giá
 * @property int $quotation_status Duyệt đơn báo giá nên đơn có Trạng thái báo giá. null : là hàng SHOP ,  0 - pending, 1- approve, 2- deny
 * @property string $quotation_note note đơn request
 * @property string $buyer_email
 * @property string $buyer_name
 * @property string $buyer_address
 * @property int $buyer_country_id
 * @property string $buyer_country_name
 * @property int $buyer_province_id
 * @property string $buyer_province_name
 * @property int $buyer_district_id
 * @property string $buyer_district_name
 * @property string $buyer_post_code
 * @property string $receiver_email
 * @property string $receiver_name Họ tên người nhận
 * @property string $receiver_phone Số điện thoại người nhận
 * @property string $receiver_address Địa chỉ người nhận
 * @property int $receiver_country_id Mã Country người nhận
 * @property string $receiver_country_name Country người nhận
 * @property int $receiver_province_id  mã Tỉnh thành người nhận
 * @property string $receiver_province_name Tên Tỉnh thành người nhận
 * @property int $receiver_district_id Mã Quận huyện người nhận
 * @property string $receiver_district_name  Tên Quận huyện người nhận
 * @property string $receiver_post_code  Mã bưu điện người nhận
 * @property int $receiver_address_id
 * @property string $note_by_customer Ghi chú của customer hoặc ghi chú cho người nhận 
 * @property string $note Ghi chú cho đơn hàng
 * @property int $seller_id Mã người bán 
 * @property string $seller_name Tên người bán
 * @property string $seller_store Link shop của người bán
 * @property string $total_final_amount_local  Tổng giá trị đơn hàng ( Số tiền đã trừ đi giảm giá ) : số tiền cuối cùng khách hàng phải thanh toán và tính theo tiền local
 * @property string $total_amount_local  Tổng giá trị đơn hàng : Số tiền chưa tính giảm giá 
 * @property string $total_origin_fee_local Tổng phí gốc tại xuất xứ (Tiền Local)
 * @property string $total_price_amount_origin  Tổng Tiền Hàng ( Theo tiền ngoại tê của EBAY / AMAZON  / WEBSITE NGOÀI) : Tổng giá tiền gốc các item theo ngoại tệ 
 * @property string $total_paid_amount_local Tổng số tiền khách hàng đã thanh toán : Theo tiền local 
 * @property string $total_refund_amount_local số tiền đã hoàn trả cho khách hàng : Theo tiền local
 * @property string $total_counpon_amount_local Tổng số tiền giảm giá bằng mã counpon . Ví dụ MÃ VALENTIN200 áp dụng cho khách hàng mới 
 * @property string $total_promotion_amount_local Tổng số tiền giảm giá do promotion . Vi Dụ : Chương trình giảm giá trừ 200.000 VNĐ cho cả đơn 
 * @property string $total_fee_amount_local tổng phí đơn hàng
 * @property string $total_origin_tax_fee_local Tổng phí tax tại xuất xứ
 * @property string $total_origin_shipping_fee_local Tổng phí vận chuyển tại xuất xứ
 * @property string $total_weshop_fee_local Tổng phí Weshop
 * @property string $total_intl_shipping_fee_local Tổng phí vận chuyển quốc tế
 * @property string $total_custom_fee_amount_local Tổng phí phụ thu
 * @property string $total_delivery_fee_local Tổng phí vận chuyển nội địa
 * @property string $total_packing_fee_local Tống phí hàng
 * @property string $total_inspection_fee_local Tổng phí kiểm hàng
 * @property string $total_insurance_fee_local Tổng phí bảo hiểm
 * @property string $total_vat_amount_local Tổng phí VAT
 * @property string $exchange_rate_fee  Tỉ Giá Tính Phí Local : áp dung theo tỉ giá của VietCombank Crowler upate từ 1 bảng systeam_curentcy : Tỷ giá từ USD => tiền local
 * @property string $exchange_rate_purchase Tỉ Giá mua hàng : áp dung theo tỉ giá của VietCombank , Ẩn với Khách. Tỉ giá USD / Tỉ giá Yên / Tỉ giá UK .Tỷ giá từ tiền website gốc => tiền local. VD: yên => vnd
 * @property string $currency_purchase  Loại tiền mua hàng là : USD,JPY,AUD .....
 * @property string $payment_type hinh thuc thanh toan. -online_payment, 'VT'...
 * @property string $transaction_code
 * @property int $sale_support_id Người support đơn hàng
 * @property string $support_email email người support
 * @property int $is_email_sent  đánh đâu đơn này đã được gửi email tạo thành công đơn hàng
 * @property int $is_sms_sent đánh đâu đơn này đã được gửi SMS tạo thành công đơn hàng
 * @property int $difference_money 0: mac dinh, 1: lech, 2:ẩn thông báo bằng quyền của Admin
 * @property string $coupon_id  id mã giảm giá
 * @property string $revenue_xu số xu được nhận
 * @property string $xu_count số xu sử dụng
 * @property string $xu_amount giá trị quy đổi ra tiền
 * @property string $xu_time thời gian mốc sử dụng mã xu  
 * @property string $xu_log trừ từ xu đang có vào đơn , Quy chế sinh ra xu là khách hàng nhận được hàng thành công mới tự động sinh ra xu 
 * @property string $promotion_id id của promotion : Id Chạy chương trình promotion
 * @property string $total_weight
 * @property string $total_weight_temporary
 * @property string $created_at Update qua behaviors tự động  
 * @property string $updated_at Update qua behaviors tự động
 * @property int $purchase_assignee_id Id nhân viên mua hàng
 * @property string $purchase_order_id Mã order đặt mua với NB là EBAY / AMAZON / hoặc Website ngoài : mã order purchase ( dạng list, cách nhau = dấu phẩy)
 * @property string $purchase_transaction_id Mã thanh toán Paypal với eBay, amazon thanh toán bằng thẻ, k lấy được mã giao dịch ( dạng list, cách nhau = dấu phẩy)
 * @property string $purchase_amount số tiền thanh toán thực tế với người bán EBAY/AMAZON, lưu ý : Số đã trừ Buck/Point ( và là dạng list, cách nhau = dấu phẩy)
 * @property string $purchase_account_id id tài khoản mua hàng
 * @property string $purchase_account_email email tài khoản mua hàng
 * @property string $purchase_card thẻ thanh toán
 * @property string $purchase_amount_buck số tiền buck thanh toán
 * @property string $purchase_amount_refund số tiền người bán hoàn
 * @property string $purchase_refund_transaction_id mã giao dịch hoàn
 * @property int $total_quantity  Tổng số lượng khách hàng đặt = tổng các số lượng trên bảng product
 * @property int $total_purchase_quantity  Tổng số lượng nhân viên đi mua hàng thực tế của cả đơn = tổng các số lượng mua thực tế trên bảng product
 * @property int $remove đơn đánh đấu 1 là đã xóa , mặc định 0 : chưa xóa
 * @property string $version version 4.0
 * @property string $mark_supporting
 * @property string $supported
 * @property string $ready_purchase
 * @property string $supporting
 * @property int $check_update_payment
 * @property int $confirm_change_price 0: là không có thay đổi giá hoặc có thay đổi nhưng đã confirm. 1: là có thay đổi cần xác nhận
 * @property int $potential 0 là khách hàng binh thường, 1 là khách hàng tiềm năng
 * @property string $courier_service
 * @property string $courier_name
 * @property string $courier_delivery_time
 * @property string $buyer_phone
 * @property int $additional_service dịch vụ cộng thêm
 * @property int $check_insurance 0 - không chọn bảo hiểm; 1- Có chọn bảo hiểm
 * @property int $check_inspection 0 - không kiểm hàng; 1- Có kiểm hàng
 * @property int $boxed_fee phí đóng gỗ, đóng hộp
 * @property int $check_packing_wood 1 là có đóng gỗ, 0 là không đóng gỗ
 * @property string $total_intl_shipping_fee_amount
 * @property string $total_origin_tax_fee_amount
 * @property string $total_weshop_fee_amount
 * @property string $total_boxed_fee_amount
 * @property string $total_origin_shipping_fee_amount
 * @property string $total_vat_amount_amount
 * @property string $note_update_payment note khi chỉnh sửa payment
 * @property string $payment_provider
 * @property string $payment_method
 * @property string $payment_bank
 * @property string $payment_transaction_code
 * @property string $total_custom_fee_amount tiền phí hải quan amount
 * @property string $tracking_codes Nhiều tracking cách nhau dấu ,
 * @property string $purchase_note
 * @property string $contacting time contacting
 * @property string $awaiting_payment time chờ thanh toán
 * @property string $awaiting_confirm_purchase time chờ mua hàng
 * @property string $delivering time đang giao hàng
 * @property string $delivered time đã giao hàng
 * @property string $purchasing time đã giao hàng
 * @property string $junk time đơn hàng rác
 * @property string $refunded time hoàn trả lại tiền
 * @property string $order_boxme
 * @property string $shipment_boxme
 * @property string $transfer_to Order code của order nhận được số tiền chuyển
 * @property int $refund_transfer Thời gian chuyển
 * @property int $is_special
 *
 * @property User $saleSupport
 * @property Seller $seller
 * @property Store $store
 * @property User $purchaseAssignee
 * @property Product[] $products
 * @property PurchaseProduct[] $purchaseProducts
 * @property QueuedEmail[] $queuedEmails
 */
class Order extends \common\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'type_order', 'customer_type', 'portal', 'buyer_email', 'buyer_name', 'buyer_address', 'buyer_country_id', 'buyer_country_name', 'buyer_province_id', 'buyer_province_name', 'buyer_district_id', 'buyer_district_name', 'receiver_name', 'receiver_phone', 'receiver_address', 'receiver_country_id', 'receiver_country_name', 'receiver_province_id', 'receiver_province_name', 'receiver_district_id', 'receiver_district_name', 'payment_type', 'buyer_phone'], 'required'],
            [['store_id', 'customer_id', 'new', 'purchase_start', 'purchased', 'seller_shipped', 'stockin_us', 'stockout_us', 'stockin_local', 'stockout_local', 'at_customer', 'returned', 'cancelled', 'lost', 'is_quotation', 'quotation_status', 'buyer_country_id', 'buyer_province_id', 'buyer_district_id', 'receiver_country_id', 'receiver_province_id', 'receiver_district_id', 'receiver_address_id', 'seller_id', 'sale_support_id', 'is_email_sent', 'is_sms_sent', 'difference_money', 'coupon_id', 'xu_time', 'promotion_id', 'created_at', 'updated_at', 'purchase_assignee_id', 'total_quantity', 'total_purchase_quantity', 'remove', 'mark_supporting', 'supported', 'ready_purchase', 'supporting', 'check_update_payment', 'confirm_change_price', 'potential', 'additional_service', 'check_insurance', 'check_inspection', 'boxed_fee', 'check_packing_wood', 'contacting', 'awaiting_payment', 'awaiting_confirm_purchase', 'delivering', 'delivered', 'purchasing', 'junk', 'refunded', 'refund_transfer', 'is_special'], 'integer'],
            [['note_by_customer', 'note', 'seller_store', 'purchase_order_id', 'purchase_transaction_id', 'purchase_account_id', 'purchase_account_email', 'purchase_card', 'purchase_refund_transaction_id', 'note_update_payment', 'tracking_codes', 'purchase_note', 'shipment_boxme'], 'string'],
            [['total_final_amount_local', 'total_amount_local', 'total_origin_fee_local', 'total_price_amount_origin', 'total_paid_amount_local', 'total_refund_amount_local', 'total_counpon_amount_local', 'total_promotion_amount_local', 'total_fee_amount_local', 'total_origin_tax_fee_local', 'total_origin_shipping_fee_local', 'total_weshop_fee_local', 'total_intl_shipping_fee_local', 'total_custom_fee_amount_local', 'total_delivery_fee_local', 'total_packing_fee_local', 'total_inspection_fee_local', 'total_insurance_fee_local', 'total_vat_amount_local', 'exchange_rate_fee', 'exchange_rate_purchase', 'revenue_xu', 'xu_count', 'xu_amount', 'total_weight', 'total_weight_temporary', 'purchase_amount', 'purchase_amount_buck', 'purchase_amount_refund', 'total_intl_shipping_fee_amount', 'total_origin_tax_fee_amount', 'total_weshop_fee_amount', 'total_boxed_fee_amount', 'total_origin_shipping_fee_amount', 'total_vat_amount_amount', 'total_custom_fee_amount'], 'number'],
            [['ordercode', 'type_order', 'portal', 'utm_source', 'quotation_note', 'buyer_email', 'buyer_name', 'buyer_address', 'buyer_country_name', 'buyer_province_name', 'buyer_district_name', 'buyer_post_code', 'receiver_email', 'receiver_name', 'receiver_phone', 'receiver_address', 'receiver_country_name', 'receiver_province_name', 'receiver_district_name', 'receiver_post_code', 'seller_name', 'currency_purchase', 'payment_type', 'support_email', 'xu_log', 'version', 'courier_name', 'courier_delivery_time', 'buyer_phone', 'payment_provider', 'payment_method', 'payment_bank', 'order_boxme', 'transfer_to'], 'string', 'max' => 255],
            [['customer_type'], 'string', 'max' => 11],
            [['current_status'], 'string', 'max' => 200],
            [['transaction_code', 'courier_service', 'payment_transaction_code'], 'string', 'max' => 32],
            [['sale_support_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sale_support_id' => 'id']],
            [['seller_id'], 'exist', 'skipOnError' => true, 'targetClass' => Seller::className(), 'targetAttribute' => ['seller_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::className(), 'targetAttribute' => ['store_id' => 'id']],
            [['purchase_assignee_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['purchase_assignee_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ordercode' => 'Ordercode',
            'store_id' => 'Store ID',
            'type_order' => 'Type Order',
            'customer_id' => 'Customer ID',
            'customer_type' => 'Customer Type',
            'portal' => 'Portal',
            'utm_source' => 'Utm Source',
            'new' => 'New',
            'purchase_start' => 'Purchase Start',
            'purchased' => 'Purchased',
            'seller_shipped' => 'Seller Shipped',
            'stockin_us' => 'Stockin Us',
            'stockout_us' => 'Stockout Us',
            'stockin_local' => 'Stockin Local',
            'stockout_local' => 'Stockout Local',
            'at_customer' => 'At Customer',
            'returned' => 'Returned',
            'cancelled' => 'Cancelled',
            'lost' => 'Lost',
            'current_status' => 'Current Status',
            'is_quotation' => 'Is Quotation',
            'quotation_status' => 'Quotation Status',
            'quotation_note' => 'Quotation Note',
            'buyer_email' => 'Buyer Email',
            'buyer_name' => 'Buyer Name',
            'buyer_address' => 'Buyer Address',
            'buyer_country_id' => 'Buyer Country ID',
            'buyer_country_name' => 'Buyer Country Name',
            'buyer_province_id' => 'Buyer Province ID',
            'buyer_province_name' => 'Buyer Province Name',
            'buyer_district_id' => 'Buyer District ID',
            'buyer_district_name' => 'Buyer District Name',
            'buyer_post_code' => 'Buyer Post Code',
            'receiver_email' => 'Receiver Email',
            'receiver_name' => 'Receiver Name',
            'receiver_phone' => 'Receiver Phone',
            'receiver_address' => 'Receiver Address',
            'receiver_country_id' => 'Receiver Country ID',
            'receiver_country_name' => 'Receiver Country Name',
            'receiver_province_id' => 'Receiver Province ID',
            'receiver_province_name' => 'Receiver Province Name',
            'receiver_district_id' => 'Receiver District ID',
            'receiver_district_name' => 'Receiver District Name',
            'receiver_post_code' => 'Receiver Post Code',
            'receiver_address_id' => 'Receiver Address ID',
            'note_by_customer' => 'Note By Customer',
            'note' => 'Note',
            'seller_id' => 'Seller ID',
            'seller_name' => 'Seller Name',
            'seller_store' => 'Seller Store',
            'total_final_amount_local' => 'Total Final Amount Local',
            'total_amount_local' => 'Total Amount Local',
            'total_origin_fee_local' => 'Total Origin Fee Local',
            'total_price_amount_origin' => 'Total Price Amount Origin',
            'total_paid_amount_local' => 'Total Paid Amount Local',
            'total_refund_amount_local' => 'Total Refund Amount Local',
            'total_counpon_amount_local' => 'Total Counpon Amount Local',
            'total_promotion_amount_local' => 'Total Promotion Amount Local',
            'total_fee_amount_local' => 'Total Fee Amount Local',
            'total_origin_tax_fee_local' => 'Total Origin Tax Fee Local',
            'total_origin_shipping_fee_local' => 'Total Origin Shipping Fee Local',
            'total_weshop_fee_local' => 'Total Weshop Fee Local',
            'total_intl_shipping_fee_local' => 'Total Intl Shipping Fee Local',
            'total_custom_fee_amount_local' => 'Total Custom Fee Amount Local',
            'total_delivery_fee_local' => 'Total Delivery Fee Local',
            'total_packing_fee_local' => 'Total Packing Fee Local',
            'total_inspection_fee_local' => 'Total Inspection Fee Local',
            'total_insurance_fee_local' => 'Total Insurance Fee Local',
            'total_vat_amount_local' => 'Total Vat Amount Local',
            'exchange_rate_fee' => 'Exchange Rate Fee',
            'exchange_rate_purchase' => 'Exchange Rate Purchase',
            'currency_purchase' => 'Currency Purchase',
            'payment_type' => 'Payment Type',
            'transaction_code' => 'Transaction Code',
            'sale_support_id' => 'Sale Support ID',
            'support_email' => 'Support Email',
            'is_email_sent' => 'Is Email Sent',
            'is_sms_sent' => 'Is Sms Sent',
            'difference_money' => 'Difference Money',
            'coupon_id' => 'Coupon ID',
            'revenue_xu' => 'Revenue Xu',
            'xu_count' => 'Xu Count',
            'xu_amount' => 'Xu Amount',
            'xu_time' => 'Xu Time',
            'xu_log' => 'Xu Log',
            'promotion_id' => 'Promotion ID',
            'total_weight' => 'Total Weight',
            'total_weight_temporary' => 'Total Weight Temporary',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'purchase_assignee_id' => 'Purchase Assignee ID',
            'purchase_order_id' => 'Purchase Order ID',
            'purchase_transaction_id' => 'Purchase Transaction ID',
            'purchase_amount' => 'Purchase Amount',
            'purchase_account_id' => 'Purchase Account ID',
            'purchase_account_email' => 'Purchase Account Email',
            'purchase_card' => 'Purchase Card',
            'purchase_amount_buck' => 'Purchase Amount Buck',
            'purchase_amount_refund' => 'Purchase Amount Refund',
            'purchase_refund_transaction_id' => 'Purchase Refund Transaction ID',
            'total_quantity' => 'Total Quantity',
            'total_purchase_quantity' => 'Total Purchase Quantity',
            'remove' => 'Remove',
            'version' => 'Version',
            'mark_supporting' => 'Mark Supporting',
            'supported' => 'Supported',
            'ready_purchase' => 'Ready Purchase',
            'supporting' => 'Supporting',
            'check_update_payment' => 'Check Update Payment',
            'confirm_change_price' => 'Confirm Change Price',
            'potential' => 'Potential',
            'courier_service' => 'Courier Service',
            'courier_name' => 'Courier Name',
            'courier_delivery_time' => 'Courier Delivery Time',
            'buyer_phone' => 'Buyer Phone',
            'additional_service' => 'Additional Service',
            'check_insurance' => 'Check Insurance',
            'check_inspection' => 'Check Inspection',
            'boxed_fee' => 'Boxed Fee',
            'check_packing_wood' => 'Check Packing Wood',
            'total_intl_shipping_fee_amount' => 'Total Intl Shipping Fee Amount',
            'total_origin_tax_fee_amount' => 'Total Origin Tax Fee Amount',
            'total_weshop_fee_amount' => 'Total Weshop Fee Amount',
            'total_boxed_fee_amount' => 'Total Boxed Fee Amount',
            'total_origin_shipping_fee_amount' => 'Total Origin Shipping Fee Amount',
            'total_vat_amount_amount' => 'Total Vat Amount Amount',
            'note_update_payment' => 'Note Update Payment',
            'payment_provider' => 'Payment Provider',
            'payment_method' => 'Payment Method',
            'payment_bank' => 'Payment Bank',
            'payment_transaction_code' => 'Payment Transaction Code',
            'total_custom_fee_amount' => 'Total Custom Fee Amount',
            'tracking_codes' => 'Tracking Codes',
            'purchase_note' => 'Purchase Note',
            'contacting' => 'Contacting',
            'awaiting_payment' => 'Awaiting Payment',
            'awaiting_confirm_purchase' => 'Awaiting Confirm Purchase',
            'delivering' => 'Delivering',
            'delivered' => 'Delivered',
            'purchasing' => 'Purchasing',
            'junk' => 'Junk',
            'refunded' => 'Refunded',
            'order_boxme' => 'Order Boxme',
            'shipment_boxme' => 'Shipment Boxme',
            'transfer_to' => 'Transfer To',
            'refund_transfer' => 'Refund Transfer',
            'is_special' => 'Is Special',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaleSupport()
    {
        return $this->hasOne(User::className(), ['id' => 'sale_support_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeller()
    {
        return $this->hasOne(Seller::className(), ['id' => 'seller_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseAssignee()
    {
        return $this->hasOne(User::className(), ['id' => 'purchase_assignee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseProducts()
    {
        return $this->hasMany(PurchaseProduct::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQueuedEmails()
    {
        return $this->hasMany(QueuedEmail::className(), ['OrderId' => 'id']);
    }
}
