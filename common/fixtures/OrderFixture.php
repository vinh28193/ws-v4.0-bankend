<?php
/**
 * Created by PhpStorm.
 * User: galat
 * Date: 22/02/2019
 * Time: 14:55
 */

namespace common\fixtures;


use common\models\Order;
use yii\test\ActiveFixture;

class OrderFixture extends ActiveFixture
{
    public $modelClass = 'common\models\db\Order';
    public $depends = [
        'common\fixtures\SystemCountryFixture',
        'common\fixtures\SystemStateProvinceFixture',
        'common\fixtures\SystemDistrictFixture',
        'common\fixtures\CustomerFixture',
        'common\fixtures\StoreFixture',
        'common\fixtures\AddressFixture',
        'common\fixtures\UserFixture',
        'common\fixtures\SellerFixture',
        'common\fixtures\CouponFixture',
    ];
    public $dataFile = '@common/fixtures/data/data_fixed/order.php';

    /*
    public function load()
    {
        echo "<pre>";
        print_r($this->getData());
        echo "</pre>";
        die;

        $this->data = [];
        $table = $this->getTableSchema();
        foreach ($this->getData() as $alias => $row) {
            $fee = $row['fees'];
            unset($row['fees']);
            // @var  $order Order
            $order = new Order();
            //$order->attributes = $row;
            $order->setAttributes($row, false);
            //$order->setAdditionalFees($fee,true,true);
            $order->total_final_amount_local = $order->total_amount_local + $order->total_fee_amount_local;
            $order->save(false);
//            $primaryKeys = $this->db->schema->insert($table->fullName, $row);
            $this->data[$alias] = array_merge($row, (array)$order->primaryKey);
        }
    }
    */
}
