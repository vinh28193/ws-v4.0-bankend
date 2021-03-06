<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-03-05
 * Time: 14:07
 */

return [
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['s' => 'secure'],
        'patterns' => [
            'GET me' => 'secure/me',
            'POST authorize' => 'secure/authorize',
            'POST access-token' => 'secure/access-token',
            'OPTIONS' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['check-out', 'cart'],
        'tokens' => [
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET' => 'index',
            'POST' => 'create',
            'PUT,PATCH {code}' => 'update',
            'OPTIONS' => 'options',
            'OPTIONS {code}' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['data'],
        'patterns' => [
            'POST' => 'create',
            'OPTIONS' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['product'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>'
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['list-chat-mongo'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'PUT,PATCH {code}' => 'update',
            'DELETE {id}' => 'delete',
            'DELETE {code}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS {code}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['list-notification'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'PUT,PATCH {code}' => 'update',
            'DELETE {id}' => 'delete',
            'DELETE {code}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS {code}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['order'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{image}' => '<image:\\d[\\d,]*>',
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'GET,HEAD export' => 'export',
            'PUT,PATCH {id}' => 'update',
            'PUT,PATCH {code}/confirm' => 'confirm',
            'DELETE {id}' => 'delete',
            'GET,HEAD {code}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS export' => 'options',
            'OPTIONS {code}/confirm' => 'options',
            'OPTIONS {code}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'PUT edit-image/<id:\d+>' => 'edit-image',
            'OPTIONS edit-image/<id:\d+>' => 'options',
            'PUT edit-variant/<id:\d+>' => 'edit-variant',
            'OPTIONS edit-variant/<id:\d+>' => 'options',
            'GET assign/<id:\d+>' => 'sale-assign',
            'OPTIONS assign/<id:\d+>' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['p' => 'package', 'u' => 'user', 's' => 'shipment', 'tracking-code', 'us-sending', 'manifest', 'tracking', 'warehouse-ws' => 'warehouse-management', 'dn' => 'delivery-note'],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'POST m' => 'merge',
            'GET r/<id:\d+>' => 'remove-item',
            'OPTIONS m' => 'options',
            'OPTIONS r/<id:\d+>' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['chat' => 'rest-api-chat', 'chatlists' => 'rest-api-chatlists'],
        'tokens' => [
            '{content}' => '<content:\\w[\\w,]*>',
            '{id}' => '<id:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'GET,HEAD {id}' => 'view',
            'DELETE {id}' => 'delete',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['ex' => 'exchange-rate'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{content}' => '<content:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'DELETE {content}' => 'delete',
            'POST' => 'create',
            'PUT,PATCH {id}' => 'update',
            'OPTIONS {content}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        //'if_service' => true,
        'controller' => ['chat-service' => 'service/rest-service-chat'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'PUT {id}' => 'customer-viewed',
            'PATCH {id}' => 'group-viewed',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['purchase'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'PUT update/<id:\d+>' => 'update',
            'OPTIONS update/<id:\d+>' => 'options',
            'POST create' => 'create',
            'OPTIONS create' => 'options',
            'DELETE delete/<id:\d+>' => 'delete',
            'OPTIONS delete/<id:\d+>' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['purchase-account' => 'service/purchase-service'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET' => 'list-account',
            'POST' => 'send-notify-changing,confirm-changing-price',
        ],
        'extraPatterns' => [
            'GET list-account' => 'list-account',
            'OPTIONS list-account' => 'options',
            'POST send-notify-changing' => 'send-notify-changing',
            'OPTIONS send-notify-changing' => 'options',
            'POST confirm-changing-price' => 'confirm-changing-price',
            'OPTIONS confirm-changing-price' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['card-payment' => 'service/rest-service-list-card-payment'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            // 'GET' => 'list-card-payment',
            'POST' => 'list-card-payment',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],

    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['system-location' => 'system-state-province'],
        'patterns' => [
            'GET,POST' => 'index',
            'OPTIONS' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['sale-support' => 'sale'],
        'patterns' => [
            'GET,POST' => 'index',
            'PUT,POST {id}' => 'assign',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['seller' => 'seller'],
        'patterns' => [
            'GET,POST' => 'index',
            'OPTIONS' => 'options',
        ],
    ],

    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['actionlog' => 'rest-action-log', 'packing-log' => 'rest-api-packing-log', 'ex-log' => 'exchange-rate-log'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['paymentlog' => 'rest-payment-log-w-s'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['walletlog' => 'rest-wallet-log-ws'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['boxme-inspect-log' => 'rest-bm-log-inspect-ws'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],

    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['send-shipment-return-log' => 'rest-bm-log-send-return-shipment-ws'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],

    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['send-shipment-log' => 'rest-boxme-log-send-shipment-ws'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],

    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['package-item', 'draft-package-item'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],

    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['fee' => 'product-fee'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'PUT update/<id:\d+>' => 'update',
            'OPTIONS update/<id:\d+>' => 'options',
            'GET view/<id:\d+>' => 'view',
            'OPTIONS view/<id:\d+>' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['gate' => 'product-gate'],
        'patterns' => [
            'GET,POST search' => 'search',
            'GET,POST get' => 'detail',
            'GET,POST calc' => 'calculator',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['coupon' => 'coupon'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['promotion' => 'promotion'],
        'patterns' => [
            'GET' => 'index',
            'POST' => 'check',
            'OPTIONS' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['policy' => 'category-customer-policy'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['link-image' => 'image-mongo'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['ext' => 'service/extension'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'POST' => 'update',
        ],
        'extraPatterns' => [
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['boxme' => 'service/manifest-box-me'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'GET {manifest_id}' => 'get-detail',
        ],
        'extraPatterns' => [
            'GET get-detail/<manifest_id:\d+>' => 'get-detail',
            'OPTIONS get-detail/<manifest_id:\d+>' => 'options',
        ]
    ],
    /*
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['account-purchase' => 'list-account-purchase'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    */
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'pluralize' => false,
        'controller' => ['warehouse' => 'service/warehouse-service'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET' => 'list',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'pluralize' => false,
        'controller' => ['ext-tracking' => 'extension-tracking'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'POST,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'POST create' => 'create',
            'OPTIONS create' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['notifications' => 'notifications', 'downloadexcel' => 'download-file-excel', 'trackinglogs' => 'rest-api-tracking-log'],
        'tokens' => [
            '{id}' => '<id:\\w[\\w,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'subscribe',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['s-tracking-code' => 'service/tracking-code-service'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'POST' => 'merge',
            'POST {id}' => 'map-unknown,seller-refund,mark-hold,insert-shipment',
            'DELETE {id}' => 'split-tracking',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'POST map-unknown/<id:\d+>' => 'map-unknown',
            'OPTIONS map-unknown/<id:\d+>' => 'options',
            'POST seller-refund/<id:\d+>' => 'seller-refund',
            'OPTIONS seller-refund/<id:\d+>' => 'options',
            'POST mark-hold/<id:\d+>' => 'mark-hold',
            'OPTIONS mark-hold/<id:\d+>' => 'options',
            'OPTIONS {id}' => 'options',
            'POST insert-shipment' => 'insert-shipment',
            'OPTIONS insert-shipment' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['cms'],
        'patterns' => [
            'GET,POST' => 'index',
            'OPTIONS' => 'options',

        ],
    ],

    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['courier' => 'service/courier'],
        'patterns' => [
            'POST create' => 'create',
            'POST bulk' => 'create-bulk',
            'POST suggest' => 'calculate',
            'POST cancel' => 'cancel',
            'OPTIONS' => 'options',
            'OPTIONS create' => 'options',
            'OPTIONS bulk' => 'options',
            'OPTIONS suggest' => 'options',
            'OPTIONS cancel' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['wh' => 'warehouse'],
        'patterns' => [
            'GET' => 'index',
            'OPTIONS' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['s-us-send' => 'service/service-us-sending'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'POST' => 'merge',
            'POST {id}' => 'map-unknown,seller-refund,mark-hold,insert-shipment',
            'PUT {id}' => 'get-type',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'POST map-unknown/<id:\d+>' => 'map-unknown',
            'OPTIONS map-unknown/<id:\d+>' => 'options',
            'POST seller-refund/<id:\d+>' => 'seller-refund',
            'OPTIONS seller-refund/<id:\d+>' => 'options',
            'POST insert-tracking' => 'insert-tracking',
            'OPTIONS insert-tracking' => 'options',
            'OPTIONS {id}' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['order-s' => 'service/order'],
        'patterns' => [
            'GET,HEAD' => 'index',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create,update-arrears,confirm-change-price,save-purchase-info',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'POST confirm-change-price' => 'confirm-change-price',
            'OPTIONS confirm-change-price' => 'options',
            'POST save-purchase-info' => 'save-purchase-info',
            'POST update-payment' => 'update-payment',
            'OPTIONS update-payment' => 'options',
            'OPTIONS save-purchase-info' => 'options',
            'POST update-arrears' => 'update-arrears',
            'OPTIONS update-arrears' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['dns' => 'service/delivery-note-service'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{token}' => '<token:\\d[\\d,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'POST' => 'merge',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'POST merge' => 'merge',
            'OPTIONS merge' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['fcm-noti'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'POST' => 'create',
        ],
        'extraPatterns' => []
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['log-tracking' => 'service/log-tracking'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'POST' => 'view-log',
            'OPTIONS' => 'options',

        ],
        'extraPatterns' => [
            'POST view-log' => 'view-log',
            'OPTIONS view-log' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['s-package' => 'service/package'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'POST' => 'merge',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'POST merge' => 'merge',
            'OPTIONS merge' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['i18n' => 'i18n'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'GET' => 'get-lang',
            'PUT,PATCH {id}' => 'update',
            'DELETE {id}' => 'delete',
            'GET,HEAD {id}' => 'view',
            'POST' => 'create',
            'OPTIONS {id}' => 'options',
            'OPTIONS' => 'options',
        ],
        'extraPatterns' => [
            'GET get-lang' => 'get-lang',
            'OPTIONS get-lang' => 'options',
        ]
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['additional'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET' => 'index',
            'GET,HEAD {code}' => 'view',
            'POST' => 'calculator',
            'OPTIONS {code}' => 'options',
            'OPTIONS' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['pay' => 'payment-transaction', 'pay-bank' => 'payment-bank'],
        'tokens' => [
            '{id}' => '<id:\\d[\\d,]*>',
            '{code}' => '<code:\\w[\\w,]*>',
        ],
        'patterns' => [
            'GET,HEAD' => 'index',
            'GET' => 'get-lang',
            'PUT,PATCH {code}' => 'update',
            'GET,HEAD {code}' => 'view',
            'POST' => 'create',
            'OPTIONS {code}' => 'options',
            'OPTIONS' => 'options',
        ],
    ],
    [
        'class' => \common\filters\ApiUrlRule::className(),
        'prefix' => 'v1',
        'controller' => ['order-save-file' => 'service/order-upload'],
        'patterns' => [
            'POST' => 'upload',
            'OPTIONS' => 'options',
        ],
    ]
];
