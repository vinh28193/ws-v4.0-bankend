<?php

namespace frontend\modules\checkout\controllers;

use yii\web\Controller;

/**
 * Default controller for the `checkout` module
 */
class DefaultController extends CheckoutController
{
    public $step = 1;

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
