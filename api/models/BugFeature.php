<?php

namespace app\models;

use Yii;
use app\models\SoftDeleteModel;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "bug_feature".
 *
 * @property int $id
 * @property int $reporter_user_id
 * @property int|null $qa_user_id
 * @property int|null $developer_user_id
 * @property string $title
 * @property string|null $description
 * @property string|null $jira_number
 * @property string $fix_status
 * @property string $priority
 * @property string $status
 * @property string $type
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $deleted_at
 * @property int|null $deleted_by
 *
 * @property User $developer
 * @property User $qa
 * @property User $reporter
 * @property BugFeatureFollowUp[] $followUps
 */
class BugFeature extends SoftDeleteModel
{
    const FIX_STATUS_PENDING_INVESTIGATION = 'Pending Investigation';
    const FIX_STATUS_UNABLE_TO_REPLICATE = 'Unable to Replicate';
    const FIX_STATUS_NEED_MORE_INFORMATION = 'Need More Information';
    const FIX_STATUS_WORK_IN_PROGRESS = 'Work In Progress';
    const FIX_STATUS_TO_DO = 'To Do';
    const FIX_STATUS_FIXED = 'Fixed';
    const FIX_STATUS_KIV = 'KIV';

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
        return 'bug_feature';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = TimestampBehavior::class;
        $behaviors['blameable'] = BlameableBehavior::class;
        return $behaviors;
    }

    public function extraFields()
    {
        return ['qa', 'developer', 'reporter'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reporter_user_id', 'title', 'fix_status', 'priority', 'status', 'type'], 'required'],
            [['reporter_user_id', 'qa_user_id', 'developer_user_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'], 'integer'],
            [['description'], 'string'],
            [['title', 'jira_number', 'fix_status', 'priority', 'status', 'type'], 'string', 'max' => 255],
            [['developer_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['developer_user_id' => 'id']],
            [['qa_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['qa_user_id' => 'id']],
            [['reporter_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['reporter_user_id' => 'id']],
            ['status', 'in', 'range' => array_values(self::getConstants('STATUS'))],
            ['fix_status', 'in', 'range' => array_values(self::getConstants('FIX_STATUS'))],
            ['priority', 'in', 'range' => array_values(self::getConstants('PRIORITY'))],
            ['type', 'in', 'range' => array_values(self::getConstants('TYPE'))],
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
            'qa_user_id' => Yii::t('app', 'Qa User ID'),
            'developer_user_id' => Yii::t('app', 'Developer User ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'jira_number' => Yii::t('app', 'Jira Number'),
            'fix_status' => Yii::t('app', 'Fix Status'),
            'priority' => Yii::t('app', 'Priority'),
            'status' => Yii::t('app', 'Status'),
            'type' => Yii::t('app', 'Type'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }

    /**
     * Gets query for [[DeveloperUser]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getDeveloper()
    {
        return $this->hasOne(User::className(), ['id' => 'developer_user_id']);
    }

    /**
     * Gets query for [[QaUser]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getQa()
    {
        return $this->hasOne(User::className(), ['id' => 'qa_user_id']);
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
     * Gets query for [[BugFeatureFollowUps]].
     *
     * @return \yii\db\ActiveQuery|BugFeatureFollowUpQuery
     */
    public function getFollowUps()
    {
        return $this->hasMany(BugFeatureFollowUp::className(), ['bug_feature_id' => 'id']);
    }

    public function nextFollowUpDue($time = null)
    {
        if ($time === null) {
            $time = time();
        }
        return $this->hasOne(BugFeatureFollowUp::className(), ['bug_feature_id' => 'id'])
            ->where(['>', 'due_at', $time])
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->orderBy(['due_at' => SORT_ASC]);
    }

    /**
     * {@inheritdoc}
     * @return BugFeatureQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BugFeatureQuery(get_called_class());
    }
}

class BugFeatureQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BugFeature[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BugFeature|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
