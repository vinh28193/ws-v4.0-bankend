<?php


namespace common\components;

use Yii;
use common\models\User;

/**
 * Class GetUserIdentityTrait
 * @package common\components
 * @property-read null|User $user
 */
trait GetUserIdentityTrait
{
    /**
     * @var null|User
     */
    private $_user = null;

    /**
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === null && ($user = Yii::$app->user->identity) !== null) {
            $this->_user = $user;
        }
        return $this->_user;
    }

    public function setUser($user)
    {
        if ($user instanceof User) {
            $this->_user = $user;
        } elseif (is_numeric($user)) {
            $this->_user = User::findOne($user);
        } else if (is_array($user)) {
            if (!isset($user['class'])) {
                $user['class'] = User::className();
            }
            $this->_user = Yii::createObject($user);
        } else {
            $this->_user = $user;
        }
    }

}