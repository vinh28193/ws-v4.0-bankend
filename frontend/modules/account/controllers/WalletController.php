<?php


namespace frontend\modules\account\controllers;


use frontend\modules\payment\models\OtpVerifyForm;
use frontend\modules\payment\Payment;
use frontend\modules\payment\providers\wallet\WalletService;
use yii\helpers\ArrayHelper;

class WalletController extends BaseAccountController
{

    public function beforeAction($action)
    {
        $before = parent::beforeAction($action); // TODO: Change the autogenerated stub
        if(WalletService::isGuest()){
            if($action->actionMethod && $action->actionMethod == 'actionIndex'){
                return $before;
            }
            return \Yii::$app->response->redirect('/my-weshop/wallet.html');
        }else{
            return $before;
        }
    }

    public function actionIndex(){
        $walletS = new WalletService();
        $wallet = ArrayHelper::getValue($walletS->detailWalletClient(),'data',[]);
        return $this->render('index',[
            'wallet' => $wallet,
        ]);
    }
    public function actionTopUp(){
        $payment = new Payment([
            'page' => Payment::PAGE_TOP_UP,
        ]);
        $payment->initDefaultMethod();
        return $this->render('top-up',[
            'payment' => $payment
        ]);
    }
    public function actionDetail($transaction_code){
        $walletS = new WalletService();
        $walletS->transaction_code = $transaction_code;
        $rs = $walletS->transactionDetail();
        $success = ArrayHelper::getValue($rs,'success',false);
        $tran = [];
        if($success){
            $tran = ArrayHelper::getValue($rs,'data',[]);
        }
        return $this->render('detail',[
            'transaction_code' => $transaction_code,
            'transactionDetail' => ArrayHelper::getValue($tran,'transactionInfo'),
        ]);
    }
    public function actionWithdraw($transaction_code = null){
        $walletS = new WalletService();
        $wallet = ArrayHelper::getValue($walletS->detailWalletClient(),'data',[]);
        if(!$transaction_code){
            return $this->render('withdraw',[
                'wallet' => $wallet
            ]);
        }else{
            $walletS->transaction_code = $transaction_code;
            $data = ArrayHelper::getValue($walletS->transactionDetail(),'data',[]);
            $transactionInfo = ArrayHelper::getValue($data,'transactionInfo',[]);
            if(!$transactionInfo){
                return \Yii::$app->response->redirect('/my-weshop/wallet/withdraw.html');
            }
            if((ArrayHelper::getValue($transactionInfo,'type')) != WalletService::TYPE_WITH_DRAW || (ArrayHelper::getValue($transactionInfo,'status')) !== 0){
                return \Yii::$app->response->redirect('/my-weshop/wallet/transaction/'.$transaction_code.'/detail.html');
            }
            if(!(ArrayHelper::getValue($transactionInfo,'verified_at'))){
                $modal = new OtpVerifyForm();
                $modal->transactionCode = $transaction_code;
                $modal->orderCode = $transaction_code;
                return $this->render('verify_otp',[
                    'wallet' => $wallet,
                    'transaction_info' => $transactionInfo,
                    'transaction_code' => $transaction_code,
                    'modal' => $modal,
                ]);
            }else{
                return $this->render('withdraw_success',[
                    'wallet' => $wallet,
                    'transaction_info' => $transactionInfo,
                    'transaction_code' => $transaction_code,
                ]);
            }
        }
    }
    public function actionHistory(){
        $get = \Yii::$app->request->get();
        $page = \Yii::$app->request->get('page',1);
        $limit = \Yii::$app->request->get('limit',20);
        $walletS = new WalletService();
        $wallet = ArrayHelper::getValue($walletS->detailWalletClient(),'data',[]);
        $offset = $page * $limit - $limit;
        $rs = $walletS->listTransaction($get,$limit,$offset);
        $listTransaction = ArrayHelper::getValue($rs,'data',[]);
        $total = ArrayHelper::getValue($rs,'total',[]);

        return $this->render('history',[
            'wallet' => $wallet,
            'trans' => $listTransaction,
            'total' => $total,
        ]);
    }
}