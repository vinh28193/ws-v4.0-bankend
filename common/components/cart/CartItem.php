<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-02-27
 * Time: 08:41
 */

namespace common\components\cart;


class CartItem extends \yii\base\BaseObject
{

    public $sku;
    public $parentSku;
    public $source;
    public $seller;
    public $quantity = 1;
    public $images = [];

    public function process(){
        return new self([
            'sku' => $this->sku,
            'parentSku' => $this->parentSku,
            'source'=> $this->source,
            'seller' => $this->seller,
            'quantity' => $this->quantity,
            'images' => $this->images
        ]);
    }

}