<?php


namespace api\modules\v1\controllers;


use api\controllers\BaseApiController;
use common\helpers\ExcelHelper;
use common\models\draft\DraftDataTracking;
use common\models\draft\DraftExtensionTrackingMap;
use common\models\Manifest;
use common\models\TrackingCode;
use Yii;
use yii\helpers\ArrayHelper;

class UsSendingController extends BaseApiController
{
    /**
     * @inheritdoc
     */
    protected function rules()
    {
        return [
            [
                'allow' => true,
                'actions' => ['index'],
                'roles' => ['operation', 'master_operation']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function verbs()
    {
        return [
            'index' => ['GET'],
            'create' => ['POST'],
            'update' => ['PUT'],
        ];
    }
    public function actionIndex(){
        $manifest_id = \Yii::$app->request->get('id');
        $limit = \Yii::$app->request->get('ps',20);
        $page = \Yii::$app->request->get('p',1);
        $limit_t = \Yii::$app->request->get('ps_t',20);
        $page_t = \Yii::$app->request->get('p_t',1);
        $limit_e = \Yii::$app->request->get('ps_e',20);
        $page_e = \Yii::$app->request->get('p_e',1);
        $tracking_t = \Yii::$app->request->get('t_t');
        $tracking_e = \Yii::$app->request->get('t_e');
        $manifest = Manifest::find()->with(['receiveWarehouse']);
        if($manifest_id){
            $manifest->andWhere(['manifest_id'=>$manifest_id]);
        }
        $tracking = DraftDataTracking::find()->with(['order','product']);
        if($tracking_t){
            $tracking->andWhere(['tracking_code'=>$tracking_t]);
        }
        $ext = DraftExtensionTrackingMap::find()->with(['order','product'])
            ->where(['or',['status' => DraftExtensionTrackingMap::STATUST_NEW],['draft_data_tracking_id' => null],['draft_data_tracking_id' => '']]);
        if($tracking_e){
            $ext->andWhere(['tracking_code'=>$tracking_e]);
        }


        $data['_manifest_total'] = $manifest->count();
        $data['_manifest'] = $manifest->limit($limit)->offset($limit*$page - $limit)->orderBy('id desc')->asArray()->all();
        $tracking->andWhere(['manifest_id' => $data['_manifest'][0]['id']]);
        $data['_tracking_total'] = $tracking->count();
        $data['_tracking'] = $tracking->limit($limit_t)->offset($limit_t*$page_t - $limit_t)->orderBy('id desc')->asArray()->all();
        $data['_ext_total'] = $ext->count();
        $data['_ext'] = $ext->limit($limit_e)->offset($limit_e*$page_e - $limit_e)->orderBy('id desc')->asArray()->all();
        return $this->response(true, "Success", $data);
    }
    public function actionCreate()
    {
        $start = microtime(true);
        $post = $this->post;
        $tokens = [];
        if (($store = ArrayHelper::getValue($post, 'store')) === null) {
            return $this->response(false, "create form undefined store !.");
        }
        if (($warehouse = ArrayHelper::getValue($post, 'warehouse')) === null) {
            return $this->response(false, "create form undefined warehouse !.");
        }
        if (($manifest = ArrayHelper::getValue($post, 'manifest')) === null) {
            return $this->response(false, "create form undefined manifest !.");
        }
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $manifest = Manifest::createSafe($manifest, 1, 1);

            foreach (ExcelHelper::readFromFile('file') as $name => $sheet) {
                $count = 0;
                $tokens[$name]['total'] = count($sheet);
                foreach ($sheet as $row) {
                    if (($trackingCode = ArrayHelper::getValue($row, 'TrackingCode')) === null) {
                        $tokens[$name]['error'][] = $row;
                        continue;
                    }
                    $model = new TrackingCode([
                        'tracking_code' => $trackingCode,
                        'store_id' => $manifest->store_id,
                        'manifest_code' => $manifest->manifest_code,
                        'manifest_id' => $manifest->id
                    ]);
                    if (!$model->save(false)) {
                        $tokens[$name]['error'][] = $row;
                    }
                    $count++;
                }
                $tokens[$name]['success'] = $count;
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e);
            return $this->response(false, $e->getMessage());
        }
        $time = microtime(true) - $start;
        $message = ["Sending `$manifest->manifest_code` success"];
        foreach ($tokens as $name => $token){
            $error = isset($token['error']) ? count($token['error']) : 0;
            $message[] = "from `$name` {$token['total']} executed $error error/{$token['success']} success";
        }
        $message = implode(", ",$message);

//        /** Log + Push chat**/
//        ChatHelper::push($message,$model->ordercode,'GROUP_WS', 'SYSTEM');
//        Yii::$app->wsLog->order->push('Us Sending', null, [
//            'id' => $model->order->ordercode,
//            'request' => $this->post,
//        ]);
        return $this->response(true, $message);
    }
    public function actionUpdate($id){
        $manifest = Manifest::findOne($id);
        if(!$manifest){
            return $this->response(false,"Cannot find manifest ".$id);
        }
        $tracking = DraftDataTracking::find()
            ->where(['manifest_id' => $manifest->id])
            ->select('count(id) as `countId`, tracking_code')
            ->groupBy('tracking_code')->asArray()->all();
        if(!$tracking){
            return $this->response(false,"Tracking is empty with manifest ".$manifest->manifest_code.'-'.$id);
        }
        $manifest->status = Manifest::STATUS_TYPE_GETTING;
        $manifest->save(0);
        DraftDataTracking::updateAll(
            ['type_tracking' => DraftDataTracking::TYPE_NORMAL],
            ['manifest_id' => $manifest->id]
        );
        foreach ($tracking as $dataTracking){
            if($dataTracking['countId'] > 1){
                DraftDataTracking::updateAll(
                    ['type_tracking' => DraftDataTracking::TYPE_SPLIT],
                    [
                        'manifest_id' => $manifest->id,
                        'tracking_code' => $dataTracking['tracking_code']
                    ]
                );
            }
        }
        DraftDataTracking::updateAll(
            ['type_tracking' => DraftDataTracking::TYPE_UNKNOWN],
            [   'and',
                ['manifest_id' => $manifest->id],
                ['or',['product_id' => null],['product_id' => '']],
                ['or',['order_id' => null],['order_id' => '']],
            ]
        );
        $manifest->status = Manifest::STATUS_TYPE_GET_DONE;
        $manifest->save(0);
        return $this->response(True,"Re Get Type manifest ".$manifest->manifest_code.'-'.$id. ' success!');
    }
}