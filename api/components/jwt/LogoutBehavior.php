<?php

namespace app\components\jwt;

use yii\web\User;
use yii\base\Event;

class LogoutBehavior extends \yii\base\Behavior
{
    public function events(): array
    {
        return [
            User::EVENT_BEFORE_LOGOUT => 'beforeLogout',
        ];
    }
    
    public function beforeLogout(Event $event)
    {
        $this->owner->getIdentity()->revokeJwtToken();
    }
}
