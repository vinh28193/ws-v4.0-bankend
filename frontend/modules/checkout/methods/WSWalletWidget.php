<?php


namespace frontend\modules\checkout\methods;


class WSWalletWidget extends MethodWidget
{

    public function init()
    {

    }

    public function run()
    {
        parent::run();
        $this->render('ws_wallet');
    }
}