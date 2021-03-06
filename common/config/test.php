<?php
return [
    'id' => 'app-common-tests',
    'basePath' => dirname(__DIR__),
    'components' => [
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
        ],
        'storeManager' => [
            'class' => common\components\consoles\StoreManager::className(),
            'defaultDomain' => 'localhost:80'
        ]
    ],
];
