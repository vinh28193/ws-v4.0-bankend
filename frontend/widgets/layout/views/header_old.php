<?php

use yii\helpers\Html;

?>
<nav class="navbar navbar-default navbar-2">
    <div class="container">
        <div class="navbar-header">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">
                        <i class="flag us"></i>
                        <span>Từ Mỹ</span>
                    </a>
                    <div class="dropdown-menu animated fadeIn">
                        <div class="title2 active tab-ebay" data-toggle="tab-ebay">Mua hời nhất tại eBay</div>
                        <div class="content-tab" id="tab-ebay">
                            <ul>
                                <?= \frontend\widgets\alias\TopMenuAliasWidget::widget(['type' => 'NAV_TOP_E']) ?>
                            </ul>
                        </div>
                        <div class="title2" data-toggle="tab-amazon">Mua hời nhất tại Amazon</div>
                        <div class="content-tab" style="display: none;" id="tab-amazon">
                            <ul>
                                <?= \frontend\widgets\alias\TopMenuAliasWidget::widget(['type' => 'NAV_TOP_A']) ?>
                            </ul>
                        </div>
                        <div class="title2" data-toggle="tab-top-us">Top US store</div>
                        <div class="content-tab" style="display: none;" id="tab-top-us">
                            <ul>
                                <?= \frontend\widgets\alias\TopMenuAliasWidget::widget(['type' => 'NAV_TOP_US']) ?>
                            </ul>
                        </div>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">
                        <i class="flag jp"></i>
                        <span>Từ Nhật</span>
                    </a>
                    <div class="dropdown-menu animated fadeIn">
                        <div class="title2 active">Amazon Japan</div>
                        <ul>
                            <?= \frontend\widgets\alias\TopMenuAliasWidget::widget(['type' => 'NAV_TOP_JP']) ?>
                        </ul>
                        <div class="see-all">
                            <a href="#">Xem toàn bộ danh mục <i class="fa fa-long-arrow-right pull-right"></i></a>
                        </div>
                        <div class="title2">Top Japan store</div>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">
                        <i class="flag uk"></i>
                        <span>Từ Anh</span>
                    </a>
                    <div class="dropdown-menu animated fadeIn">
                        <a href="#">Gửi link báo giá từ Anh</a>
                    </div>
                </li>
            </ul>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown"><a href="#">Blog</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    Mua hộ
                </a>
                <div class="dropdown-menu animated fadeIn">
                    <div class="buyship">
                        <div class="left">
                            <ul class="timeline">
                                <li>
                                    <i class="buyship-ico ico1"></i>
                                    <div class="step">Bước 1</div>
                                    <p>Chọn hàng hoặc gửi link muốn mua</p>

                                <li>
                                    <i class="buyship-ico ico2"></i>
                                    <div class="step">Bước 2</div>
                                    <p>Thanh toán đơn hàng</p>

                                <li>
                                    <i class="buyship-ico ico3"></i>
                                    <div class="step">Bước 3</div>
                                    <p>Weshop hỗ trợ mua hàng, kiểm tra hàng</p>

                                <li>
                                    <i class="buyship-ico ico4"></i>
                                    <div class="step">Bước 4</div>
                                    <p>Weshop hỗ trợ chuyển hàng về tận tay bạn</p>
                                </li>
                            </ul>
                            <div class="text-center">
                                <a href="#" class="btn btn-primary">Đi đến gửi link báo giá sản phẩm</a>
                            </div>
                        </div>
                        <div class="right">
                            <div class="title">Dịch vụ mua hộ</div>
                            <div class="sub-title">Nhanh hơn - Rẻ hơn - An toàn hơn</div>
                            <ol>
                                <li><span>Vận chuyển rẻ, chỉ 14 ngày</span></li>
                                <li><span>Miễn thủ tục hải quan</span></li>
                                <li><span>Bảo hiểm rủi ro hàng hóa</span></li>
                                <li>
                                    <span>Mua bất cứ site nào và Không cần dùng tới tài khoản Paypal, thẻ tín dụng</span>
                                </li>
                            </ol>
                            <a href="#" class="btn btn-block">Xem các địa chỉ mua sắm</a>
                        </div>
                    </div>
                </div>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    Ship hộ
                </a>
                <div class="dropdown-menu animated fadeIn">
                    <div class="buyship">
                        <div class="left">
                            <ul class="timeline">
                                <li>
                                    <i class="buyship-ico ico5"></i>
                                    <div class="step">Bước 1</div>
                                    <p>Đăng ký tài khoản Weshop Có ngay địa chỉ riêng tại Mỹ!</p>

                                <li>
                                    <i class="buyship-ico ico1"></i>
                                    <div class="step">Bước 2</div>
                                    <p>Tự mua tại bất kỳ website nào và chuyển hàng tới địa chỉ Mỹ của bạn.</p>

                                <li>
                                    <i class="buyship-ico ico3"></i>
                                    <div class="step">Bước 3</div>
                                    <p>Weshop hỗ trợ nhận hàng, kiểm hàng và chuyển quốc tế</p>

                                <li>
                                    <i class="buyship-ico ico4"></i>
                                    <div class="step">Bước 4</div>
                                    <p>Weshop hỗ trợ chuyển hàng tới tận tay bạn và thu tiền dịch vụ</p>
                                </li>
                            </ul>
                        </div>
                        <div class="right">
                            <div class="title">Dịch vụ ship hộ</div>
                            <div class="sub-title">Rẻ hơn 30% - An toàn - Đảm bảo</div>
                            <ol>
                                <li><a href="#">Miễn phí gom hàng</a></li>
                                <li><span>Miễn phí gói lại hàng</span></li>
                                <li><span>Miễn phí kiểm tra hàng</span></li>
                                <li><span>Miễn phí lưu kho 14 ngày</span></li>
                                <li><span>Và nhiều dịch vụ khác</span></li>
                            </ol>
                            <button type="button" class="btn ws-btn btn-block" data-toggle="modal"
                                    data-target="#wood-alert">Yêu cầu ship hàng
                            </button>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <?php if (Yii::$app->user->isGuest) {
                    echo Html::a('<i class="nav-ico user"></i>', ['/secure/login']);
                } else { ?>
                    <a href="/account/home"><?= Yii::$app->user->identity->username ? Yii::$app->user->identity->username : Yii::$app->user->identity->email ?></a>
                <?php } ?>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <i class="nav-ico map"></i>
                </a>
                <div class="dropdown-menu animated fadeIn">
                    <div class="checking-order">
                        <div class="title">Kiểm tra hành trình đơn hàng</div>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Bin code">
                            <div class="input-group-append">
                                <button class="btn btn-search" type="button"><i class="la la-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <i class="nav-ico question"></i>
                </a>
                <div class="dropdown-menu animated fadeIn">
                    <ul>
                        <li>
                            <a href="#">
                                <i class="arrow-ico"></i>
                                <span>Thanh toán như thế nào</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="arrow-ico"></i>
                                <span>Chính sách hoàn trả</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="arrow-ico"></i>
                                <span>Các mặt hàng cấm</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="arrow-ico"></i>
                                <span>Thuế nhập khẩu</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="arrow-ico"></i>
                                <span>Chính sách bảo mật</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="arrow-ico"></i>
                                <span>Các điều khoản và điều kiện</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="arrow-ico"></i>
                                <span>Liên hệ</span>
                            </a>
                        </li>
                    </ul>
                    <div class="contact">
                        <div class="contact-icon"><img src="https://weshop.com.vn/images/support-icon.png" alt=""
                                                       title=""></div>
                        <div class="item">
                            <a href="mailto:support@weshop.com.vn" target="_top">support@weshop.com.vn</a>
                            <span>19006755</span>
                        </div>
                        <!--<div class="clearfix"></div>-->
                    </div>
                    <div class="support">
                        <a href="#">
                            Đến trung tâm hỗ trợ
                            <i class="fa fa-long-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </li>
            <li class="active">
                <a href="/my-cart.html">
                    <i class="nav-ico cart"></i>
                    <span class="badge" id="cartBadge"><?php
                        $cartManager = new \common\components\cart\CartManager();
                        echo Yii::$app->user->isGuest ? 0 : $cartManager->countItems('shopping');
                        ?></span>
                </a>
            </li>
        </ul>
    </div>
</nav>
<div class="navbar-ws">
    <div class="container">
        <div class="logo">
            <span class="menu-toggle"></span>
            <a href="/" class="logo-pc">
                <img src="/img/weshop-logo-vn.png" alt="" title=""/>
            </a>
        </div>
        <div class="produce-box">
            <ul>
                <li>
                    <span class="produce-ico icon1"></span>
                    <span class="text">Giá rẻ chỉ từ $8.5/Kg</span>
                </li>
                <li>
                    <span class="produce-ico icon2"></span>
                    <span class="text">Thủ tục trọn gói</span>
                </li>
                <li>
                    <span class="produce-ico icon3"></span>
                    <span class="text">Vận chuyển từ 14 ngày</span>
                </li>
                <li>
                    <span class="produce-ico icon4"></span>
                    <span class="text">Bảo hiểm tới $2.000</span>
                </li>
            </ul>
        </div>
    </div>
</div>