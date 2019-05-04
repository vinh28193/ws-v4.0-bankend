<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "tracking_code".
 *
 * @property int $id ID
 * @property string $version version 4.0
 * @property int $store_id Store ID reference
 * @property int $manifest_id Manifest Id
 * @property string $manifest_code Manifest code
 * @property int $delivery_note_id Package id after sent
 * @property string $delivery_note_code Mã kiện của weshop
 * @property int $package_id Package item id after create item
 * @property string $tracking_code
 * @property string $order_ids Order id(s)
 * @property string $weshop_tag Weshop Tag
 * @property string $warehouse_alias warehouse alias BMVN_HN (Boxme Ha Noi/Boxme HCM)
 * @property string $warehouse_tag warehouse tag
 * @property string $warehouse_note warehouse note
 * @property string $warehouse_status warehouse status (open/close)
 * @property string $weight seller Weight (kg)
 * @property string $quantity seller quantity
 * @property string $dimension_width Width (cm)
 * @property string $dimension_length Length (cm)
 * @property string $dimension_height Height (cm)
 * @property string $operation_note Note
 * @property string $status Status
 * @property int $remove removed or not (1:Removed)
 * @property int $created_by Created by
 * @property int $created_at Created at (timestamp)
 * @property int $updated_by Updated by
 * @property int $updated_at Updated at (timestamp)
 * @property string $status_merge Trạng thái của tracking với việc đối chiếu tracking với bảng ext
 * @property int $stock_in_us
 * @property int $stock_out_us
 * @property int $stock_in_local
 * @property int $stock_out_local
 */
class TrackingCode extends \common\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tracking_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id'], 'required'],
            [['store_id', 'manifest_id', 'delivery_note_id', 'package_id', 'remove', 'created_by', 'created_at', 'updated_by', 'updated_at', 'stock_in_us', 'stock_out_us', 'stock_in_local', 'stock_out_local'], 'integer'],
            [['warehouse_note', 'operation_note'], 'string'],
            [['weight', 'quantity', 'dimension_width', 'dimension_length', 'dimension_height'], 'number'],
            [['version', 'tracking_code', 'order_ids', 'status_merge'], 'string', 'max' => 255],
            [['manifest_code', 'delivery_note_code', 'weshop_tag', 'warehouse_alias', 'warehouse_tag', 'status'], 'string', 'max' => 32],
            [['warehouse_status'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version' => 'Version',
            'store_id' => 'Store ID',
            'manifest_id' => 'Manifest ID',
            'manifest_code' => 'Manifest Code',
            'delivery_note_id' => 'Delivery Note ID',
            'delivery_note_code' => 'Delivery Note Code',
            'package_id' => 'Package ID',
            'tracking_code' => 'Tracking Code',
            'order_ids' => 'Order Ids',
            'weshop_tag' => 'Weshop Tag',
            'warehouse_alias' => 'Warehouse Alias',
            'warehouse_tag' => 'Warehouse Tag',
            'warehouse_note' => 'Warehouse Note',
            'warehouse_status' => 'Warehouse Status',
            'weight' => 'Weight',
            'quantity' => 'Quantity',
            'dimension_width' => 'Dimension Width',
            'dimension_length' => 'Dimension Length',
            'dimension_height' => 'Dimension Height',
            'operation_note' => 'Operation Note',
            'status' => 'Status',
            'remove' => 'Remove',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'status_merge' => 'Status Merge',
            'stock_in_us' => 'Stock In Us',
            'stock_out_us' => 'Stock Out Us',
            'stock_in_local' => 'Stock In Local',
            'stock_out_local' => 'Stock Out Local',
        ];
    }
}
