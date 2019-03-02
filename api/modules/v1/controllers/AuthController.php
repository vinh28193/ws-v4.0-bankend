<?php
/**
 * Created by PhpStorm.
 * User: galat
 * Date: 23/02/2019
 * Time: 09:14
 */

namespace api\modules\v1\controllers;


use api\behaviours\Apiauth;
use api\behaviours\Verbcheck;
use common\models\db\Actions;
use common\models\db\ActionScope;
use common\models\Scopes;
use common\models\db\ScopeUser;
use yii\db\ActiveQuery;
use yii\filters\AccessControl;
use Yii;


class AuthController extends RestController
{
    public $user ;
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [
                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => [],
                    'callback'=>[]
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['index'],
                    'rules' => [
                        [
                            'actions' => [],
                            'allow' => true,
                            'roles' => ['?'],
                        ],
                        [
                            'actions' => [
                                'index'
                            ],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => [],
                            'allow' => true,
                            'roles' => ['*'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'index' => ['GET', 'POST'],
                        'create' => ['POST'],
                        'update' => ['PUT'],
                        'view' => ['GET'],
                        'delete' => ['DELETE']
                    ],
                ],

            ];
    }

    public function beforeAction($action)
    {
        $before = parent::beforeAction($action);
        $this->user = \Yii::$app->user->getIdentity();
        if(Yii::$app->user->isGuest){
            return $this->response(false,"pls login!",null,0,401);
        }
        $user_id = Yii::$app->user->getIdentity()->getId();
        $key = isset($this->get['actionKey']) && $this->get['actionKey'] ? $action->controller->id."/".$action->actionMethod .'/'.$this->get['actionKey'] : $action->controller->id."/".$action->actionMethod;
        if(isset($this->get['scope']) && $this->get['scope'] == "clear"){
            Scopes::removeCacheScope($key,$user_id);
        }
        $list_scope = Yii::$app->cache->get($key);
        if(!$list_scope){
            $list_scope = self::getListScop($key,$action->actionMethod);
            Yii::$app->cache->set($key,$list_scope,60*60*24*7);
        }
        $list_scope_user = $this->isCustomer() ? Yii::$app->cache->get(Scopes::SCOPES_FOR_CUSTOMER) : Yii::$app->cache->get(Scopes::SCOPES_FOR_USER_ID.$user_id);
        if (!$list_scope_user) {
            if ($this->isCustomer()) {
                $scope_customer = Scopes::find()->where(['name' => 'customer', 'remove' => 0])->limit(1)->one();
                if (!$scope_customer) {
                    $scope_customer = new Scopes();
                    $scope_customer->name = 'customer';
                    $scope_customer->slug = 'customer';
                    $scope_customer->description = 'customer';
                    $scope_customer->level = 0;
                    $scope_customer->remove = 0;
                    $scope_customer->created_at = time();
                    $scope_customer->updated_at = time();
                    $scope_customer->save(0);
                }
                $list_scope_user = [$scope_customer->id];
                Yii::$app->cache->set(Scopes::SCOPES_FOR_CUSTOMER, $list_scope_user, 60 * 60);
            } else {
                /** @var ScopeUser[] $scopes */
                $scopes = ScopeUser::find()->where(['user_id' => $user_id, 'remove' => 0])->all();
                foreach ($scopes as $val) {
                    $list_scope_user[] = $val->scope_id;
                }
                Yii::$app->cache->set(Scopes::SCOPES_FOR_USER_ID . $user_id, $list_scope_user, 60 * 60);
            }
        }
        $check = false;
        foreach ($list_scope_user as $value){
            if(in_array($value,$list_scope)){
                $check = true;
                break;
            }
        }
        if(!$check){
         return $this->response(false,"You can not access! Pls contact with admin!",null,0,405);
        }
        return $before; // TODO: Change the autogenerated stub
    }

    public static function getListScop($key,$actionMethod){
        $list_scope = [];
        /** @var Actions $actionUse */
        $actionUse = Actions::find()
            ->with([
                'actionScopes' => function ($q) {
                    /** @var ActiveQuery $q */
                    $q->where(['remove' => 0]);
                }
            ])
            ->where(['action' => $key,'remove' => 0])->limit(1)->one();
        if(!$actionUse){
            $actionUse = new Actions();
            $actionUse->name = $actionMethod;
            $actionUse->action = $key;
            $actionUse->remove = 0;
            $actionUse->created_at = time();
            $actionUse->updated_at = time();
            $actionUse->save(0);
            $scope = Scopes::find()->where(['name' => 'admin'])->limit(1)->one();
            if(!$scope){
                $scope= new Scopes();
                $scope->name = 'admin';
                $scope->slug = 'admin';
                $scope->description = 'admin';
                $scope->level = 0;
                $scope->remove = 0;
                $scope->created_at = time();
                $scope->updated_at = time();
                $scope->save(0);
            }
            $scope_action = new ActionScope();
            $scope_action->action_id = $actionUse->id;
            $scope_action->scope_id = $scope->id;
            $scope_action->updated_at = time();
            $scope_action->created_at = time();
            $scope_action->remove = 0;
            $scope_action->save(0);
            $list_scope = [$scope->id];
        }else{
            foreach ($actionUse->actionScopes as $actionScope){
                $list_scope[] = $actionScope->scope_id;
            }
        }
        return $list_scope;
    }
}