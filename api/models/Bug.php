<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bug".
 *
 * @property int $id
 * @property int $reporter_user_id
 * @property string $title
 * @property string|null $description
 * @property string|null $jira_number
 * @property string $fix_status
 * @property string $priority
 * @property string $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property User $reporter
 */
class Bug extends \app\components\ActiveRecord
{
    const FIX_STATUS_PENDING_INVESTIGATION = 'Pending Investigation';
    const FIX_STATUS_UNABLE_TO_REPLICATE = 'Unable to Replicate';
    const FIX_STATUS_NEED_MORE_INFORMATION = 'Need More Information';
    const FIX_STATUS_WORK_IN_PROGRESS = 'Work In Progress';
    const FIX_STATUS_TO_DO = 'To Do';
    const FIX_STATUS_FIX_VERIFIED = 'Fixed';
    const FIX_KIV = 'KIV';

    const PRIORITY_LOW = 'Low';
    const PRIORITY_MEDIUM = 'Medium';
    const PRIORITY_HIGH = 'High';
    const PRIORITY_BLOCKER = 'Blocker';

    const TYPE_BUG = 'Bug';
    const TYPE_FEATURE = 'Feature';
    const TYPE_OTHERS = 'Others';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reporter_user_id', 'title', 'fix_status', 'priority', 'status'], 'required'],
            [['reporter_user_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['description'], 'string'],
            [['title', 'fix_status', 'priority', 'status'], 'string', 'max' => 255],
            [['reporter_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['reporter_user_id' => 'id']],
            ['status', 'in', 'range' => array_values(self::getConstants('STATUS'))],
            ['fix_status', 'in', 'range' => array_values(self::getConstants('FIX_STATUS'))],
            ['priority', 'in', 'range' => array_values(self::getConstants('PRIORITY'))],
            ['jira_number', 'match', 'pattern' => '/[A-Z]+-[0-9]+/i'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'reporter_user_id' => Yii::t('app', 'Reporter User ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'jira_number' => Yii::t('app', 'JIRA Issue'),
            'fix_status' => Yii::t('app', 'Fix Status'),
            'priority' => Yii::t('app', 'Priority'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[ReporterUser]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getReporter()
    {
        return $this->hasOne(User::className(), ['id' => 'reporter_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return BugQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BugQuery(get_called_class());
    }
}

class BugQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Bug[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Bug|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
