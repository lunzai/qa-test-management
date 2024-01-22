<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\models\SoftDeleteModel;

/**
 * This is the model class for table "timeline".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $start
 * @property string $end
 * @property string $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int|null $deleted_by
 *
 * @property User $user
 */
class Timeline extends SoftDeleteModel
{
    const ONE_YEAR = 31536000;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timeline';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = TimestampBehavior::class;
        $behaviors['blameable'] = BlameableBehavior::class;
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $now = time();
        return [
            [['user_id', 'title', 'start', 'end'], 'required'],
            [['user_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_by', 'deleted_at'], 'integer'],
            [['start', 'end'], 'safe'],
            [['title', 'status'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => array_values(self::getConstants('status'))],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['start', 'end'], 'date', 'format' => 'yyyy-MM-dd', 'min' => $now - self::ONE_YEAR, 'max' => $now + self::ONE_YEAR],
            ['end', function ($attribute, $params, $validator) {
                if (!$this->start || !$this->end) {
                    $this->addError($attribute, 'Invalid date range.');
                } else {
                    if (strtotime($this->start) >= strtotime($this->end)) {
                        $this->addError($attribute, 'Invalid date range.');
                    }
                }
            }]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'start' => 'Start',
            'end' => 'End',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'deleted_at' => 'Deleted By',
            'deleted_by' => 'Deleted By',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        unset(
            $fields['created_at'],
            $fields['created_by'],
            $fields['updated_at'],
            $fields['updated_by'],
            $fields['deleted_at'],
            $fields['deleted_by']
        );
        $fields['type'] = function ($model) {
            return $model->user->job_role;
        };
        return $fields;
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return TimelineQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TimelineQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        if (!$this->status) {
            $this->status = self::STATUS_ACTIVE;
        }
        return parent::beforeSave($insert);
    }
}

class TimelineQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['{{timeline}}.status' => Timeline::STATUS_ACTIVE]);
    }

    public function endBefore($time)
    {
        if (is_int($time)) {
            $time = date('Y-m-d', $time);
        }
        return $this->andWhere(['<=', '{{timeline}}.end', $time]);
    }

    public function startAfter($time)
    {
        if (is_int($time)) {
            $time = date('Y-m-d', $time);
        }
        return $this->andWhere(['>=', '{{timeline}}.start', $time]);
    }

    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }
}
