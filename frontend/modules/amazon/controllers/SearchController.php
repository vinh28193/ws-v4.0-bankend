<?php


namespace frontend\modules\amazon\controllers;


use common\products\forms\ProductSearchForm;
use Yii;
use yii\helpers\Html;

class SearchController extends AmazonController
{


    public function actionIndex()
    {
        $queryParams = $this->request->getQueryParams();
        $form = new ProductSearchForm();
        $form->load($queryParams);
        $form->type = 'amazon';
        $this->portalTitle = "Search Amazon :" . Html::decode($form->keyword);
        Yii::info($form->getAttributes(), __METHOD__);
        if (($results = $form->search()) === false || (isset($results['products']) && $results['products'] === 0)) {
            return $this->render('@frontend/views/common/no_search_results');
        }
        return $this->render('index', [
            'results' => $results,
            'form' => $form
        ]);
    }
}