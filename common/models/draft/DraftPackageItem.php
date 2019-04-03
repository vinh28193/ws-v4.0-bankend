<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-04-03
 * Time: 10:53
 */

namespace common\models\draft;


use yii\helpers\ArrayHelper;

class DraftPackageItem extends \common\models\db\DraftPackageItem
{

    /**
     * @return array
     */
    public function confidentialFields()
    {
        return ArrayHelper::merge(parent::confidentialFields(), [
            'product_id',
            'order_id',
            'status',
            'updated_at',
            'manifest_id'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasMany(\common\models\Order::className(), ['id' => 'order_id']);
    }

    public function getProduct()
    {
        return $this->hasMany(\common\models\Product::className(), ['id' => 'product_id']);
    }

    public function fields()
    {
        $fields = parent::fields();
        $dimension = [];
        foreach (['dimension_l', 'dimension_w', 'dimension_h'] as $name) {
            if ($this->$name === null) {
                continue;
            }
            unset($fields[$name]);
            $key = str_replace('dimension_', '', $name);
            $dimension[$key] = $this->$name;
        }
        $fields['volume_label'] = function ($model) use ($dimension) {
            return !empty($dimension) ? implode('.', array_keys($dimension)) : 'l.w.h';
        };
        $fields['volume'] = function ($model) use ($dimension) {
            return !empty($dimension) ? implode('x', array_values($dimension)) : null;
        };
        $fields['dimension'] = function ($model) use ($dimension) {
            if (empty($dimension) || count($dimension) < 3) {
                return 0;
            }
            $weight = 1;
            foreach ($dimension as $key => $value) {
                $weight *= $value;
            }
            return $weight / 5000;
        };
        return $fields;
    }
}