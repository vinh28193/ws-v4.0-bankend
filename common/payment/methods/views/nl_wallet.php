<?php

/* @var yii\web\View $this */
/* @var integer $group */
/* @var frontend\modules\checkout\Payment $payment */
/* @var array $methods */
?>

<div class="method-item">
    <a class="btn method-select" data-toggle="collapse" data-target="#method<?=$group?>" aria-expanded="false">
        <i class="icon method_<?= $group; ?>"></i>
        <div class="name">Ví điện tử Ngân Lượng</div>
        <div class="desc">Miễn phí giao dịch</div>
    </a>
    <div id="method<?= $group; ?>" class="collapse" aria-labelledby="headingOne" data-parent="#payment-method">
        <div class="method-content wallet">
            <div>Đăng ký ví NgânLượng.vn miễn phí <a href="">tại đây</a></div>
        </div>
    </div>
</div>