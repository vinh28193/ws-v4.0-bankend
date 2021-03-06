<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel userbackend\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="be-acc">
    <div class="ba-block1">
            <?php $form = ActiveForm::begin([
                    'options' => [
                            'class' => 'payment-form'
                    ]

            ]); ?>
            <div class="form-group">
                <?= $form->field($model, 'first_name',['template' => " <i class=\"icon user\"></i>{input}\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'last_name', ['template' => " <i class=\"icon user\"></i>{input}\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'phone', ['template' => " <i class=\"icon phone\"></i>{input}\n{hint}\n{error}"])->input('number') ?>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'email', ['template' => " <i class=\"icon email\"></i>{input}\n{hint}\n{error}"])->input('email') ?>
            </div>
            <?php
                $provider=\common\models\SystemStateProvince::find()->all();
                $provi=ArrayHelper::map($provider,'id','name');
            ?>
            <div class="form-group">
                <?= $form->field($address, 'province_id', ['template' => " <i class=\"icon globe\"></i>{input}\n{hint}\n{error}"])->dropDownList($provi, ['id' => 'province_id'], ['data' => [1 => $address->district_name],]); ?>
            </div>
            <?php
            $district=\common\models\SystemDistrict::find()->all();
            $distr=ArrayHelper::map($district,'id','name')
            ?>
            <div class="form-group">
                <?= $form->field($address, 'district_id', ['template' => " <i class=\"icon city\"></i>{input}\n{hint}\n{error}"])->widget(DepDrop::classname(), [
                    'data' => $distr,
                    'options'=>['id'=> 'district_id'],
                    'pluginOptions'=>[
                        'depends'=>['province_id'],
                        'placeholder'=>'Select...',
                        'url'=>Url::to(['customer/subcat'])
                    ]
                ]); ?>
            </div>
            <div class="form-group">
                <?= $form->field($address, 'address', ['template' => " <i class=\"icon mapmaker\"></i>{input}\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('C???p Nh???p', ['class' => 'btn btn-payment']) ?>
                <?php echo Html::a('Thay ?????i m???t kh???u', ['/site/change-password'], ['class' => 'text-blue float-right pt-4 mt-3']);?>
            </div>
            <?php ActiveForm::end(); ?>
            <div class="config-acc">
                <div class="title">T??y ch???n t??i kho???n</div>
                <ul>
                    <li>
                        <b>Qu???c t???ch:</b>
                        <span><?= $model->store->name ?></span>
                    </li>
                    <li>
                        <b>Ng??n ng???:</b>
                        <span>Ti???ng vi???t</span>
                    </li>
                    <li>
                        <b>Ti???n t???:</b>
                        <span>VN??</span>
                    </li>
                    <li>
                        <b>C???p th??nh vi??n:</b>
                        <span>Basic</span>
                    </li>
                </ul>
            </div>
    </div>
    <div class="us-address">
        <div class="title">?????a ch??? t???i M??? c???a b???n</div>
        <div class="code">M?? kh??ch h??ng c???a b???n : <b><?= $model->verify_code_expired_at ?></b></div>
    </div>
    <div class="ba-block2">
        <div class="title-box">
            <div class="title">?????a ch??? giao h??ng c???a b???n:</div>
            <a href="#" class="add-new">Th??m m???i</a>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="name-box">
                    <b>L?? NG???C LINH</b>
                    <a href="#"><i class="icon edit"></i></a>
                    <a href="#"><i class="icon del"></i></a>
                </div>
                <ul>
                    <li>18 Tam Trinh- Qu???n Hai B?? Tr??ng- Tp.H?? N???i- Vi???t Nam</li>
                    <li>Email: linhktt@peacesoft.net</li>
                    <li>S??? ??i???n tho???i: 0967985456</li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="name-box">
                    <b>L?? NG???C LINH</b>
                    <a href="#"><i class="icon edit"></i></a>
                    <a href="#"><i class="icon del"></i></a>
                </div>
                <ul>
                    <li>18 Tam Trinh- Qu???n Hai B?? Tr??ng- Tp.H?? N???i- Vi???t Nam</li>
                    <li>Email: linhktt@peacesoft.net</li>
                    <li>S??? ??i???n tho???i: 0967985456</li>
                </ul>
            </div>
        </div>
    </div>
</div>
