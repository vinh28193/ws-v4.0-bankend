<?php


namespace frontend\widgets\cms;


class FiveImgWidget extends WeshopBlockWidget
{

    public function run()
    {
        parent::run(); // TODO: Change the autogenerated stub
        $images = isset($this->block['images']) ? $this->block['images'] : [];
        $grid = isset($this->block['grid']) ? $this->block['grid'] : [];
        $categories = isset($this->block['categories']) ? $this->block['categories'] : [];

//        if(YII_ENV =='dev'){
//            return $this->render("five_img", [
//            'block' => $this->block['block'],
//            'categories' => $categories,
//            'images' => $images,
//            'grid' => $grid,
//        ]);

    }
}
