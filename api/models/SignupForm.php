<?php

namespace app\models;

use Yii;

class SignupForm extends \yii\base\Model
{
    public $email;
    public $display_name;
    public $password;
    private $_user;
    
    public function init()
    {
        parent::init();
    }
    
    public function rules(): array
    {
        return [
            [['email', 'display_name', 'password'], 'required'],
            [['password'], 'string', 'min' => getenv('PASSWORD_LENGTH_MIN'), 'max' => getenv('PASSWORD_LENGTH_MAX')],
            [['display_name'], 'string', 'min' => 2, 'max' => 80],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class],
        ];
    }
    
    public function getUser(): ?User
    {
        return $this->_user;
    }
    
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $user = new User([
            'email' => $this->email,
            'display_name' => $this->display_name,
            'job_role' => User::JOB_ROLE_USER,
            'status' => User::STATUS_ACTIVE,
        ]);
        $user->setPassword($this->password);
        if ($user->save()) {
            $auth = \Yii::$app->authManager;
            $role = $auth->getRole(User::ROLE_USER);
            $auth->assign($role, $user->id);

            $this->_user = $user;
            return $user;
        }
        return false;
    }
}
