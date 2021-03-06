<?php


namespace frontend\widgets\search;

use Yii;
use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;
use yii\web\Request;

class SearchBoxWidget extends Widget
{

    public $redirectHref;

    public $placeholder;

    public $keywordParam = 'keyword';


    public function init()
    {
        parent::init();
        if ($this->redirectHref === null) {
            $this->redirectHref = Yii::$app->request->getUrl();
        }
        $this->placeholder = Yii::t('frontend', 'Enter keyword');
        Html::addCssClass($this->options, 'search-box');
        $this->registerClientScript();
    }

    public function run()
    {
        parent::run();
        echo $this->renderSearchBox();

    }

    protected function registerClientScript()
    {
        $view = $this->getView();
        $url = $this->redirectHref;
        $js = <<<JS
        
JS;
        $view->registerJs($js);
    }

    protected function renderSearchBox()
    {
//        $formOptions = ArrayHelper::remove($this->options, 'formGroupOptions', []);
//        Html::addCssClass($formOptions, 'form-group');
//        return Html::tag('div', $this->renderSearchInput($formOptions), $this->options);
        $keyWord = $this->getQueryParam($this->keywordParam, null);
        $keyWord = Html::encode($keyWord);
        return $this->render('searchBox', [
            'keyword' => $keyWord,
            'placeholder' => $this->placeholder
        ]);

    }

    protected function renderSearchInput($options)
    {
        $keyWord = $this->getQueryParam($this->keywordParam, null);
        $keyWord = Html::encode($keyWord);
        $input = Html::input('text', 'searchBox', $keyWord, ['class' => 'form-control', 'placeholder' => $this->placeholder]);
        $input .= Html::beginTag('span', ['class' => 'input-group-btn']);
        $input .= Html::button('<i class="la la-search">', ['class' => 'btn btn-default']);
        $input .= Html::endTag('span');
        return Html::tag('div', Html::tag('div', $input, ['class' => 'input-group']), $options);

    }

    protected function getQueryParam($name, $defaultValue = null)
    {
        $params = ($request = Yii::$app->getRequest()) instanceof Request ? $request->getQueryParams() : [];
        return isset($params[$name]) && is_scalar($params[$name]) ? $params[$name] : $defaultValue;
    }
}