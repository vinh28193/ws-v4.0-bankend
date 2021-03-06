<?php

namespace frontend\modules\favorites\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use frontend\modules\favorites\query\FavoriteQuery;

/**
 * This is the base model class for table "{{%favorites}}".
 *
 * @property integer $id
 * @property integer $obj_id
 * @property string $obj_type
 * @property string $ip
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 */
class Favorite extends \yii\db\ActiveRecord
{
   // use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obj_id', 'obj_type', 'ip'], 'required'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['ip'], 'string', 'max' => 255],
            [['obj_type'],'safe'],
            [['obj_id'],'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%favorites}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'obj_id' => Yii::t('app', 'Obj ID'),
            'obj_type' => Yii::t('app', 'Obj Type'),
            'ip' => Yii::t('app', 'Ip'),
        ];
    }


    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at'
            ]
        ];
    }


    /**
     * @inheritdoc
     * @return FavoriteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new  FavoriteQuery(get_called_class());
    }
}
