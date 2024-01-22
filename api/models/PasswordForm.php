<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class PasswordForm extends Model
{
    public $password_current;
    public $password_new;
    public $user;

    public function init()
    {
        parent::init();
        if ($this->user instanceof User === false) {
            throw new ServerErrorHttpException('Password form expecting user model.');
        }
    }

    public function rules(): array
    {
        return [
            [['password_new', 'password_current'], 'required'],
            [['password_new', 'password_current'], 'string', 'min' => getenv('PASSWORD_LENGTH_MIN'), 'max' => getenv('PASSWORD_LENGTH_MAX')],
            ['password_current', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            if (!$this->user || !$this->user->validatePassword($this->$attribute)) {
                $this->addError($attribute, 'Incorrect current password.');
            }
        }
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $this->user->setPassword($this->password_new);
        return $this->user->save();
    }
}
