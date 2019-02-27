<?php
/**
 * Created by PhpStorm.
 * User: galat
 * Date: 23/02/2019
 * Time: 11:10
 */

namespace api\modules\v1\userbackend\controllers;

use api\modules\v1\weshop\controllers\BaseAuthorController;
use common\models\Customer;
use common\models\db\Order;
use common\models\db\User;
use Yii;

class OrderController extends BaseAuthorController
{
    public  $page = 1;
    public  $limit = 20;
    /** @var Customer $user */
    public  $user;

    public function beforeAction($action)
    {
        $parent = parent::beforeAction($action);
        $this->user = Yii::$app->user->getIdentity();
        return $parent; // TODO: Change the autogenerated stub
    }

    public function actionIndex(){
        /****
         * Todo Log Activity All Action In RestController login or request
         */
        if(isset($this->post['action'])){

        }
        print_r($this->post);
        print_r(\Yii::$app->user->getIdentity());
        die;
    }

    public function actionGetListOrder(){
        $data = [];
        try{
            $data = $this->searchOrder($this->post['type_order'],$this->post['keyword'],$this->post['type_search'],$this->post['time_ranger'],$this->post['status']);
        }catch (\Exception $exception){
            $data = $this->searchOrder();
        }
        return $this->response(true,"Success",(array)$data);
    }

    /**
     * @param string $typeorder
     * @param string $keyword
     * @param string $typeSearch
     * @param array $timeRanger = [
     *                              'time_start' => 964475495,
     *                              'time_end' => 1217889313,
     *                          ]
     * @param string $status
     * @return array|Order
     */
    private function searchOrder($typeorder = '' ,$keyword = "",$typeSearch = "",$timeRanger = [],$status = ""){
        if(!$this->user){
            return [];
        }
        $query = Order::find()
            ->with([
                'products',
                'orderFees',
                'packageItems',
                'walletTransactions',
                'seller',
                'saleSupport' => function ($q) {
                /** @var ActiveQuery $q */
                    $q->select(['username','email','id','status', 'created_at', 'updated_at']);
                }
                ])
            ->where(['customer_id' => $this->user->id,'remove' => 0,]);
        if($typeSearch){
            $query->andWhere(['like',$typeSearch,$keyword]);
        }else{
            $query->andWhere(['or',
                ['like', 'id', $keyword],
                ['like', 'seller_name', $keyword],
                ['like', 'seller_store', $keyword],
                ['like', 'portal', $keyword],
            ]);
        }
        if($typeorder){
            $query->andWhere(['type_order' => $typeorder]);
        }
        if($status){
            $query->andWhere(['current_status' => $status]);
        }
        if ($timeRanger){
            $query->andWhere(['or',
                ['>=', 'created_at', $timeRanger['time_start']],
                ['<=', 'created_at', $timeRanger['time_end']]
            ]);
        }
        return $query->orderBy('created_at desc')->limit($this->limit)->offset($this->page* $this->limit - $this->limit)->asArray()->all();
    }
}