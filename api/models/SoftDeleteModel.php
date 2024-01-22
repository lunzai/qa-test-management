<?php

namespace app\models;

use Yii;
use app\components\ActiveRecord;

class SoftDeleteModel extends ActiveRecord
{
    const STATUS_DELETED = 'Deleted';

    public function delete()
    {
        if (!$this->beforeDelete()) {
            return false;
        }
        $this->status = self::STATUS_DELETED;
        $this->deleted_at = time();
        $this->deleted_by = Yii::$app->user->getId();
        $save = $this->save();
        $this->afterDelete();
        return $save;
    }
}
