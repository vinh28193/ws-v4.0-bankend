<?php
use yii\helpers\Html;

?>
  <div class="thumb-slider">
      <div class="detail-slider">
          <i class="la la-chevron-up slider-prev"></i>
          <i class="la la-chevron-down slider-next"></i>
          <div id="gal1 detail-slider" class="slick-slider">
            <?php foreach ($images as $index => $image)
             {
                $html_slider = '';
                $img = Html::img($image->thumb, [
                                'alt' => $alt
                            ]);
                $html_slider = html::tag('a',$img,[
                  'class'=>'elevatezoom-gallery',
                  'data-image'=>$image->thumb,
                  'data-zoom-image'=>$image->thumb,
                  'width' => '100'
                   ]);
              echo  html::tag('div',$img,['class'=> 'item']);
             }?>
          </div>
      </div>
      <div class="big-img">
               <?php    
               echo Html::img($images[0]->thumb, [
                                'alt' => $alt,
                                'id'  => 'detail-big-img',
                                'class'=> 'detail-big-img',
                                'data-zoom-image' => $images[0]->thumb,
                                'width' => '400'
                            ]);
                     
                ?> 
       

      </div>
  </div>
  <style type="text/css">
    .detail-big-img{
      max-width: 400px
    }
  </style>
