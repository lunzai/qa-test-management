<?php

namespace app\models;

use Yii;
use app\models\BaseTestModel;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "test_result".
 *
 * @property int $id
 * @property int $test_case_id
 * @property int $tester_user_id
 * @property string|null $version
 * @property string|null $platform
 * @property string $actual_result
 * @property string $status
 * @property string $test_status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int|null $deleted_by
 *
 * @property TestCase $testCase
 * @property User $user
 */
class TestResult extends BaseTestModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test_result';
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
        return [
            'testCase', 'testCase.issue', 'testCase.issue.group',
            'testCase.issue.qa', 'testCase.issue.developer', 'user'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $testStatusOptions = self::getConstants('TEST_RESULT');
        unset($testStatusOptions[ self::TEST_RESULT_PENDING ]);
        return [
            [['test_case_id', 'tester_user_id', 'actual_result', 'test_status', 'status'], 'required'],
            [['test_case_id', 'tester_user_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_by', 'deleted_at'], 'integer'],
            [['actual_result', 'status'], 'string'],
            [['version', 'platform', 'test_status'], 'string', 'max' => 255],
            [['test_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestCase::className(), 'targetAttribute' => ['test_case_id' => 'id']],
            [['tester_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['tester_user_id' => 'id']],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['test_status'], 'default', 'value' => self::TEST_RESULT_PENDING],
            ['test_status', 'in', 'range' => array_values($testStatusOptions)],
            ['status', 'in', 'range' => array_values(self::getConstants('STATUS'))],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'test_case_id' => Yii::t('app', 'Test Case ID'),
            'tester_user_id' => Yii::t('app', 'Tester User ID'),
            'version' => Yii::t('app', 'Version'),
            'platform' => Yii::t('app', 'Platform'),
            'actual_result' => Yii::t('app', 'Actual Result'),
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
     * Gets query for [[TestCase]].
     *
     * @return \yii\db\ActiveQuery|TestCaseQuery
     */
    public function getTestCase()
    {
        return $this->hasOne(TestCase::className(), ['id' => 'test_case_id']);
    }

    /**
     * Gets query for [[TesterUser]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'tester_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return TestResultQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TestResultQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $testCase = $this->testCase;
            $testCase->test_status = $this->test_status;
            $testCase->save();
        }
    }
}

class TestResultQuery extends \yii\db\ActiveQuery
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
