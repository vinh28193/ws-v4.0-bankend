<?php
/**
 * Created by PhpStorm.
 * User: galat
 * Date: 07/03/2019
 * Time: 10:41
 */

namespace common\models;

use Yii;
use common\models\queries\ShipmentQuery;
use common\models\db\Shipment as DbShipment;
/**
 * Class Shipment
 * @package common\models
 * @property PackageItem[] $packageItems
 */

class Shipment extends DbShipment
{
    const STATUS_REMOVE_SHIPMENT = "REMOVED";
    const STATUS_NEW = "NEW";
    const STATUS_LOCAL_INSPECT_DONE = "LOCAL_INSPECT_DONE";

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        $paren = parent::rules();
        $child = [
            [['warehouse_send_id', 'customer_id', 'receiver_email', 'receiver_name', 'receiver_phone', 'receiver_address', 'receiver_country_id', 'receiver_province_id', 'receiver_district_id'], 'required'],
        ];
        return array_merge($paren,$child);
    }

    /**
     * @inheritdoc
     * @return ShipmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return Yii::createObject(ShipmentQuery::className(), [get_called_class()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPackageItems()
    {
        return $this->hasMany(PackageItem::className(), ['shipment_id' => 'id']);
    }
}