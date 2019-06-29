<footer class="footer">
    <div class="top">
        <div class="container">
            <div class="row">
                <div class="col-md-6 item-box">
                    <div class="title"><?= Yii::t('frontend','Accept payment:') ?></div>
                    <ul>
                        <li><a href="#"><img src="/img/pay_master.png" alt="" title=""/></a></li>
                        <li><a href="#"><img src="/img/pay_visa.png" alt="" title=""/></a></li>
                        <li><a href="#"><img src="/img/pay_jcb.png" alt="" title=""/></a></li>
                    </ul>
                </div>
                <div class="col-md-6 item-box">
                    <div class="title"><?= Yii::t('frontend','Co-operate:') ?></div>
                    <ul>
                        <li><a href="#"><img src="/img/operate_ebay.png" alt="" title=""/></a></li>
                        <li><a href="#"><img src="/img/operate_amz.png" alt="" title=""/></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="info">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="title"><?= Yii::t('frontend','About Weshop:') ?></div>
                            <ul>
                                <li><a href="<?= Yii::t('frontend','#aboutus') ?>"><?= Yii::t('frontend','About us') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#privacypolicy') ?>"><?= Yii::t('frontend','Privacy Policy') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#term&condition') ?>"><?= Yii::t('frontend','Terms & Condition') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#contact') ?>"><?= Yii::t('frontend','Contact us') ?></a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <div class="title"><?= Yii::t('frontend','Help') ?></div>
                            <ul>
                                <li><a href="<?= Yii::t('frontend','#how2pay') ?>"><?= Yii::t('frontend','How to pay') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#termsrefund') ?>"><?= Yii::t('frontend','Terms Refund') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#prohibited') ?>"><?= Yii::t('frontend','Prohibited Items') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#importtaxduty') ?>"><?= Yii::t('frontend','Import Tax Duty') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#privacyPolicy') ?>"><?= Yii::t('frontend','Privacy Policy') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#term&condition') ?>"><?= Yii::t('frontend','Terms & Condition') ?></a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <div class="title"><?= Yii::t('frontend','Value Added Services') ?></div>
                            <ul>
                                <li><a href="<?= Yii::t('frontend','#inspectionservice') ?>"><?= Yii::t('frontend','Inspection Service') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#consolidateshipment') ?>"><?= Yii::t('frontend','Consolidate shipments') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#repackingservice') ?>"><?= Yii::t('frontend','Repacking service') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#shippinginsurance') ?>"><?= Yii::t('frontend','Shipping Insurance') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#feedaystorage') ?>"><?= Yii::t('frontend','Free 60days storage') ?></a></li>
                                <li><a href="<?= Yii::t('frontend','#allvas') ?>"><?= Yii::t('frontend','View all service VAS') ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="title-register"><?= Yii::t('frontend','Sign up to receive promotion news') ?></div>
                    <div class="form-group contact">
                        <div class="input-group">
                            <input class="form-control" type="text"
                                   placeholder="<?= Yii::t('frontend','Enter email to get hot deals') ?>">
                            <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i
                                                class="contact-ico"></i></button>
                                    </span>
                        </div>
                    </div>
                    <div class="sticker-bct"><a href="#" target="_blank"><img src="/img/chung_nhan_bct.png" alt=""
                                                                              title=""/></a></div>
                    <div class="connect">
                        <span><?= Yii::t('frontend','Connect with Weshop:') ?>:</span>
                        <a href="<?= Yii::t('frontend','#facebookWeshop') ?>" target="_blank"><img src="/img/social_fb.png" alt="" title=""/></a>
                        <a href="<?= Yii::t('frontend','#youtubeWeshop') ?>" target="_blank"><img src="/img/social_youtube.png" alt="" title=""/></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bot">
        <div class="container">
            <div class="title"><?= Yii::t('frontend','WESHOP VIET NAM - WORLD WIDE SHOPPING MADE EASY') ?></div>
            <ul>
                <li><?= Yii::t('frontend','<b>Ha Noi:</b> 3rd floor, VTC Online building No. 18 Tam Trinh Street, Minh Khai Ward, Hai Ba Trung District, Hanoi City, Vietnam') ?></li>
                <li><?= Yii::t('frontend','<b> Ho Chi Minh: </b> 6th floor, Sumikura building, 18H Cong Hoa, Ward 4, Tan Binh District') ?></li>
                <li><?= Yii::t('frontend','<b> Business number: </b> 0106693837 by the City Department of Planning and Investment. Hanoi first issued on November 18, 2014') ?></li>
            </ul>
        </div>
    </div>
</footer>
<div class="loading_new" id="loading" style="display:none;">
    <div class="loading-inner-new">
        <img src="/img/gif/loading.gif">
    </div>
</div>
<!-- Modal login waller -->
<div class="modal" id="loginWallet" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Đăng nhập ví</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <i class="icon password"></i>
                    <input type="password" name="passwordWallet" class="form-control" placeholder="Mật khẩu">
                    <label style="color: red" id="ErrorPasswordWallet"></label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="ws.loginWallet()">Đăng nhập</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="NotifyConfirm" tabindex="-1" role="dialog" aria-labelledby="NotifyConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" id="modal-content" role="document">
        <div class="modal-content">
            <div class="modal-header" id="NotifyConfirmHeader">
                <div class="modal-title" id="NotifyConfirmTitle"><?= Yii::t('frontend','Notify upgrade search')?></div>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div id="NotifyConfirmMessage">
                        <?= Yii::t('frontend','Notify') ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="display: none" class="btn btn-primary" id="NotifyConfirmBtnSubmit"><?= Yii::t('frontend','Confirm') ?></button>
                <button type="button" class="btn btn-secondary" id="NotifyConfirmBtnClose" data-dismiss="modal"><?= Yii::t('frontend','Close') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal-address" tabindex="-1" role="dialog" aria-labelledby="NotifyConfirmTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title"><?= Yii::t('frontend', 'Select Your Address') ?></div>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div>
                        <?= Yii::t('frontend', 'Please enter your address so we can display shipping fee correctly.') ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group formIconTitle">
                        <span class="input-group-addon">
                            <i class="la la-user"></i>
                        </span>
                        <input name="fullName_default" type="text" class="form-control" placeholder="<?= Yii::t('frontend','Full Name') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group formIconTitle">
                        <span class="input-group-addon">
                            <i class="la la-phone"></i>
                        </span>
                        <input name="phone_default" type="text" class="form-control" placeholder="<?= Yii::t('frontend','Phone') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group formIconTitle">
                        <span class="input-group-addon">
                            <i class="la la-map-marker"></i>
                        </span>
                        <input name="city_default" type="text" class="form-control" placeholder="<?= Yii::t('frontend','City') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group formIconTitle">
                        <span class="input-group-addon">
                            <i class="la la-map-marker"></i>
                        </span>
                        <input name="district_default" type="text" class="form-control" placeholder="<?= Yii::t('frontend','District') ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="display: none"
                        class="btn btn-primary"><?= Yii::t('frontend', 'Confirm') ?></button>
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?= Yii::t('frontend', 'Close') ?></button>
            </div>
        </div>
    </div>
</div>