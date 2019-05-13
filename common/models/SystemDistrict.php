<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class SystemDistrict extends \common\models\db\SystemDistrict
{

    public static function select2Data($country = 1, $dataKey = 'id', $dataValue = 'name', $refreshCache = false)
    {
        $cacheKey = ['SystemDistrict', $dataKey, $dataValue];
        if (!($provinces = Yii::$app->cache->get($cacheKey)) || $refreshCache) {
            $query = new Query();
            $query->from(['d' => self::tableName()]);
            $query->select(["id" => "d.$dataKey", "name" => "d.$dataValue"]);
            $query->where(['AND', ['d.remove' => 0], ['d.province_id' => $country]]);
            $provinces = $query->all(self::getDb());
            Yii::$app->cache->set($cacheKey, $provinces, 3600);
        }
        return $provinces;
    }
}
