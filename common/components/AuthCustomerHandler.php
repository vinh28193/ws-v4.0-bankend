<?php
namespace common\components;

use common\models\Auth;
use common\models\User;
use common\models\Customer;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthCustomerHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        @date_default_timezone_set('Asia/Ho_Chi_Minh');

        $attributes = $this->client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'email');
        $id = ArrayHelper::getValue($attributes, 'id');

        $nickname = '';
        if(ArrayHelper::index($attributes, 'login')){
            $nickname = ArrayHelper::getValue($attributes, 'login');
        }else if(ArrayHelper::index($attributes, 'name')){
            $nickname = ArrayHelper::getValue($attributes, 'name');
        }
        if($nickname == ''){
            $nickname = ArrayHelper::getValue($attributes, 'login');
        }

        $email_verified = ArrayHelper::getValue($attributes, 'email_verified');
        $picture = ArrayHelper::getValue($attributes, 'picture');

        $duration = isset(Yii::$app->params['user.rememberMeDuration']) ? isset(Yii::$app->params['user.rememberMeDuration']) : 3600;

        /* @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

      if (Yii::$app->user->isGuest) {

         if ($auth) {
                // login
                /* @var User $user */
                $user = $auth->user;
                // $this->updateUserInfo($user);  // Error Argument 1 passed to common\components\AuthCustomerHandler::updateUserInfo() must be an instance of common\models\User, instance of common\models\db\User given
                Yii::$app->user->login($user, $duration);
         } else {
           // signup
            if ($email !== null && User::find()->where(['email' => $email])->exists()) {
                Yii::$app->getSession()->setFlash('error', [
                    Yii::t('app', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", ['client' => $this->client->getTitle()]),
                ]);
            } else {
                $password = Yii::$app->security->generateRandomString(6);
                $user = new User([
                    'username' => $nickname ? $nickname : $email,
                    'email' => $email,
                    'password' => $password,
                    'updated_at'=> time(),
                    'store_id' => 1, // Domain Weshop Viet Nam
                    'active_shipping' => 0,
                    'total_xu' => 0,
                    'usable_xu' => 0 ,
                    'last_revenue_xu' => 0 ,
                    'email_verified' => $email_verified ? $email_verified : 0 ,  // N???u l?? Google Email l???y ???????c r???i n??n coi nh?? l?? d?? x??c nh???n
                    'phone_verified' => 0, // V?? l?? ????ng k?? qua Google + Facebook n??n ch??a x??c nh??n ho???c l???y ???????c s??? ??t c???a kh??ch h??ng
                    'gender' => 0 ,
                    'type_customer' => 1 , // set 1 l?? Kh??ch L??? v?? 2 l?? Kh??ch bu??n - WholeSale Customer
                    'avatar' => $picture ? $picture : null ,
                    'note_by_employee' => 'Kh??ch h??ng t???o t??i kho???n qua '. $this->client->getId() .' .Attributes ' . serialize($attributes),
                    'employee' => 0 , //  1 L?? Nh??n vi??n , 0 l?? kh??ch h??ng
                    'vip' => 0 , //  M???c ????? Vip C???a Kh??ch H??ng kh??ng ap d???ng cho nh??n vi??n , theo thang ??i???m 0-5 s???
                ]);

                $user->generateAuthKey();
                $user->generatePasswordResetToken();

                $transaction = User::getDb()->beginTransaction();

                if ($user->save()) {
                    $auth = new Auth([
                        'user_id' => $user->id,
                        'source' => $this->client->getId(),
                        'source_id' => (string)$id,
                    ]);

                    if ($auth->save()) {
                        $transaction->commit();
                        Yii::$app->user->login($user, $duration);
                    } else {
                        Yii::$app->getSession()->setFlash('error', [
                            Yii::t('app', 'Unable to save {client} account: {errors}', [
                                'client' => $this->client->getTitle(),
                                'errors' => json_encode($auth->getErrors()),
                            ]),
                        ]);
                    }

                } else {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', 'Unable to save User : {errors}'),
                    ]);
                }
            }

         }
      } else {
          // user already logged in
          if (!$auth) { // add auth provider
              $auth = new Auth([
                  'user_id' => Yii::$app->user->id,
                  'source' => $this->client->getId(),
                  'source_id' => (string)$attributes['id'],
              ]);
              if ($auth->save()) {
                  /** @var User $user */
                  $user = $auth->user;
                  //$this->updateUserInfo($user);
                  Yii::$app->getSession()->setFlash('success', [
                      Yii::t('app', 'Linked {client} account.', [
                          'client' => $this->client->getTitle()
                      ]),
                  ]);
              } else {
                  Yii::$app->getSession()->setFlash('error', [
                      Yii::t('app', 'Unable to link {client} account: {errors}', [
                          'client' => $this->client->getTitle(),
                          'errors' => json_encode($auth->getErrors()),
                      ]),
                  ]);
              }
          } else { // there's existing auth
              Yii::$app->getSession()->setFlash('error', [
                  Yii::t('app',
                      'Unable to link {client} account. There is another user using it.',
                      ['client' => $this->client->getTitle()]),
              ]);
          }
      }
    }

    /**
     * @param User $user
     */
    /**
     * @param User $user
     */
    private function updateUserInfo(User $user)
    {
        $attributes = $this->client->getUserAttributes();
        $github = ArrayHelper::getValue($attributes, 'login');
        if ($user->github === null && $github) {
            $user->github = $github;
            $user->save();
        }

    }
}
