<?php
use yii\helpers\Html;

/**
 * @var $item \common\products\BaseProduct
 */
?>

<div class="thumb-slider">
    <div class="detail-slider">
        <i class="la la-chevron-up slider-prev"></i>
        <div id="detail-slider"  class="slick-slider">
            <?php
                foreach ($item->primary_images as $image){ ?>
                    <div class="item">
                        <a href="javascript:void (0);" onclick="changeBigImage(this)" data-image="<?= $image->main ?>" data-zoom-image="<?= $image->main ?>">
                            <img src="<?= $image->main ?>" style="max-width: 100px"/>
                        </a>
                    </div>
                <?php }
            ?>
        </div>
        <i class="la la-chevron-down slider-next"></i>
    </div>
    <div class="big-img">
            <img id="detail-big-img" class="detail-big-img"
                 src="<?= $item->primary_images ? $item->primary_images[0]->main : '/img/no_image.png' ?>"
                 data-zoom-image="<?= $item->primary_images ? $item->primary_images[0]->main : '/img/no_image.png' ?>"/>
        <!--<div class="out-of-date">-->
        <!--<span><i class="fas fa-exclamation-triangle"></i><br/>Hàng hết hạn bán</span>-->
        <!--</div>-->
    </div>
</div>
