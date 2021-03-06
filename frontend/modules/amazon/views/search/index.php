<?php

use yii\helpers\Html;
use common\widgets\Pjax;
use frontend\widgets\search\SearchResultWidget;

/* @var $this yii\web\View */
/* @var $results array */
/* @var $form common\products\forms\ProductSearchForm */
$keyword = Yii::$app->request->get('keyword');
$this->params = ['Home' => '/','Amazon Search' => '/', $keyword => 'amazon/search/'.$keyword.'.html'];

echo SearchResultWidget::widget([
    'results' => $results,
    'form' => $form,
    'options' => [
        'class' => 'search-2-content',
        'id' => 'wsAmazonSearch'
    ],
]);

?>


