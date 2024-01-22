<?php

namespace app\components;

use Yii;
use app\models\User as UserModel;
use app\components\jwt\LoginBehavior;
use app\components\jwt\LogoutBehavior;

class User extends \yii\web\User
{
    public $identityClass = UserModel::class;
    public $enableAutoLogin = false;
    public $enableSession = true;
    public $loginUrl = null;
    
    public function isAdminRole()
    {
        return $this->can(UserModel::ROLE_ADMIN);
    }

    public function isUserRole()
    {
        return $this->can(UserModel::ROLE_USER);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['jwtLogin'] = LoginBehavior::class;
        $behaviors['jwtLogout'] = LogoutBehavior::class;
        return $behaviors;
    }
}
