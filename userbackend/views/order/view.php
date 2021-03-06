<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->ordercode;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-detail">
    <ul class="od-header">
        <li><a href="#" class="icon icon1">Xem shop</a></li>
        <li><a href="#" class="icon icon2">Chat</a></li>
        <li><a href="#" class="icon icon3">Khiếu nại</a></li>
    </ul>
    <?php
        $status = array('NEW', 'STOCKIN_US', 'STOCKIN_LOCAL', 'STOCKOUT_LOCAL', 'AT_CUSTOMER');
        $count = 0;
        foreach ($status as $key => $sta) {
            unset($status[$key]);
            if ($sta == $model->current_status) {
                break;
            }
        }
    ?>
    <div class="od-content">
        <div class="title">Hành trình đơn hàng</div>
        <ul class="od-tracking">
            <li class="<?php if (!in_array('NEW', $status)) {?> active <?php }?>">
                <i class="icon icon1"></i>
                <span>Purchasing</span>
            </li>
            <li class="<?php if (!in_array('STOCKIN_US', $status)) {?> active <?php }?>">
                <i class="icon icon2"></i>
                <span>US Warehousing</span>
            </li>
            <li class="<?php if (!in_array('STOCKIN_LOCAL', $status)) {?> active <?php }?>">
                <i class="icon icon3"></i>
                <span>Local Warehousing</span>
            </li>
            <li class="<?php if (!in_array('STOCKOUT_LOCAL', $status)) {?> active <?php }?>">
                <i class="icon icon4"></i>
                <span>Shipment</span>
            </li>
            <li class="<?php if (!in_array('AT_CUSTOMER', $status)) {?> active <?php }?>">
                <i class="icon icon5"></i>
                <span>At customer</span>
            </li>
        </ul>
        <div class="row info-detail">
            <div class="col-md-4">
                <div class="title-2">Địa chỉ chuyển hàng</div>
                <ul>
                    <li>Họ và tên: <?= $model->receiver_name ?></li>
                    <li>Địa chỉ: <?= $model->receiver_address ?></li>
                    <li>Số điện thoại: <?= $model->receiver_phone ?></li>
                    <li>Email: <?= $model->receiver_email ?></li>
                </ul>
            </div>
            <div class="col-md-4">
                <div class="title-2">Hình thức thanh toán</div>
                <div><?= $model->payment_type ?></div>
            </div>
            <div class="col-md-4">
                <div class="title-2">Tổng giá trị hàng hóa</div>
                <b class="text-orange"><?= number_format($model->total_paid_amount_local, 2, ',', '.').' VNĐ'; ?></b>
            </div>
        </div>
        <div class="od-table">
            <div class="be-table">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Sản phẩm</th>
                        <th scope="col">Số lượng</th>
                        <th scope="col">Giá sản phẩm</th>
                        <th scope="col">Trang thái đơn hàng</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($model->products as $product) {
                    ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <div class="thumb">
                                    <img src="<?= $product->link_img ?>" alt=""/>
                                </div>
                                <div class="info"><?= $product->product_name ?></div>
                            </div>
                        </td>
                        <td>01</td>
                        <td><b class="total text-orange"><?= $product->total_price_amount_local ?></b></td>
                        <td><b><?= $product->condition ?></b></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="package form-inline">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="package" id="package1" checked>
                <label class="form-check-label" for="package1">Yêu cầu đóng thùng tại Mỹ</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="package" id="package2">
                <label class="form-check-label" for="package2">Yêu cầu đóng thùng tại Việt Nam</label>
            </div>
        </div>
        <div class="title-2">Thông tin kiện hàng</div>
        <div class="package-table">
            <div class="be-table">
                <table class="table text-center">
                    <thead>
                    <tr>
                        <th scope="col">Kiện hàng</th>
                        <th scope="col">Trạng thái kiện</th>
                        <th scope="col">Mã Boxme</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1 kiện</td>
                        <td>abc ...</td>
                        <td><a href="#" class="text-blue">Chi tiết >></a></td>
                    </tr>
                    <tr>
                        <td>1 kiện</td>
                        <td>abc ...</td>
                        <td><a href="#" class="text-blue">Chi tiết >></a></td>
                    </tr>
                    <tr>
                        <td>1 kiện</td>
                        <td>abc ...</td>
                        <td><a href="#" class="text-blue">Chi tiết >></a></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
