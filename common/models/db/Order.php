<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id ID
 * @property int $store_id hàng của nước nào
 * @property string $type_order Hình thức mua hàng: SHOP | REQUEST | POS | SHIP
 * @property string $portal portal ebay, amazon us, amazon jp ...
 * @property int $is_quotation Đánh dấu đơn báo giá
 * @property int $quotation_status Trạng thái báo giá. 0 - pending, 1- approve, 2- deny
 * @property string $quotation_note note đơn request
 * @property int $customer_id id của customer
 * @property string $receiver_email
 * @property string $receiver_name
 * @property string $receiver_phone
 * @property string $receiver_address
 * @property int $receiver_country_id
 * @property string $receiver_country_name
 * @property int $receiver_province_id
 * @property string $receiver_province_name
 * @property int $receiver_district_id
 * @property string $receiver_district_name
 * @property string $receiver_post_code
 * @property int $receiver_address_id id address của người nhận trong bảng address
 * @property string $note_by_customer Ghi chú của customer
 * @property string $note Ghi chú cho đơn hàng
 * @property string $payment_type hinh thuc thanh toan. -online_payment, 'VT'...
 * @property int $sale_support_id Người support đơn hàng
 * @property string $support_email email người support
 * @property string $coupon_id mã giảm giá
 * @property string $coupon_code mã giảm giá
 * @property string $coupon_time thời gian sử dụng
 * @property string $revenue_xu số xu được nhận
 * @property string $xu_count số xu sử dụng
 * @property string $xu_amount giá trị quy đổi ra tiền
 * @property int $is_email_sent
 * @property int $is_sms_sent
 * @property int $total_quantity
 * @property int $promotion_id id của promotion
 * @property int $difference_money 0: mac dinh, 1: lech, 2:ẩn thông báo bằng quyền của Admin
 * @property string $utm_source
 * @property int $seller_id
 * @property string $seller_name
 * @property string $seller_store
 * @property string $total_final_amount_local số tiền cuối cùng khách hàng phải thanh toán
 * @property string $total_paid_amount_local số tiền khách hàng đã thanh toán
 * @property string $total_refund_amount_local số tiền đã hoàn trả cho khách hàng
 * @property string $total_amount_local tổng giá đơn hàng
 * @property string $total_fee_amount_local tổng phí đơn hàng
 * @property string $total_counpon_amount_local Tổng số tiền giảm giá bằng mã counpon
 * @property string $total_promotion_amount_local Tổng số tiền giảm giá do promotion
 * @property string $total_price_amount_local tổng giá tiền các item
 * @property string $total_tax_us_amount_local Tổng phí us tax
 * @property string $total_shipping_us_amount_local Tổng phí shipping us
 * @property string $total_weshop_fee_amount_local Tổng phí weshop
 * @property string $total_intl_shipping_fee_amount_local Tổng phí vận chuyển quốc tế
 * @property string $total_custom_fee_amount_local Tổng phí phụ thu
 * @property string $total_delivery_fee_amount_local Tổng phí vận chuyển nội địa
 * @property string $total_packing_fee_amount_local tổng phí đóng gỗ
 * @property string $total_inspection_fee_amount_local Tổng phí kiểm hàng
 * @property string $total_insurance_fee_amount_local Tổng phí bảo hiểm
 * @property string $total_vat_amount_local Tổng phí VAT
 * @property string $exchange_rate_fee Tỷ giá từ USD => tiền local
 * @property string $exchange_rate_purchase Tỷ giá từ tiền website gốc => tiền local. VD: yên => vnd
 * @property string $currency_purchase USD,JPY,AUD .....
 * @property string $purchase_order_id mã order purchase ( dạng list, cách nhau = dấu phẩy)
 * @property string $purchase_transaction_id Mã thanh toán Paypal với eBay, amazon thanh toán bằng thẻ, k lấy được mã giao dịch ( dạng list, cách nhau = dấu phẩy)
 * @property string $purchase_amount số tiền đã thanh toán với người bán, Số đã trừ Buck/Point ( dạng list, cách nhau = dấu phẩy)
 * @property int $purchase_account_id id tài khoản mua hàng
 * @property string $purchase_account_email email tài khoản mua hàng
 * @property string $purchase_card thẻ thanh toán
 * @property string $purchase_amount_buck số tiền buck thanh toán
 * @property string $purchase_amount_refund số tiền người bán hoàn
 * @property string $purchase_refund_transaction_id mã giao dịch hoàn
 * @property string $total_weight cân nặng tính phí
 * @property string $total_weight_temporary cân nặng tạm tính
 * @property string $new time NEW
 * @property string $purchased time PURCHASED
 * @property string $seller_shipped time SELLER_SHIPPED
 * @property string $stockin_us time STOCKIN_US
 * @property string $stockout_us time STOCKOUT_US
 * @property string $stockin_local time STOCKIN_LOCAL
 * @property string $stockout_local time STOCKOUT_LOCAL
 * @property string $at_customer time AT_CUSTOMER
 * @property string $returned time RETURNED
 * @property string $cancelled  time CANCELLED
 * @property string $lost  time LOST
 * @property string $current_status Trạng thái hiện tại của order
 * @property string $created_time Update qua behaviors tự động  
 * @property string $updated_time Update qua behaviors tự động
 * @property int $remove
 *
 * @property Customer $customer
 * @property Address $receiverAddress
 * @property SystemCountry $receiverCountry
 * @property SystemDistrict $receiverDistrict
 * @property SystemStateProvince $receiverProvince
 * @property User $saleSupport
 * @property Seller $seller
 * @property Store $store
 * @property OrderFee[] $orderFees
 * @property PackageItem[] $packageItems
 * @property Product[] $products
 * @property WalletTransaction[] $walletTransactions
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'is_quotation', 'quotation_status', 'customer_id', 'receiver_country_id', 'receiver_province_id', 'receiver_district_id', 'receiver_address_id', 'sale_support_id', 'coupon_time', 'is_email_sent', 'is_sms_sent', 'total_quantity', 'promotion_id', 'difference_money', 'seller_id', 'purchase_account_id', 'new', 'purchased', 'seller_shipped', 'stockin_us', 'stockout_us', 'stockin_local', 'stockout_local', 'at_customer', 'returned', 'cancelled', 'lost', 'created_time', 'updated_time', 'remove'], 'integer'],
            [['note_by_customer', 'note', 'seller_store', 'purchase_order_id', 'purchase_transaction_id', 'purchase_amount', 'purchase_account_email', 'purchase_card', 'purchase_refund_transaction_id', 'total_weight', 'total_weight_temporary'], 'string'],
            [['revenue_xu', 'xu_count', 'xu_amount', 'total_final_amount_local', 'total_paid_amount_local', 'total_refund_amount_local', 'total_amount_local', 'total_fee_amount_local', 'total_counpon_amount_local', 'total_promotion_amount_local', 'total_price_amount_local', 'total_tax_us_amount_local', 'total_shipping_us_amount_local', 'total_weshop_fee_amount_local', 'total_intl_shipping_fee_amount_local', 'total_custom_fee_amount_local', 'total_delivery_fee_amount_local', 'total_packing_fee_amount_local', 'total_inspection_fee_amount_local', 'total_insurance_fee_amount_local', 'total_vat_amount_local', 'exchange_rate_fee', 'exchange_rate_purchase', 'purchase_amount_buck', 'purchase_amount_refund'], 'number'],
            [['type_order', 'portal', 'quotation_note', 'receiver_email', 'receiver_name', 'receiver_phone', 'receiver_address', 'receiver_country_name', 'receiver_province_name', 'receiver_district_name', 'receiver_post_code', 'payment_type', 'support_email', 'coupon_id', 'coupon_code', 'utm_source', 'seller_name', 'currency_purchase'], 'string', 'max' => 255],
            [['current_status'], 'string', 'max' => 200],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['receiver_address_id'], 'exist', 'skipOnError' => true, 'targetClass' => Address::className(), 'targetAttribute' => ['receiver_address_id' => 'id']],
            [['receiver_country_id'], 'exist', 'skipOnError' => true, 'targetClass' => SystemCountry::className(), 'targetAttribute' => ['receiver_country_id' => 'id']],
            [['receiver_district_id'], 'exist', 'skipOnError' => true, 'targetClass' => SystemDistrict::className(), 'targetAttribute' => ['receiver_district_id' => 'id']],
            [['receiver_province_id'], 'exist', 'skipOnError' => true, 'targetClass' => SystemStateProvince::className(), 'targetAttribute' => ['receiver_province_id' => 'id']],
            [['sale_support_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sale_support_id' => 'id']],
            [['seller_id'], 'exist', 'skipOnError' => true, 'targetClass' => Seller::className(), 'targetAttribute' => ['seller_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::className(), 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'type_order' => 'Type Order',
            'portal' => 'Portal',
            'is_quotation' => 'Is Quotation',
            'quotation_status' => 'Quotation Status',
            'quotation_note' => 'Quotation Note',
            'customer_id' => 'Customer ID',
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
            'payment_type' => 'Payment Type',
            'sale_support_id' => 'Sale Support ID',
            'support_email' => 'Support Email',
            'coupon_id' => 'Coupon ID',
            'coupon_code' => 'Coupon Code',
            'coupon_time' => 'Coupon Time',
            'revenue_xu' => 'Revenue Xu',
            'xu_count' => 'Xu Count',
            'xu_amount' => 'Xu Amount',
            'is_email_sent' => 'Is Email Sent',
            'is_sms_sent' => 'Is Sms Sent',
            'total_quantity' => 'Total Quantity',
            'promotion_id' => 'Promotion ID',
            'difference_money' => 'Difference Money',
            'utm_source' => 'Utm Source',
            'seller_id' => 'Seller ID',
            'seller_name' => 'Seller Name',
            'seller_store' => 'Seller Store',
            'total_final_amount_local' => 'Total Final Amount Local',
            'total_paid_amount_local' => 'Total Paid Amount Local',
            'total_refund_amount_local' => 'Total Refund Amount Local',
            'total_amount_local' => 'Total Amount Local',
            'total_fee_amount_local' => 'Total Fee Amount Local',
            'total_counpon_amount_local' => 'Total Counpon Amount Local',
            'total_promotion_amount_local' => 'Total Promotion Amount Local',
            'total_price_amount_local' => 'Total Price Amount Local',
            'total_tax_us_amount_local' => 'Total Tax Us Amount Local',
            'total_shipping_us_amount_local' => 'Total Shipping Us Amount Local',
            'total_weshop_fee_amount_local' => 'Total Weshop Fee Amount Local',
            'total_intl_shipping_fee_amount_local' => 'Total Intl Shipping Fee Amount Local',
            'total_custom_fee_amount_local' => 'Total Custom Fee Amount Local',
            'total_delivery_fee_amount_local' => 'Total Delivery Fee Amount Local',
            'total_packing_fee_amount_local' => 'Total Packing Fee Amount Local',
            'total_inspection_fee_amount_local' => 'Total Inspection Fee Amount Local',
            'total_insurance_fee_amount_local' => 'Total Insurance Fee Amount Local',
            'total_vat_amount_local' => 'Total Vat Amount Local',
            'exchange_rate_fee' => 'Exchange Rate Fee',
            'exchange_rate_purchase' => 'Exchange Rate Purchase',
            'currency_purchase' => 'Currency Purchase',
            'purchase_order_id' => 'Purchase Order ID',
            'purchase_transaction_id' => 'Purchase Transaction ID',
            'purchase_amount' => 'Purchase Amount',
            'purchase_account_id' => 'Purchase Account ID',
            'purchase_account_email' => 'Purchase Account Email',
            'purchase_card' => 'Purchase Card',
            'purchase_amount_buck' => 'Purchase Amount Buck',
            'purchase_amount_refund' => 'Purchase Amount Refund',
            'purchase_refund_transaction_id' => 'Purchase Refund Transaction ID',
            'total_weight' => 'Total Weight',
            'total_weight_temporary' => 'Total Weight Temporary',
            'new' => 'New',
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
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
            'remove' => 'Remove',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiverAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'receiver_address_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiverCountry()
    {
        return $this->hasOne(SystemCountry::className(), ['id' => 'receiver_country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiverDistrict()
    {
        return $this->hasOne(SystemDistrict::className(), ['id' => 'receiver_district_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiverProvince()
    {
        return $this->hasOne(SystemStateProvince::className(), ['id' => 'receiver_province_id']);
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
    public function getOrderFees()
    {
        return $this->hasMany(OrderFee::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackageItems()
    {
        return $this->hasMany(PackageItem::className(), ['order_id' => 'id']);
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
    public function getWalletTransactions()
    {
        return $this->hasMany(WalletTransaction::className(), ['order_id' => 'id']);
    }
}
