<?php

use yii\db\Migration;

/**
 * Class m190220_081033_shipment_returned
 */
class m190220_081033_shipment_returned extends Migration
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
        $this->createTable('shipment_returned',[
            'id' => $this->primaryKey()->comment(''),
            'shipment_code' => $this->integer(11)->comment('mã phiếu giao, BM_CODE'),
            'warehouse_send_id' => $this->integer(11)->comment('id kho gửi đi'),
            'warehouse_tags' => $this->text()->comment('1 list mã thẻ kho Weshop'),
            'customer_id' => $this->integer(11)->comment("id của customer"),
            'shipment_status' => $this->string(255)->comment("trạng thái shipment"),
            'total_weight' => $this->double()->comment('Tổng cân nặng của các món hàng'),
            'total_shipping_fee' => $this->decimal(18,2)->comment("phí ship"),
            'total_price' => $this->decimal(18,2)->comment("Tổng giá trị shipment"),
            'total_cod' => $this->decimal(18,2)->comment("Tổng tiền thu cod"),
            'total_quantity' => $this->integer(11)->comment("Tổng số lượng"),
            'courier_code' => $this->integer(11)->comment("mã hãng vận chuyển"),
            'courier_logo' => $this->text()->comment("logo hãng vận chuyển"),
            'courier_estimate_time' => $this->text()->comment("thời gian ước tính của hãng vận chuyển"),
            'shipment_id' => $this->integer(11)->comment(""),


            'created_time' => $this->bigInteger()->comment('thời gian tạo'),
            'updated_time' => $this->bigInteger()->comment('thời gian cập nhật'),
        ],$tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190220_081033_shipment_returned cannot be reverted.\n";

//        foreach ($this->list as $data){
//            $this->dropIndex('idx-shipment_returned-'.$data['column'], 'shipment_returned');
//            $this->dropForeignKey('fk-shipment_returned-'.$data['column'], 'shipment_returned');
//        }
//        $this->dropTable('shipment_returned');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190220_081033_shipment_returned cannot be reverted.\n";

        return false;
    }
    */
}
