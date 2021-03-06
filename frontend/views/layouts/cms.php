<?php

use frontend\widgets\breadcrumb\BreadcrumbWidget;
use frontend\widgets\cms\SlideWidget;
use yii\helpers\Html;
use common\models\cms\WsPage;
use frontend\widgets\alias\AliasWidget;

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $page WsPage */
/* @var $isShow bool */

$js = <<<JS
$(document).ready(function() {
    var client = new ClientJS();
            var _fingerprint = client.getFingerprint();
            var data = {
                fingerprint: _fingerprint,
                path : window.location.pathname
            };
            // /cms/home/u
  setTimeout(function () { 
        ws.ajax('/frontend/u',{
        type: 'POST',
        dataType: 'json',
        data: data,
        loading: true,
        success: function (result) {
            //console.log(result);  console.log(result.success); 
        }
        });
    }, 1000 * 1);
});
JS;
//$this->registerJs($js, \yii\web\View::POS_END);

// Tracking Code
$jsgauges = <<<JS
  var _gauges = _gauges || [];
  (function() {
    var t   = document.createElement('script');
    t.type  = 'text/javascript';
    t.async = true;
    t.id    = 'gauges-tracker';
    t.setAttribute('data-site-id', '5d13801a71077e66207ba85e');
    t.setAttribute('data-track-path', 'https://track.gaug.es/track.gif');
    t.src = 'https://d2fuc4clr7gvcn.cloudfront.net/track.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(t, s);
  })(); 
JS;
$this->registerJs($jsgauges, \yii\web\View::POS_END);

$this->beginContent('@frontend/views/layouts/common.php')
?>

    <!--<div class="keep-navbar">
        <div class="container">
            <?php /*echo AliasWidget::widget(['type' => $page->type, 'isShow' => ($page->type === WsPage::TYPE_HOME && $isShow)]); */?>
            <?/*= \frontend\widgets\search\SearchBoxWidget::widget() */?>
        </div>
    </div>-->

<?php
//if ($isShow){
//echo Html::beginTag('div', ['class' => 'slider-2']);
//    echo SlideWidget::widget([
//        'page' => $page,
//        'options' => [
//            'id' => 'home-slide'
//        ],
//        'owlCarouselOptions' => [
//            'slideSpeed' => 300,
//            'paginationSpeed' => 400,
//            'loop' => !0,
//            'items' => 1,
//            'itemsDesktop' => !1,
//            'itemsDesktopSmall' => !1,
//            'itemsTablet' => !1,
//            'itemsMobile' => !1,
//            'autoplay' => 1e3
//        ]
//    ]);
//echo Html::endTag('div');
//}
if($isShow){
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
}else{
    echo BreadcrumbWidget::widget(['params' => $this->params]);
}
echo '<div class="container">' .
    $content.
    '</div>';
$this->endContent();
?>
