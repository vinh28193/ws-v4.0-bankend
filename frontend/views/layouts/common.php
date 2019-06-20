<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use frontend\assets\FrontendAsset;
use frontend\models\PasswordRequiredForm;

$passwordRequiredForm = new PasswordRequiredForm();
FrontendAsset::register($this);

$this->registerJs("ws.sendFingerprint();",\yii\web\View::POS_READY);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://sdk.accountkit.com/vi_VN/sdk.js"></script>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<script>
    // initialize Account Kit with CSRF protection
    AccountKit_OnInteractive = function(){
        AccountKit.init(
            {
                appId:"216590825760272",
                state:"<?= Yii::$app->request->getCsrfToken() ?>",
                version:"v1.1",
                fbAppEventsEnabled:true,
                redirect:"/secure/test-auth"
            }
        );
    };
</script>
<?php $this->beginBody() ?>

<div class="wrapper">
    <?= \frontend\widgets\layout\HeaderWidget::widget() ?>
    <?= $content; ?>
    <?= \frontend\widgets\layout\FooterWidget::widget() ?>
    <div class="modal otp-modal" id="otp-confirm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body" id="modalContent"></div>
            </div>
        </div>
    </div>

    <div class="modal password-required-modal" id="passwordRequired" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'passwordRequiredForm',
                        'action' => Url::toRoute('/secure/password-required', true)
                    ]);
                    ActiveForm::end();
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal qr-modal" id="qr-pay" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title"><img src="/img/payment_qrpay.png"/></div>
                </div>
                <div class="modal-body">
                    <div class="qr-box">
                        <img src="" alt="QR - Code" id="qrCodeImg">
<!--                    <p><a href="#">Download ảnh QR - Code!</a></p>-->
                </div>
            </div>
        </div>
    </div>
    <div class="modal success-modal" id="checkout-success" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <i class="la la-check"></i>
                    <div class="modal-title">Cám ơn bạn!</div>
                    <div class="order-code">Mã giao dịch: <span class="text-blue" id="transactionCode"></span></div>
                    <p>Đơn hàng của bạn đã được đặt hàng thành công!<br/>Hệ thống sẽ tự chuyển sang trang của nhà thành
                        toán
                    </p>
                    <button type="button" class="btn btn-submit btn-block" id="next-payment">Chuyển ngay <span
                                id="countdown_payment">5</span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endBody() ?>

<script>
    dataLayer = [];
</script>
    <script>
        // login callback
        function loginCallback(response) {
            console.log(response);
            if (response.status === "PARTIALLY_AUTHENTICATED") {
                ws.loading(true);
                location.assign('/secure/auth-account-kit?code='+response.code);
            }
            else if (response.status === "NOT_AUTHENTICATED") {
            }
            else if (response.status === "BAD_PARAMS") {
            }
        }

        // phone form submission handler
        function smsLogin() {
            AccountKit.login(
                'PHONE',
                {countryCode: '+84'}, // will use default values if not specified
                loginCallback
            );
        }
        // // email form submission handler
        // function emailLogin() {
        //     var emailAddress = document.getElementById("email").value;
        //     AccountKit.login(
        //         'EMAIL',
        //         {emailAddress: emailAddress},
        //         loginCallback
        //     );
        // }
    </script>
</body>
</html>
<?php $this->endPage() ?>
