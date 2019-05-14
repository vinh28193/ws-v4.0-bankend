<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "payment_method".
 *
 * @property int $id ID
 * @property int $store_id Store ID reference
 * @property string $name
 * @property string $code
 * @property string $bank_code
 * @property string $description
 * @property string $icon
 * @property int $group 1: the tin dung, 2:ngan hang, 3:vi ngan luong
 * @property int $status Status (1:Active;2:Inactive)
 * @property int $created_by Created by
 * @property int $created_at Created at (timestamp)
 * @property int $updated_by Updated by
 * @property int $updated_at Updated at (timestamp)
 */
class PaymentMethod extends \common\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_method';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'name', 'code'], 'required'],
            [['store_id', 'group', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['code', 'bank_code'], 'string', 'max' => 32],
            [['description', 'icon'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'code' => 'Code',
            'bank_code' => 'Bank Code',
            'description' => 'Description',
            'icon' => 'Icon',
            'group' => 'Group',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }
}