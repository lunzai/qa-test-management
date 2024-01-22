<?php

namespace app\components;

use Yii;
use yii\helpers\Inflector;
use app\models\User as UserModel;

class ActiveRecord extends \yii\db\ActiveRecord
{
    const CREATED_BY_ATTRIBUTE = 'created_by';
    const UPDATED_BY_ATTRIBUTE = 'updated_by';
    const CREATED_AT_ATTRIBUTE = 'created_at';
    const UPDATED_AT_ATTRIBUTE = 'updated_at';

    const STATUS_ACTIVE = 'Active';
    const STATUS_INACTIVE = 'Inactive';
    const STATUS_DELETED = 'Deleted';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public function fields()
    {
        $fields = parent::fields();
        return $fields;
    }
    
    public function isInactive()
    {
        return $this->status == self::STATUS_INACTIVE;
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function getCreator($attribute = self::CREATED_BY_ATTRIBUTE)
    {
        if ($this->hasAttribute($attribute)) {
            return $this->hasOne(UserModel::className(), ['id' => $attribute]);
        }
        return false;
    }
    
    public function getUpdater($attribute = self::UPDATED_BY_ATTRIBUTE)
    {
        if ($this->hasAttribute($attribute)) {
            return $this->hasOne(UserModel::className(), ['id' => $attribute]);
        }
        return false;
    }
    
    public static function getConstants($name)
    {
        $self = new \ReflectionClass(new static());
        $contants = $self->getConstants();
        $prefix = strtoupper($name) . '_';
        $prefixLength = strlen($prefix);
        $prefixOffset = $prefixLength - 1;
        $options = [];
        foreach ($contants as $key => $value) {
            if (substr($key, 0, $prefixLength) === $prefix) {
                $options[$value] = ucwords(strtolower(Inflector::humanize(substr($key, $prefixLength))));
            }
        }
        return $options ? : [];
    }
    
    public static function getConstant($name, $value)
    {
        if ($options = static::getConstants($name)) {
            if (isset($options[$value])) {
                return $options[$value];
            }
        }
        return false;
    }
    
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }
}
