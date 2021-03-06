<?php

namespace common\models\db;

use Yii;

/**
 * This is the model class for table "{{%access_tokens}}".
 *
 * @property int $id
 * @property string $token
 * @property int $expires_at
 * @property string $auth_code
 * @property int $user_id
 * @property string $app_id
 * @property int $created_at
 * @property int $updated_at
 */
class AccessTokens extends \common\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%access_tokens}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'expires_at', 'auth_code', 'user_id', 'created_at', 'updated_at'], 'required'],
            [['expires_at', 'user_id', 'created_at', 'updated_at'], 'integer'],
            [['token'], 'string', 'max' => 300],
            [['auth_code', 'app_id'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('db', 'ID'),
            'token' => Yii::t('db', 'Token'),
            'expires_at' => Yii::t('db', 'Expires At'),
            'auth_code' => Yii::t('db', 'Auth Code'),
            'user_id' => Yii::t('db', 'User ID'),
            'app_id' => Yii::t('db', 'App ID'),
            'created_at' => Yii::t('db', 'Created At'),
            'updated_at' => Yii::t('db', 'Updated At'),
        ];
    }
}
