<?php

use yii\db\Migration;

/**
 * Class m190220_072846_shipment
 */
class m190220_072846_shipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('shipment',[
            'id' => $this->primaryKey()->comment(''),
            'shipment_code' => $this->integer(11)->comment('mã phiếu giao, BM_CODE'),
            'warehouse_tags' => $this->text()->comment('1 list mã thẻ kho Weshop'),
            'total_weight' => $this->double()->comment('Tổng cân nặng của các món hàng'),
            'warehouse_send_id' => $this->integer(11)->comment('id kho gửi đi'),
            'customer_id' => $this->integer(11)->comment("id của customer"),
            'receiver_email' => $this->string(255)->comment(""),
            'receiver_name' => $this->string(255)->comment(""),
            'receiver_phone' => $this->string(255)->comment(""),
            'receiver_address' => $this->string(255)->comment(""),
            'receiver_country_id' => $this->integer(11)->comment(""),
            'receiver_country_name' => $this->string(255)->comment(""),
            'receiver_province_id' => $this->integer(11)->comment(""),
            'receiver_province_name' => $this->string(255)->comment(""),
            'receiver_district_id' => $this->integer(11)->comment(""),
            'receiver_district_name' => $this->string(255)->comment(""),
            'receiver_post_code' => $this->string(255)->comment(""),
            'receiver_address_id' => $this->integer(11)->comment("id address của người nhận trong bảng address"),
            'note_by_customer' => $this->text()->comment("Ghi chú của customer"),
            'note' => $this->text()->comment("Ghi chú cho đơn hàng"),
            'shipment_status' => $this->string(255)->comment("trạng thái shipment"),
            'total_shipping_fee' => $this->decimal(18,2)->comment("phí ship"),
            'total_price' => $this->decimal(18,2)->comment("Tổng giá trị shipment"),
            'total_cod' => $this->decimal(18,2)->comment("Tổng tiền thu cod"),
            'total_quantity' => $this->integer(11)->comment("Tổng số lượng"),
            'is_hold' => $this->integer(11)->comment("đánh dấu hàng hold, 0 là không hold, 1 là hold"),
            'courier_code' => $this->integer(11)->comment("mã hãng vận chuyển"),
            'courier_logo' => $this->text()->comment("logo hãng vận chuyển"),
            'courier_estimate_time' => $this->text()->comment("thời gian ước tính của hãng vận chuyển"),
            'list_old_shipment_code' => $this->text()->comment("danh sách mã shipment cũ đã bị cancel"),

            'created_time' => $this->bigInteger()->comment('thời gian tạo'),
            'updated_time' => $this->bigInteger()->comment('thời gian cập nhật'),
        ],$tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190220_072846_shipment cannot be reverted.\n";

//        foreach ($this->list as $data){
//            $this->dropIndex('idx-shipment-'.$data['column'], 'shipment');
//            $this->dropForeignKey('fk-shipment-'.$data['column'], 'shipment');
//        }
//        $this->dropTable('shipment');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190220_072846_shipment cannot be reverted.\n";

        return false;
    }
    */
}
