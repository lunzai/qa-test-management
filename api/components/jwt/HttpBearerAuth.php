<?php

namespace app\components\jwt;

use yii\di\Instance;
use yii\web\Response;

class HttpBearerAuth extends \yii\filters\auth\HttpBearerAuth
{
    public $jwt = 'jwt';
    public $identifier = 'jti';
    
    public function init(): void
    {
        parent::init();
        $this->jwt = Instance::ensure($this->jwt, Jwt::class);
    }
    
    public function authenticate($user, $request, $response)
    {
        $response->format = Response::FORMAT_JSON;
        $authHeader = $request->getHeaders()->get($this->header);
        if ($authHeader === null) {
            return null;
        }
        if ($this->pattern !== null) {
            if (preg_match($this->pattern, $authHeader, $matches)) {
                $authHeader = $matches[1];
            } else {
                return null;
            }
        }
        try {
            $jwtToken = $this->jwt->decodeToken($authHeader);
            if (!$jwtToken) {
                return null;
            }
            $identity = $user->loginByAccessToken($jwtToken->token, get_class($this));
            if ($identity === null || $identity->getId() != $jwtToken->id) {
                $this->challenge($response);
                $this->handleFailure($response);
            }
        } catch (\Exception $error) {
            $this->challenge($response);
            $this->handleFailure($response);
        }
        return $identity;
    }
}
