<?php

use yii\db\Migration;

/**
 * Class m190219_080356_order
 */
class m190219_080356_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        /**ToDo :
         * 1. 'customer_type' => $this->string(11)->notNull()->comment(" Mã id của customer : Retail Customer : Khách lẻ . Wholesale customers "),
              Cần chuyển / dánh dấu lại email khách hàng là Buôn hay lẻ khi mua hàng để lấy luôn id + loại khách hàng update vào bảng Order để tiện tính toán ko phải load lại bảng customer gây chậm hệ thống,
           2. current_status  trạng thái hiện tại của order không phản ánh hết từng trang thái product mà nó được update theo thời gian + sản phẩm cuối cùng
         **/

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('order',[
            'id' => $this->primaryKey()->comment("ID"),
            'store_id' => $this->integer(11)->notNull()->comment("hàng của nước nào"),
            'type_order' => $this->string(255)->notNull()->comment("Hình thức mua hàng: SHOP | REQUEST | POS | SHIP"),
            'customer_id' => $this->integer(11)->notNull()->comment(" Mã id của customer : có thể là khách buôn hoặc khách lẻ "),
            'customer_type' => $this->string(11)->notNull()->comment(" Mã id của customer : Retail Customer : Khách lẻ . Wholesale customers "),
            'portal' => $this->string(255)->notNull()->comment("portal ebay, amazon us, amazon jp ...: EBAY/ AMAZON_US / AMAZON_JAPAN / OTHER / WEBSITE NGOÀI "),


            'new' => $this->bigInteger()->comment("time NEW"),
            'purchased' => $this->bigInteger()->comment("time PURCHASED"),
            'seller_shipped' => $this->bigInteger()->comment("time SELLER_SHIPPED"),
            'stockin_us' => $this->bigInteger()->comment("time STOCKIN_US"),
            'stockout_us' => $this->bigInteger()->comment("time STOCKOUT_US"),
            'stockin_local' => $this->bigInteger()->comment("time STOCKIN_LOCAL"),
            'stockout_local' => $this->bigInteger()->comment("time STOCKOUT_LOCAL"),
            'at_customer' => $this->bigInteger()->comment("time AT_CUSTOMER"),
            'returned' => $this->bigInteger()->comment("time RETURNED : null"),
            'cancelled' => $this->bigInteger()->comment(" time CANCELLED : null :  Đơn hàng đã  thanh toán --> thì hoàn  tiền ; Đơn hàng chưa thanh toán --> thì Hủy"),
            'lost' => $this->bigInteger()->comment(" time LOST : null : Hàng mất ở kho Mỹ hoặc hải quan hoặc kho VN hoặc trên đường giao cho KH "),
            'current_status' => $this->string(200)->comment("Trạng thái hiện tại của order : update theo trạng thái của sản phẩm cuối "),


            'is_quotation' => $this->tinyInteger(4)->comment("Đánh dấu đơn báo giá"),
            'quotation_status' => $this->tinyInteger(4)->comment("Duyệt đơn báo giá nên đơn có Trạng thái báo giá. 0 - pending, 1- approve, 2- deny"),
            'quotation_note' => $this->string(255)->comment("note đơn request"),

            'receiver_email' => $this->string(255)->notNull()->comment("Email người nhận"),
            'receiver_name' => $this->string(255)->notNull()->comment("Họ tên người nhận"),
            'receiver_phone' => $this->string(255)->notNull()->comment("Số điện thoại người nhận"),
            'receiver_address' => $this->string(255)->notNull()->comment("Địa chỉ người nhận"),
            'receiver_country_id' => $this->integer(11)->notNull()->comment("Mã Country người nhận"),
            'receiver_country_name' => $this->string(255)->notNull()->comment("Country người nhận"),
            'receiver_province_id' => $this->integer(11)->notNull()->comment(" mã Tỉnh thành người nhận"),
            'receiver_province_name' => $this->string(255)->notNull()->comment("Tên Tỉnh thành người nhận"),
            'receiver_district_id' => $this->integer(11)->notNull()->comment("Mã Quận huyện người nhận"),
            'receiver_district_name' => $this->string(255)->notNull()->comment(" Tên Quận huyện người nhận"),
            'receiver_post_code' => $this->string(255)->notNull()->comment(" Mã bưu điện người nhận"),
            'receiver_address_id' => $this->integer(11)->notNull()->comment("id address của người nhận trong bảng address"),
            'note_by_customer' => $this->text()->comment("Ghi chú của customer hoặc ghi chú cho người nhận "),
            'note' => $this->text()->comment("Ghi chú cho đơn hàng"),


            'payment_type' => $this->string(255)->notNull()->comment("hinh thuc thanh toan. -online_payment, 'VT'..."),

            'sale_support_id' => $this->integer(11)->comment("Người support đơn hàng"),
            'support_email' => $this->string(255)->comment("email người support"),
            'coupon_id' => $this->string(255)->comment("mã giảm giá"),
            'coupon_code' => $this->string(255)->comment("mã giảm giá"),
            'coupon_time' => $this->bigInteger()->comment("thời gian sử dụng"),
            'revenue_xu' => $this->decimal(18,2)->comment("số xu được nhận"),
            'xu_count' => $this->decimal(18,2)->comment("số xu sử dụng"),
            'xu_amount' => $this->decimal(18,2)->comment("giá trị quy đổi ra tiền"),
            'is_email_sent' => $this->tinyInteger(1)->comment(" đánh đâu đơn này đã được gửi email tạo thành công đơn hàng"),
            'is_sms_sent' => $this->tinyInteger(1)->comment("đánh đâu đơn này đã được gửi SMS tạo thành công đơn hàng"),
            //'total_quantity' => $this->integer(11)->comment(" Tổng số lượng khách hàng đặt = tổng các số lượng trên bảng product"),
            'promotion_id' => $this->integer(11)->comment("id của promotion"),
            'difference_money' => $this->tinyInteger(1)->comment("0: mac dinh, 1: lech, 2:ẩn thông báo bằng quyền của Admin"),
            'utm_source' => $this->string(255)->comment("Đơn theo viết được tạo ra bới chiến dịch nào : Facebook ads, Google ads , eomobi , etc ,,,, "),
            'seller_id' => $this->integer(11)->comment("Mã người bán "),
            'seller_name' => $this->string(255)->comment("Tên người bán"),
            'seller_store' => $this->text()->comment("Link shop của người bán"),
            'total_final_amount_local' => $this->decimal(18,2)->comment("số tiền cuối cùng khách hàng phải thanh toán"),
            'total_paid_amount_local' => $this->decimal(18,2)->comment("số tiền khách hàng đã thanh toán"),
            'total_refund_amount_local' => $this->decimal(18,2)->comment("số tiền đã hoàn trả cho khách hàng"),
            'total_amount_local' => $this->decimal(18,2)->comment("tổng giá đơn hàng"),
            'total_fee_amount_local' => $this->decimal(18,2)->comment("tổng phí đơn hàng"),
            'total_counpon_amount_local' => $this->decimal(18,2)->comment("Tổng số tiền giảm giá bằng mã counpon"),
            'total_promotion_amount_local' => $this->decimal(18,2)->comment("Tổng số tiền giảm giá do promotion"),
            'total_price_amount_local' => $this->decimal(18,2)->comment("tổng giá tiền các item"),
            'total_tax_us_amount_local' => $this->decimal(18,2)->comment("Tổng phí us tax"),
            'total_shipping_us_amount_local' => $this->decimal(18,2)->comment("Tổng phí shipping us"),
            'total_weshop_fee_amount_local' => $this->decimal(18,2)->comment("Tổng phí weshop"),
            'total_intl_shipping_fee_amount_local' => $this->decimal(18,2)->comment("Tổng phí vận chuyển quốc tế"),
            'total_custom_fee_amount_local' => $this->decimal(18,2)->comment("Tổng phí phụ thu"),
            'total_delivery_fee_amount_local' => $this->decimal(18,2)->comment("Tổng phí vận chuyển nội địa"),
            'total_packing_fee_amount_local' => $this->decimal(18,2)->comment("tổng phí đóng gỗ"),
            'total_inspection_fee_amount_local' => $this->decimal(18,2)->comment("Tổng phí kiểm hàng"),
            'total_insurance_fee_amount_local' => $this->decimal(18,2)->comment("Tổng phí bảo hiểm"),
            'total_vat_amount_local' => $this->decimal(18,2)->comment("Tổng phí VAT"),
            'exchange_rate_fee' => $this->decimal(18,2)->comment("Tỷ giá từ USD => tiền local"),
            'exchange_rate_purchase' => $this->decimal(18,2)->comment("Tỷ giá từ tiền website gốc => tiền local. VD: yên => vnd"),
            'currency_purchase' => $this->string(255)->comment("USD,JPY,AUD ....."),
            'purchase_order_id' => $this->text()->comment("Mã order đặt mua với NB : mã order purchase ( dạng list, cách nhau = dấu phẩy)"),
            'purchase_transaction_id' => $this->text()->comment("Mã thanh toán Paypal với eBay, amazon thanh toán bằng thẻ, k lấy được mã giao dịch ( dạng list, cách nhau = dấu phẩy)"),
            'purchase_amount' => $this->text()->comment("số tiền đã thanh toán với người bán, Số đã trừ Buck/Point ( dạng list, cách nhau = dấu phẩy)"),
            'purchase_account_id' => $this->integer(11)->comment("id tài khoản mua hàng"),
            'purchase_account_email' => $this->text()->comment("email tài khoản mua hàng"),
            'purchase_card' => $this->text()->comment("thẻ thanh toán"),
            'purchase_amount_buck' => $this->decimal(18,2)->comment("số tiền buck thanh toán"),
            'purchase_amount_refund' => $this->decimal(18,2)->comment("số tiền người bán hoàn"),
            'purchase_refund_transaction_id' => $this->text()->comment("mã giao dịch hoàn"),
            'total_weight' => $this->text()->comment("cân nặng tính phí"),
            'total_weight_temporary' => $this->text()->comment("cân nặng tạm tính"),

            'created_time' => $this->bigInteger()->comment("Update qua behaviors tự động  "),
            'updated_time' => $this->bigInteger()->comment("Update qua behaviors tự động"),
            'remove' => $this->tinyInteger(4)->comment("đơn đánh đấu 1 là đã xóa , mặc định 0 : chưa xóa")
        ], $tableOptions);
    }

    /******
     *  'new' => $this->bigInteger()->comment("time NEW"),
        'purchased' => $this->bigInteger()->comment("time PURCHASED"),
        'seller_shipped' => $this->bigInteger()->comment("time SELLER_SHIPPED"),
        'stockin_us' => $this->bigInteger()->comment("time STOCKIN_US"),
        'stockout_us' => $this->bigInteger()->comment("time STOCKOUT_US"),
        'stockin_local' => $this->bigInteger()->comment("time STOCKIN_LOCAL"),
        'stockout_local' => $this->bigInteger()->comment("time STOCKOUT_LOCAL"),
        'at_customer' => $this->bigInteger()->comment("time AT_CUSTOMER"),
        'returned' => $this->bigInteger()->comment("time RETURNED"),
        'cancelled' => $this->bigInteger()->comment(" time CANCELLED :  Đơn hàng đã  hoặc chưa thanh toán --> nhưng bị hủy và hoàn tiền"),
        'lost' => $this->bigInteger()->comment(" time LOST : Hàng mất ở kho Mỹ hoặc hải quan hoặc kho VN hoặc trên đường giao cho KH "),
     * Todo :
     * 1. Sales Chipo chuyển trang thái đơn chăm như thế nào ? Khach chat và nhìn thấy trạng thái cuối sản phẩm và đơn hàng ntn ?
     */

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190219_080356_order cannot be reverted.\n";

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190219_080356_order cannot be reverted.\n";

        return false;
    }
    */
}
