<?php
/**
 * Created by PhpStorm.
 * User: galat
 * Date: 22/02/2019
 * Time: 15:32
 */

namespace common\fixtures;


use yii\test\ActiveFixture;

class CategoryFixture extends ActiveFixture
{
    public $modelClass = 'common\models\Category';
    public $dataFile = '@common/fixtures/data/data_fixed/category.php';
    public $depends = [
        'common\fixtures\CategoryGroupFixture',
    ];
}
