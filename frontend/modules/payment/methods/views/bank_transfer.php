<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\Dropdown;
use yii\helpers\Json;
use yii\web\JsExpression;

/* @var yii\web\View $this */
/* @var integer $group */
/* @var frontend\modules\payment\Payment $payment */
/* @var array $methods */
/* @var boolean $selected */

$css = <<< CSS
.select-method {
    height: 33px;
    background-color: rgb(242, 243, 245);
    color: rgb(85, 85, 85);
    width: 250px;
    font-size: 12px;
    text-align: left;
    position: relative;
    border-radius: 3px;
    border-width: 1px;
    border-style: solid;
    border-color: rgb(235, 235, 235);
    border-image: initial;
}
CSS;
$this->registerCss($css);
?>

<div class="method-item">
    <a class="btn method-select" data-toggle="collapse" data-target="#method<?= $group; ?>"
       aria-expanded="<?= $selected ? 'true' : 'false'; ?>"
       onclick="ws.payment.selectMethod(<?= $methods[0]['payment_provider_id'] ?>,<?= $methods[0]['payment_method_id'] ?>, '<?= $methods[0]['paymentMethod']['code']; ?>')">
        <i class="icon method_<?= $group; ?>"></i>
        <div class="name">Thẻ ATM nội địa/ Internet banking</div>
        <div class="desc"><span class="text-danger">Miễn phí</span>. Số thẻ của bạn được giữ an toàn 100% và chỉ được sử dụng cho giao dịch này.</div>
    </a>

    <div id="method<?= $group; ?>" class="<?= $selected ? 'collapse show' : 'collapse' ?>" aria-labelledby="headingOne"
         data-parent="#payment-method">
        <div class="method-content">
            <div class="form-group pick-method">
                <label>Chọn phương thức thanh toán</label>
                <select class="form-control select-method" onchange="ws.payment.methodChange(true)" id="bankOptions">
                    <?php foreach ($methods as $ky => $method): ?>
                        <option value="<?= $method['paymentMethod']['id'] ?>" <?= $method['paymentMethod']['id'] == $payment->payment_method ? "selected" : "" ?>><?= $method['paymentMethod']['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <ul class="method-list" id="atm_content">
            </ul>
        </div>
    </div>
</div>
