<?php

namespace frontend\modules\favorites\controllers;

//use frontend\modules\favorites\Favorite;
use yii\base\ErrorException;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use frontend\controllers\FrontendController;
use frontend\modules\favorites\models\Favorite;
use common\modelsMongo\FavoritesMongoDB;


/**
 * Default controller for the `CommentModule` module
 */
class FavoriteController extends FrontendController
{
    public function actionIndex(){
        die("hello");
    }
    /**
     * @return array
     */
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class'   => VerbFilter::className(),
//                'actions' => [
//                    'delete'       => ['post'],
//                    'create'       => ['post'],
//                    'changeStatus' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * @param $obj_type
     * @param $obj_id
     *
     * @return \yii\web\Response
     * @throws ErrorException
     */
    public function actionCreate()
    {
        $post = \Yii::$app->request->post();
        if (isset($post['obj_type'])) {  $obj_type = \serialize($post['obj_type']); }
        if (isset($post['obj_id'])) {  $obj_id = $post['obj_id']; }

        $obj_type = "Wanonymous";
        $obj_id = "9999";

        if ($this->create_favorite($obj_type, $obj_id)) {
            \Yii::info("app  create favorite Success");
             return $this->goBack();
        } else {
            \Yii::info(" app Can't create favorite");
            throw new ErrorException(\Yii::t('app', "Can't create favorite"));
        }
    }

    /**
     * @param $obj_type
     * @param $obj_id
     *
     * @return \yii\web\Response
     * @throws ErrorException
     */
    public function create($obj_type,$obj_id)
    {
        $post = \Yii::$app->request->post();
        if (isset($post['obj_type'])) {  $obj_type = \serialize($post['obj_type']); }
        if (isset($post['obj_id'])) {  $obj_id = $post['obj_id']; }

        if ($this->create_favorite($obj_type, $obj_id)) {
            \Yii::info("app  create favorite Success");
           // return $this->goBack();
        } else {
            \Yii::info(" app Can't create favorite");
            throw new ErrorException(\Yii::t('app', "Can't create favorite"));
        }
    }

    /**
     * @param $obj_type
     * @param $obj_id
     *
     * @return \yii\web\Response
     * @throws ErrorException
     */
    public function actionDelete($obj_type, $obj_id)
    {
        if ($this->delete_favorite($obj_type, $obj_id)) {
            return $this->goBack();
        } else {
            throw new ErrorException(\Yii::t('app', "Can't delete favorite"));
        }
    }

    /**
     * @param $id
     *
     * @return Favorite|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Favorite::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(\Yii::t('app', 'The requested page does not exist.'));
        }
    }


    /**
     * @param $obj_type
     * @param $obj_id
     *
     * @return bool
     */
    function is_user_created_favorite($obj_type, $obj_id)
    {
        if (\Yii::$app->user->getId()) {
            return Favorite::find()
                ->where(['obj_type' => $obj_type])
                ->andWhere(['obj_id' => $obj_id])
                ->andWhere(['created_by' => \Yii::$app->user->getId()])
                ->exists();
        } else {
            /** ToDo @Phuc Save Cookies Web , APP ---> Mongodb **/
            return FavoritesMongoDB::find()
                ->where(['obj_type' => $obj_type])
                ->andWhere(['obj_id' => $obj_id])
                ->andWhere(['ip' => \Yii::$app->getRequest()->getUserIP()])
                ->exists();
        }
    }

    /**
     * @param $obj_type
     * @param $obj_id
     *
     * @return bool
     */
    function create_favorite($obj_type, $obj_id)
    {
         if (\Yii::$app->user->getId()) {
            // Login
             $getId =  \Yii::$app->user->getId() ? \Yii::$app->user->getId() : '9999';
             $favorite = new Favorite([
                'obj_type' => $obj_type,
                'obj_id' => $obj_id,
                'ip' => \Yii::$app->getRequest()->getUserIP(),
                'created_by' => $getId,
            ]);

            if ($this->is_user_created_favorite($obj_type, $obj_id)) {
                return true;
            } else {
                $favorite->created_by = \Yii::$app->user->getId();
                try {
                    $favorite->validate();
                    \Yii::info($favorite->getErrors());
                    $favorite->save();
                    return true;
                } catch (\Exception $exception) {
                    \Yii::info($exception);
                    return false;
                }
            }
        } else {
             // anonymous
             $uuid = \thamtech\uuid\helpers\UuidHelper::uuid();
             $favoriteMongodb = new FavoritesMongoDB([
                 'obj_type' => $obj_type,
                 'obj_id' => $obj_id,
                 'ip' => \Yii::$app->getRequest()->getUserIP(),
                 'created_by' => $uuid,
             ]);
             $validator = new \thamtech\uuid\validators\UuidValidator();
             if ($validator->validate($uuid, $error)) {
                 // valid
                 if ($this->is_user_created_favorite($obj_type, $obj_id)) {
                     return true;
                 } else {
                     try {
                         $favoriteMongodb->validate();
                         \Yii::info($favoriteMongodb->getErrors());
                         $favoriteMongodb->save();
                         return true;
                     } catch (\Exception $exception) {
                         \Yii::info($exception);
                         return false;
                     }
                 }
             } else {
                 // not valid
                 echo $error;
             }


        }
    }

    /**
     * @param $obj_type
     * @param $obj_id
     *
     * @return bool
     */
    function delete_favorite($obj_type, $obj_id)
    {
        if (\Yii::$app->user->getId()) {
            if ($this->is_user_created_favorite($obj_type, $obj_id)) {
                return Favorite::deleteAll([
                    'obj_type' => $obj_type,
                    'obj_id' => $obj_id,
                    'created_by' => \Yii::$app->user->getId(),
                ]);
            } else {
                return true;
            }
        } else {
            if ($this->is_user_created_favorite($obj_type, $obj_id)) {
                return Favorite::deleteAll([
                    'obj_type' => $obj_type,
                    'obj_id' => $obj_id,
                    'ip' => \Yii::$app->getRequest()->getUserIP(),
                ]);
            } else {
                return true;
            }
        }
    }

    /**
     * @param $obj_type
     * @param $obj_id
     *
     * @return int
     */
    function delete_all_favorites($obj_type, $obj_id)
    {
        return Favorite::deleteAll([
            'obj_type' => $obj_type,
            'obj_id' => $obj_id
        ]);
    }

    /**
     * @param $obj_type
     * @param $obj_id
     * @return int|string
     */
    function count_all_favorites($obj_type, $obj_id)
    {
        return Favorite::find()
            ->where(['obj_type' => $obj_type])
            ->andWhere(['obj_id' => $obj_id])
            ->count();
    }
}
