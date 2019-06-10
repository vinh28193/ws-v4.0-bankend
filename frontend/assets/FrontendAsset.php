<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class FrontendAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style.css',
        'css/variables.css',
        'css/all.css',
        'css/styke-new.css',
        'css/all.css',
        'fonts/line-awesome/css/line-awesome.min.css',
        'fonts/line-awesome/css/line-awesome-font-awesome.min.css',
//        'css/mobile_style.css'
    ];
    public $js = [
        'js/style.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        'common\assets\JQueryEzPlus',
        //'common\assets\FontawesomeAsset',
        'common\assets\OwlCarousel',
        'common\assets\SlickCarouselAsset',
        'frontend\assets\WeshopAsset',
        'frontend\assets\JQueryLazy',
        //'frontend\assets\FancyboxPlusAsset'
    ];
}
