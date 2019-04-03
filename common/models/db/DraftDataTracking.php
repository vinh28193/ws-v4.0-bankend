<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "draft_data_tracking".
 *
 * @property int $id
 * @property string $tracking_code
 * @property int $product_id
 * @property int $order_id
 * @property int $manifest_id
 * @property string $manifest_code
 * @property int $quantity
 * @property double $weight
 * @property double $dimension_l
 * @property double $dimension_w
 * @property double $dimension_h
 * @property string $purchase_invoice_number
 * @property int $number_get_detail Số lần chạy api lấy detail
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class DraftDataTracking extends \common\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'draft_data_tracking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tracking_code'], 'required'],
            [['product_id', 'order_id', 'manifest_id', 'quantity', 'number_get_detail', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['weight', 'dimension_l', 'dimension_w', 'dimension_h'], 'number'],
            [['tracking_code', 'manifest_code', 'purchase_invoice_number', 'status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tracking_code' => 'Tracking Code',
            'product_id' => 'Product ID',
            'order_id' => 'Order ID',
            'manifest_id' => 'Manifest ID',
            'manifest_code' => 'Manifest Code',
            'quantity' => 'Quantity',
            'weight' => 'Weight',
            'dimension_l' => 'Dimension L',
            'dimension_w' => 'Dimension W',
            'dimension_h' => 'Dimension H',
            'purchase_invoice_number' => 'Purchase Invoice Number',
            'number_get_detail' => 'Number Get Detail',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\models\queries\DraftDataTrackingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\queries\DraftDataTrackingQuery(get_called_class());
    }
}