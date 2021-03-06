<?php

namespace common\modelsMongo;

use Yii;
use yii\mongodb\ActiveRecord;


class BoxmeLogInspectionWs extends ActiveRecord
{
    public static function collectionName()
    {
        return ['Weshop_log_40','Boxme_log_40_Inspection'];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $reflection = new \ReflectionClass($this);
        if($reflection->getShortName() === 'ActiveRecord'){
            return $behaviors;
        }

        $timestamp = [];
        if ($this->hasAttribute('created_at')) {
            $timestamp[self::EVENT_BEFORE_INSERT][] = 'created_at';
        }
        if ($this->hasAttribute('updated_at')) {
            $timestamp[self::EVENT_BEFORE_UPDATE][] = 'updated_at';
        }

        $behaviors = !empty($timestamp) ? array_merge($behaviors, [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => $timestamp,
            ],
        ]) : $behaviors;

        return $behaviors;
    }

    public function attributes()
    {
        return [
            '_id',
            'created_at',
            'updated_at',
            'date',

            'user_id',
            'user_email',
            'user_name',
            'user_avatar',

            'user_app',
            'user_request_suorce',
            'request_ip',

            'Role','user_id','data_input','data_output', 'action_path','status' ,'LogTypeInspection','id'
        ];
    }

    public function rules()
    {
        return [
            [[
                'created_at',
                'updated_at',
                'date',

                'user_id',
                'user_email',
                'user_name',
                'user_avatar',

                'user_app',
                'user_request_suorce',
                'request_ip',


            ], 'safe'],
            [[ 'Role','user_id','data_input','data_output', 'action_path','status' ,'LogTypeInspection','id'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            '_id' => 'ID',

            //User : Who ai t??c
            'user_id' => 'id nh??n vi??n ',
            'user_email' => 'Email nh??n vi??n chat ',
            'user_name' => 't??n nh??n vi??n chat',
            'user_avatar' => 'H??nh ?????i di???n c???a User',
            'Role' => 'Role c???a nh??n vi??n ??ang thao t??c v??o action',

            //Action thao t??c l?? g?? ?
            'action_path' => 'T??n c???a action / n??t b???m thao t??c l?? g?? ?',
            'LogTypeInspection'=> 'Order | Product | PACKEAGE | PACKEGEITEM', // LogType : Order | Product | PACKEAGE | PACKEGEITEM : and Id ????? join
            'id' => 'Id ????? join v???i LogTypeInspection ',

            'status' => 'Tr???ng th??i nh??n ki???m : DONE , .....',

            // data
            'data_input' => 'd??? li???u ban ?????u tr?????c khi ghi log',
            'data_output' => 'd??? li???u sau khi x??? l??',

            // time
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'date' => 'Date create data',

            // ENV n??o b???n l??n
            'user_app' => 'T??n Application Id ',
            'user_request_suorce' => 'suorce g???i app ch??t Ph??n bi???t : APP/FRONTEND/BACK_END ',
            'request_ip' => 'IP request send message',

        ];
    }

   static public function search($params)
    {
        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');
        $search = Yii::$app->getRequest()->getQueryParam('search');
        if (isset($search)) {
            $params = $search;
        }

        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;

        $offset = ($page - 1) * $limit;

        $query = BoxmeLogInspectionWs::find()
            ->limit($limit)
            ->offset($offset);

        if (isset($order)) {
            $query->orderBy($order);
        }


        if (isset($order)) {
            $query->orderBy($order);
        }

        $additional_info = [
            'currentPage' => $page,
            'pageCount' => $page,
            'perPage' => $limit,
            'totalCount' => (int)$query->count()
        ];

        $data = new \stdClass();
        $data->_items = $query->all();
        $data->_links = '';
        $data->_meta = $additional_info;
        return $data;

    }
}
