<?php

namespace app\models;

use Yii;
use app\models\BaseTestModel;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "issue".
 *
 * @property int $id
 * @property int $group_id
 * @property int|null $qa_user_id
 * @property int|null $developer_user_id
 * @property string $name
 * @property string|null $description
 * @property string|null $jira_number
 * @property string|null $jira_url
 * @property string|null $lark_url
 * @property string $status
 * @property string $test_status
 * @property int $total_count
 * @property int $passed_count
 * @property int $failed_count
 * @property int $pending_count
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int|null $deleted_by
 *
 * @property User $developer
 * @property Group $group
 * @property User $qa
 * @property TestCase[] $testCases
 * @property TestCase[] $passedCases
 * @property TestCase[] $failedCases
 * @property TestCase[] $pendingCases
 */
class Issue extends BaseTestModel
{
    const STATUS_DEPLOYED = 'Deployed';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'issue';
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
        return ['group', 'testCases', 'qa', 'developer', 'testCases.results', 'testCases.results.user'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'name', 'status'], 'required'],
            [['group_id', 'qa_user_id', 'developer_user_id', 'total_count', 'passed_count', 'failed_count', 'pending_count',
            'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_by', 'deleted_at'], 'integer'],
            [['description'], 'string'],
            [['jira_number'], 'string', 'max' => 60],
            [['name', 'jira_url', 'lark_url', 'test_status'], 'string', 'max' => 255],
            ['jira_url', 'url', 'pattern' => '/^{schemes}:\/\/whitecoatglobal\.atlassian\.net.*/i'],
            ['lark_url', 'url', 'pattern' => '/^{schemes}:\/\/e2brv21nn6\.larksuite\.com.*/i'],
            ['jira_number', 'match', 'pattern' => '/[A-Z]+-[0-9]+/i'],
            [['developer_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['developer_user_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::class, 'targetAttribute' => ['group_id' => 'id']],
            [['qa_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['qa_user_id' => 'id']],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['test_status'], 'default', 'value' => self::TEST_RESULT_PENDING],
            ['test_status', 'in', 'range' => array_values(self::getConstants('TEST_RESULT'))],
            ['status', 'in', 'range' => array_values(self::getConstants('STATUS'))],
            [['total_count', 'passed_count', 'failed_count', 'pending_count'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'group_id' => Yii::t('app', 'Group ID'),
            'qa_user_id' => Yii::t('app', 'Qa User ID'),
            'developer_user_id' => Yii::t('app', 'Developer User ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'jira_number' => Yii::t('app', 'Jira Number'),
            'jira_url' => Yii::t('app', 'Jira Url'),
            'lark_url' => Yii::t('app', 'Lark Url'),
            'status' => Yii::t('app', 'Status'),
            'test_status' => Yii::t('app', 'Test Status'),
            'total_count' => Yii::t('app', 'Total Count'),
            'passed_count' => Yii::t('app', 'Passed Count'),
            'failed_count' => Yii::t('app', 'Failed Count'),
            'pending_count' => Yii::t('app', 'Pending Count'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'deleted_at' => 'Deleted By',
            'deleted_by' => 'Deleted By',
        ];
    }

    public function recalculate()
    {
        $this->passed_count = $this->getPassedCases()->count();
        $this->failed_count = $this->getFailedCases()->count();
        $this->pending_count = $this->getPendingCases()->count();
        $this->total_count = $this->passed_count + $this->failed_count + $this->pending_count;
        if ($this->total_count > 0 && $this->passed_count == $this->total_count) {
            $this->test_status = self::TEST_RESULT_PASSED;
        } elseif ($this->failed_count > 0) {
            $this->test_status = self::TEST_RESULT_FAILED;
        } else {
            $this->test_status = self::TEST_RESULT_PENDING;
        }
        return $this->save();
    }

    public function getPassedCases()
    {
        return $this->hasMany(TestCase::className(), ['issue_id' => 'id'])
            ->where([
                'test_status' => self::TEST_RESULT_PASSED,
                'status' => self::STATUS_ACTIVE,
            ]);
    }

    public function getFailedCases()
    {
        return $this->hasMany(TestCase::className(), ['issue_id' => 'id'])
            ->where([
                'test_status' => self::TEST_RESULT_FAILED,
                'status' => self::STATUS_ACTIVE,
            ]);
    }

    public function getPendingCases()
    {
        return $this->hasMany(TestCase::className(), ['issue_id' => 'id'])
            ->where([
                'test_status' => self::TEST_RESULT_PENDING,
                'status' => self::STATUS_ACTIVE,
            ]);
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
     * Gets query for [[Group]].
     *
     * @return \yii\db\ActiveQuery|GroupQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
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
     * Gets query for [[TestCases]].
     *
     * @return \yii\db\ActiveQuery|TestCaseQuery
     */
    public function getTestCases()
    {
        return $this->hasMany(TestCase::className(), ['issue_id' => 'id'])
            ->andWhere(['<>', 'status', self::STATUS_DELETED])
            ->orderBy([
                new Expression(sprintf("FIELD(status, '%s', '%s')", self::STATUS_ACTIVE, self::STATUS_INACTIVE)),
                new Expression(
                    sprintf(
                        "FIELD(test_status, '%s', '%s', '%s', '%s')",
                        self::TEST_RESULT_FAILED,
                        self::TEST_RESULT_PENDING,
                        self::TEST_RESULT_PASSED,
                        self::TEST_RESULT_UNABLE_TO_TEST
                    )
                ),
                'id' => SORT_DESC
            ]);
    }

    /**
     * {@inheritdoc}
     * @return IssueQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new IssueQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert && array_key_exists('group_id', $changedAttributes)) {
            $this->group->recalculate();
            Group::findOne($changedAttributes['group_id'])->recalculate();
        }
        if ($insert || array_key_exists('status', $changedAttributes) || array_key_exists('test_status', $changedAttributes)) {
            $this->group->recalculate();
        }
    }
}

class IssueQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    public function byId($id)
    {
        return $this->andWhere(['{{issue}}.id' => $id]);
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
