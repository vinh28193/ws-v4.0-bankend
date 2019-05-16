<?php

use yii\helpers\Html;
use common\models\cms\WsPage;
use frontend\widgets\alias\AliasWidget;
use frontend\widgets\cms\SlideWidget;

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $page WsPage */
/* @var $isShow bool */

$this->beginContent('@frontend/views/layouts/common.php')
?>

    <div class="keep-navbar">
        <div class="container">
            <?php echo AliasWidget::widget(['type' => $page->type, 'isShow' => ($page->type === WsPage::TYPE_HOME && $isShow)]); ?>
            <?= \frontend\widgets\search\SearchBoxWidget::widget() ?>
        </div>
    </div>

<?php
if ($isShow){
echo Html::beginTag('div', ['class' => 'slider-2']);
    echo SlideWidget::widget([
        'page' => $page,
        'options' => [
            'id' => 'home-slide'
        ],
        'owlCarouselOptions' => [
            'slideSpeed' => 300,
            'paginationSpeed' => 400,
            'loop' => !0,
            'items' => 1,
            'itemsDesktop' => !1,
            'itemsDesktopSmall' => !1,
            'itemsTablet' => !1,
            'itemsMobile' => !1,
            'autoplay' => 1e3
        ]
    ]);
echo Html::endTag('div');
}
echo '<div class="container">' .
    $content.
    '</div>';
$this->endContent();
?>