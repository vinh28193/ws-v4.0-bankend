<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "payment_provider".
 *
 * @property int $id ID
 * @property int $store_id Store ID reference
 * @property string $name
 * @property string $code
 * @property string $description
 * @property string $return_url
 * @property string $cancel_url
 * @property string $submit_url
 * @property string $background_url
 * @property string $image_url
 * @property string $logo_url
 * @property string $pending_url
 * @property int $rc
 * @property string $alg
 * @property string $bmod
 * @property string $merchant_id
 * @property string $secret_key
 * @property string $aes_iv
 * @property string $portal
 * @property string $token
 * @property string $wsdl
 * @property int $status Status (1:Active;2:Inactive)
 * @property int $created_by Created by
 * @property int $created_at Created at (timestamp)
 * @property int $updated_by Updated by
 * @property int $updated_at Updated at (timestamp)
 */
class PaymentProvider extends \common\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_provider';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'name', 'code'], 'required'],
            [['store_id', 'rc', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['code', 'merchant_id'], 'string', 'max' => 32],
            [['description', 'return_url', 'cancel_url', 'submit_url', 'background_url', 'image_url', 'logo_url', 'pending_url', 'bmod', 'secret_key'], 'string', 'max' => 255],
            [['alg'], 'string', 'max' => 10],
            [['aes_iv', 'wsdl'], 'string', 'max' => 50],
            [['portal', 'token'], 'string', 'max' => 20],
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
            'description' => 'Description',
            'return_url' => 'Return Url',
            'cancel_url' => 'Cancel Url',
            'submit_url' => 'Submit Url',
            'background_url' => 'Background Url',
            'image_url' => 'Image Url',
            'logo_url' => 'Logo Url',
            'pending_url' => 'Pending Url',
            'rc' => 'Rc',
            'alg' => 'Alg',
            'bmod' => 'Bmod',
            'merchant_id' => 'Merchant ID',
            'secret_key' => 'Secret Key',
            'aes_iv' => 'Aes Iv',
            'portal' => 'Portal',
            'token' => 'Token',
            'wsdl' => 'Wsdl',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }
}
