<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "{{%product}}".
 *
 * @property int $id
 * @property int $order_id order id
 * @property int $seller_id
 * @property string $portal portal sản phẩm, ebay, amazon us, amazon jp , etc....
 * @property string $sku sku của sản phẩm
 * @property string $parent_sku sku cha
 * @property string $link_img link ảnh sản phẩm
 * @property string $link_origin link gốc sản phẩm
 * @property int $category_id id danh mục trên Website Weshop bắt qua API
 * @property int $custom_category_id id danh mục phụ thu Hải Quản nếu api ko bắt được dang mục mà do sale chọn trong OPS thì sẽ thu thêm COD
 * @property string $price_amount_origin đơn giá gốc ngoại tệ
 * @property string $price_amount_local đơn giá local
 * @property string $total_price_amount_local tổng tiền hàng của từng sản phẩm
 * @property string $total_fee_product_local tổng phí trên sản phẩm
 * @property int $quantity_customer số lượng khách đặt
 * @property int $quantity_purchase số lượng Nhân viên đã mua
 * @property int $quantity_inspect số lượng đã kiểm
 * @property string $price_purchase Giá khi nhân viên mua hàng
 * @property string $shipping_fee_purchase Phí ship khi nhân viên mua hàng
 * @property string $tax_fee_purchase Phí tax khi nhân viên mua hàng
 * @property string $variations thuộc tính sản phẩm
 * @property int $variation_id mã thuộc tính sản phẩm . Notes : Trường này để làm addon tự động mua hàng đẩy vào Giở hàng của Ebay / Amazon 
 * @property string $note_by_customer note của khách / Khách hàng ghi chú
 * @property string $total_weight_temporary
 * @property string $created_at
 * @property string $updated_at
 * @property int $remove mặc định 0 là chưa xóa 1 là ẩn 
 * @property string $product_name
 * @property string $product_link
 * @property string $version version 4.0
 * @property string $condition Tình trạng đơn hàng
 * @property string $seller_refund_amount Số tiền người bán hoàn chả
 * @property string $note_boxme
 * @property string $current_status
 * @property int $purchase_start
 * @property int $purchased
 * @property int $seller_shipped
 * @property int $stockin_us
 * @property int $stockout_us
 * @property int $stockin_local
 * @property int $stockout_local
 * @property int $at_customer
 * @property int $returned
 * @property int $cancel
 * @property int $lost
 * @property int $refunded
 * @property int $confirm_change_price 0: là không có thay đổi giá hoặc có thay đổi nhưng đã confirm. 1: là có thay đổi cần xác nhận
 * @property int $total_final_amount_local
 * @property string $tracking_codes list tracking_code seller, Cách nhau dấu (,)
 * @property string $total_final_amount_origin
 * @property string $total_price_amount_origin
 * @property string $price_policy tiền phí hải quan
 * @property int $is_special check hệ thống tự bắt hàng đặc biệt
 *
 * @property CategoryCustomPolicy $customCategory
 * @property Order $order
 * @property Seller $seller
 * @property PurchaseProduct[] $purchaseProducts
 */
class Product extends \common\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'seller_id', 'portal', 'parent_sku', 'link_img', 'link_origin', 'price_amount_origin', 'price_amount_local', 'total_price_amount_local', 'quantity_customer', 'created_at', 'product_name'], 'required'],
            [['order_id', 'seller_id', 'category_id', 'custom_category_id', 'quantity_customer', 'quantity_purchase', 'quantity_inspect', 'variation_id', 'created_at', 'updated_at', 'remove', 'purchase_start', 'purchased', 'seller_shipped', 'stockin_us', 'stockout_us', 'stockin_local', 'stockout_local', 'at_customer', 'returned', 'cancel', 'lost', 'refunded', 'confirm_change_price', 'total_final_amount_local', 'is_special'], 'integer'],
            [['link_img', 'link_origin', 'variations', 'note_by_customer', 'product_name', 'tracking_codes'], 'string'],
            [['price_amount_origin', 'price_amount_local', 'total_price_amount_local', 'total_fee_product_local', 'price_purchase', 'shipping_fee_purchase', 'tax_fee_purchase', 'total_weight_temporary', 'seller_refund_amount', 'total_final_amount_origin', 'total_price_amount_origin', 'price_policy'], 'number'],
            [['portal', 'sku', 'parent_sku', 'version', 'condition', 'note_boxme', 'current_status'], 'string', 'max' => 255],
            [['product_link'], 'string', 'max' => 500],
            [['custom_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoryCustomPolicy::className(), 'targetAttribute' => ['custom_category_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['seller_id'], 'exist', 'skipOnError' => true, 'targetClass' => Seller::className(), 'targetAttribute' => ['seller_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'seller_id' => 'Seller ID',
            'portal' => 'Portal',
            'sku' => 'Sku',
            'parent_sku' => 'Parent Sku',
            'link_img' => 'Link Img',
            'link_origin' => 'Link Origin',
            'category_id' => 'Category ID',
            'custom_category_id' => 'Custom Category ID',
            'price_amount_origin' => 'Price Amount Origin',
            'price_amount_local' => 'Price Amount Local',
            'total_price_amount_local' => 'Total Price Amount Local',
            'total_fee_product_local' => 'Total Fee Product Local',
            'quantity_customer' => 'Quantity Customer',
            'quantity_purchase' => 'Quantity Purchase',
            'quantity_inspect' => 'Quantity Inspect',
            'price_purchase' => 'Price Purchase',
            'shipping_fee_purchase' => 'Shipping Fee Purchase',
            'tax_fee_purchase' => 'Tax Fee Purchase',
            'variations' => 'Variations',
            'variation_id' => 'Variation ID',
            'note_by_customer' => 'Note By Customer',
            'total_weight_temporary' => 'Total Weight Temporary',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'remove' => 'Remove',
            'product_name' => 'Product Name',
            'product_link' => 'Product Link',
            'version' => 'Version',
            'condition' => 'Condition',
            'seller_refund_amount' => 'Seller Refund Amount',
            'note_boxme' => 'Note Boxme',
            'current_status' => 'Current Status',
            'purchase_start' => 'Purchase Start',
            'purchased' => 'Purchased',
            'seller_shipped' => 'Seller Shipped',
            'stockin_us' => 'Stockin Us',
            'stockout_us' => 'Stockout Us',
            'stockin_local' => 'Stockin Local',
            'stockout_local' => 'Stockout Local',
            'at_customer' => 'At Customer',
            'returned' => 'Returned',
            'cancel' => 'Cancel',
            'lost' => 'Lost',
            'refunded' => 'Refunded',
            'confirm_change_price' => 'Confirm Change Price',
            'total_final_amount_local' => 'Total Final Amount Local',
            'tracking_codes' => 'Tracking Codes',
            'total_final_amount_origin' => 'Total Final Amount Origin',
            'total_price_amount_origin' => 'Total Price Amount Origin',
            'price_policy' => 'Price Policy',
            'is_special' => 'Is Special',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomCategory()
    {
        return $this->hasOne(CategoryCustomPolicy::className(), ['id' => 'custom_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
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
    public function getPurchaseProducts()
    {
        return $this->hasMany(PurchaseProduct::className(), ['product_id' => 'id']);
    }
}
