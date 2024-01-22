<?php

namespace app\components\jwt;

use Firebase\JWT\JWT as phpJwt;
use Firebase\JWT\Key;

class Jwt extends \yii\base\Component
{
    public $secret;
    public $algo;
    public $expiryDuration;
    
    public function encodeToken(string $token, int $id): string
    {
        return phpJwt::encode([ 'token' => $token, 'id' => $id ], $this->secret, $this->algo);
    }
    
    public function decodeToken(string $token)
    {
        try {
            $payload = phpJwt::decode($token, new Key($this->secret, $this->algo));
            return $payload;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
