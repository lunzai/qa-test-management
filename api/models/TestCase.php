<?php

namespace app\models;

use Yii;
use app\models\BaseTestModel;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "test_case".
 *
 * @property int $id
 * @property int $issue_id
 * @property string $short_description
 * @property string $description
 * @property string|null $platform
 * @property string|null $pre_condition
 * @property string $replicate_step
 * @property string $expected_result
 * @property string $status
 * @property string $test_status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int|null $deleted_by
 *
 * @property Issue $issue
 * @property TestResult[] $results
 */
class TestCase extends BaseTestModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test_case';
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = TimestampBehavior::class;
        $behaviors['blameable'] = BlameableBehavior::class;
        return $behaviors;
    }

    public function extraFields()
    {
        return ['issue', 'issue.group', 'issue.qa', 'issue.developer', 'results', 'results.user'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['issue_id', 'short_description', 'description', 'replicate_step', 'expected_result', 'status'], 'required'],
            [['issue_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_by', 'deleted_at'], 'integer'],
            [['description', 'replicate_step', 'expected_result'], 'string'],
            [['short_description', 'platform', 'pre_condition', 'test_status'], 'string', 'max' => 255],
            [['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['test_status'], 'default', 'value' => self::TEST_RESULT_PENDING],
            ['test_status', 'in', 'range' => array_values(self::getConstants('TEST_RESULT'))],
            ['status', 'in', 'range' => array_values(self::getConstants('status'))],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'issue_id' => Yii::t('app', 'Issue ID'),
            'short_description' => Yii::t('app', 'Short Description'),
            'description' => Yii::t('app', 'Description'),
            'platform' => Yii::t('app', 'Platform'),
            'pre_condition' => Yii::t('app', 'Pre Condition'),
            'replicate_step' => Yii::t('app', 'Replicate Step'),
            'expected_result' => Yii::t('app', 'Expected Result'),
            'status' => Yii::t('app', 'Status'),
            'test_status' => Yii::t('app', 'Test Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'deleted_at' => 'Deleted By',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * Gets query for [[Issue]].
     *
     * @return \yii\db\ActiveQuery|IssueQuery
     */
    public function getIssue()
    {
        return $this->hasOne(Issue::className(), ['id' => 'issue_id']);
    }

    /**
     * Gets query for [[TestResults]].
     *
     * @return \yii\db\ActiveQuery|TestResultQuery
     */
    public function getResults()
    {
        return $this->hasMany(TestResult::className(), ['test_case_id' => 'id'])
            ->andWhere(['<>', 'status', self::STATUS_DELETED])
            ->orderBy([
                new Expression(sprintf("FIELD(status, '%s', '%s')", self::STATUS_ACTIVE, self::STATUS_INACTIVE)),
                'id' => SORT_DESC
            ]);
    }

    /**
     * {@inheritdoc}
     * @return TestCaseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TestCaseQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert || array_key_exists('status', $changedAttributes) || array_key_exists('test_status', $changedAttributes)) {
            $this->issue->recalculate();
        }
    }
}

class TestCaseQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }
}
