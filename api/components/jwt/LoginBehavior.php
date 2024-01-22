<?php

namespace app\components\jwt;

use yii\web\User;
use yii\base\Event;

class LoginBehavior extends \yii\base\Behavior
{
    public function events(): array
    {
        return [
            User::EVENT_AFTER_LOGIN => 'afterLogin',
        ];
    }
    
    public function afterLogin(Event $event)
    {
        $user = $this->owner->getIdentity();
        $user->generateOrRefreshJwtToken($user->jwt_token);
    }
}
