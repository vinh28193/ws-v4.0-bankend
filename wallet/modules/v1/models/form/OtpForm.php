<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2018-06-16
 * Time: 11:55
 */

namespace wallet\modules\v1\models\form;

use wallet\modules\v1\models\WalletTransaction;
use Yii;
use yii\base\Model;

class OtpForm extends Model
{

    public $otpCode;

    private $_transaction;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function verify()
    {

    }

    public function refresh()
    {

    }

    public function getTransaction()
    {
        if(is_string($this->_transaction) && strpos($this->_transaction,WalletTransaction::TRANSACTION_CODE_PREFIX) === true){
            $query = WalletTransaction::find();
            $query->where(['wallet_transaction_code' => $this->_transaction]);
            return $query->one();
        } elseif (is_numeric($this->_transaction)){
            return WalletTransaction::findOne($this->_transaction);
        }elseif ($this->_transaction instanceof WalletTransaction){
            return $this->_transaction;
        }
        return null;
    }
}