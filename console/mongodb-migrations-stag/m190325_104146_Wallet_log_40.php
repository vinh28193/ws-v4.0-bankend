<?php

class m190325_104146_Wallet_log_40 extends \yii\mongodb\Migration
{
    public function up()
    {
        $this->createCollection(['Weshoplog_v40_stag','Wallet_log_40']);
    }

    public function down()
    {
        $this->dropCollection(['Weshoplog_v40_stag','Wallet_log_40']);
    }
}
