<?php

namespace common\modelsMongo;

use Yii;
use yii\mongodb\ActiveRecord;


class PushNotifications extends ActiveRecord
{
    public static function collectionName()
    {
        return ['weshop_global_40','push_notifications'];
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
        /**
        {
            "_id": ObjectId,
            "token_fcm": String,
            "subscribed_on": Date,
            "user_id": Integer,
            "fingerprint": String,
            "details": [
                "browser" : String,
                "os" : String,
                "osVersion" : String,
                "device" : String,
                "deviceType" : String,
                "deviceVendor" : String,
                "cpu" : String
            ]
        }
         */

        return [
            '_id',
            'token_fcm',
            'subscribed_on',
            'order_code',

            'fingerprint',

            'user_id',
            'user_email',
            'user_name',
            'details',
            'order_list',
            'nv'

        ];
    }

    public function rules()
    {
        return [
            [[
                'created_at',
                'updated_at',
                'token_fcm',
                'subscribed_on',
                'order_code',

                'fingerprint',

                'user_id',
                'user_email',
                'user_name',
                'details',
                'order_list',
                'nv'

            ], 'safe'],
            [[ 'token_fcm','fingerprint','user_id','user_email', 'user_name','details','order_list','nv'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            '_id' => 'ID',

            //User : Who Nh??n subscribed_on Notification
            'user_id' => 'id nh??n vi??n ',
            'user_email' => 'Email nh??n vi??n chat ',
            'user_name' => 't??n nh??n vi??n chat',

            // time
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',

            // Infor Field Notification
            'token_fcm' => 'Token FCM nh??n Notification',
            'order_code' => 'M??n ????n h??ng nh???n Notification',
            'subscribed_on' =>' Ng??y User click v??o button nh???n Th???ng b??o  ',
            'fingerprint' => 'UUID devices : ??inh danh cua m???i thi???t b??? nh???n th??ng b??o , M???i m???t ng?????i d??ng c?? N thi???t b??? nh???n th??ng b??o',
            'details' => ' Th??ng tin V??? Thi???t b??? ',
            'order_list' => 'Danh s??ch ????n h??ng',
            'nv'=> 'm??i tr?????ng b???n Notification  WEB/ APP:'
            /*
                 "details": [
                    "browser" : String,
                    "os" : String,
                    "osVersion" : String,
                    "device" : String,
                    "deviceType" : String,
                    "deviceVendor" : String,
                    "cpu" : String
                ]
             */

            ];
    }

    // Optional sort/filter params: page,limit,order,search[name],search[email],search[id]... etc

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

        $query = PushNotifications::find()
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
