<?php
/**
 * Created by PhpStorm.
 * User: HUYKAKA
 * Date: 4/25/2019
 * Time: 9:13 AM
 */

namespace api\modules\v1\controllers;


use api\controllers\BaseApiController;
use common\data\ActiveDataProvider;
use common\modelsMongo\ListChat;
use Yii;

class ListChatMongoController extends BaseApiController
{

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET']
        ];
    }

    /**
     * @inheritdoc
     */
    protected function rules()
    {
        return [
            [
                'allow' => true,
                'actions' => ['index'],
                'roles' => $this->getAllRoles(true)
            ],
        ];
    }

    public function actionIndex() {
        $get = Yii::$app->request->get();
        $chat = ListChat::find();
        if (isset($get['noteL'])) {
            $chat->where(['LIKE', 'note', $get['noteL']]);
        }
        if (isset($get['contentL'])) {
            $chat->where(['LIKE', 'content', $get['contentL']]);
        }
        if (isset($get['statusTT'])) {
            $chat->where(['LIKE', 'status', (int)$get['statusTT']]);
        }
        return $this->response(true, 'success',  $chat->asArray()->all());
    }
    public function actionCreate() {
        $model = ListChat::find()->orderBy('code DESC')->limit(1)->one();
        $post = Yii::$app->request->post();
        $now = Yii::$app->getFormatter()->asTimestamp('now');
        $chat = new ListChat();
        $chat->note = $post['noteC'];
        $chat->code = $model->code + 1;
        $chat->content = $post['contentC'];
        $chat->status = $post['statusC'];
        $chat->time_start = $now;
        if (!$chat->save()) {
            return $this->response(false, 'error', $chat->getErrors());
        }
        return $this->response(true, 'success', $chat);
    }
    public function actionDelete($id) {
        $chat = ListChat::findOne(['code' => (int)$id]);
        if (!$chat->delete()) {
            return $this->response(false, 'error', $chat->getErrors());
        }
        return $this->response(true, 'success', $chat);
    }

    public function  actionUpdate($id) {
        $now = Yii::$app->getFormatter()->asTimestamp('now');
        $post = Yii::$app->request->post();
        if ($id) {
            $chat = ListChat::findOne(['code' => (int)$id]);
            if (isset($post['noteU'])) {
                $chat->note = $post['noteU'];
            }
            if (isset($post['contentU'])) {
                $chat->content = $post['contentU'];
            }
            $chat->update_time = $now;
            if (isset($post['status'])) {
                $chat->status = $post['status'];
            }
            if (!$chat->save()) {
                return $this->response(false, 'error', $chat->getErrors());
            }
            return$this->response(true, 'success', $chat);
        }
        return $this->response(false, '$id not found');

    }

}