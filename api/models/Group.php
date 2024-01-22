<?php

namespace app\models;

use Yii;
use app\models\BaseTestModel;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "group".
 *
 * @property int $id
 * @property string $name
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
 * @property Issue[] $issues
 * @property Issue[] $passedIssues
 * @property Issue[] $failedIssues
 * @property Issue[] $pendingIssues
 * @property User[] $starredBy
 */
class Group extends BaseTestModel
{
    const STATUS_DEPLOYED = 'Deployed';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group';
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
        return ['issues', 'starredBy'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['total_count', 'passed_count', 'failed_count', 'pending_count', 'created_at', 'created_by',
            'updated_at', 'updated_by', 'deleted_by', 'deleted_at'], 'integer'],
            [['name', 'status', 'test_status'], 'string', 'max' => 255],
            ['test_status', 'in', 'range' => array_values(self::getConstants('TEST_RESULT'))],
            ['status', 'in', 'range' => array_values(self::getConstants('STATUS'))],
            [['total_count', 'passed_count', 'failed_count', 'pending_count'], 'default', 'value' => 0],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['test_status'], 'default', 'value' => self::TEST_RESULT_PENDING],
            ['name', 'unique', 'targetAttribute' => ['name', 'status']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
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
        $this->passed_count = (int) $this->getPassedIssues()->count();
        $this->failed_count = (int) $this->getFailedIssues()->count();
        $this->pending_count = (int) $this->getPendingIssues()->count();
        $this->total_count = $this->passed_count + $this->failed_count + $this->pending_count;
        if ($this->passed_count == $this->total_count) {
            $this->test_status = self::TEST_RESULT_PASSED;
        } elseif ($this->failed_count > 0) {
            $this->test_status = self::TEST_RESULT_FAILED;
        } else {
            $this->test_status = self::TEST_RESULT_PENDING;
        }
        return $this->save();
    }

    public function getPassedIssues()
    {
        return $this->hasMany(Issue::className(), ['group_id' => 'id'])
            ->where([
                'test_status' => self::TEST_RESULT_PASSED,
                'status' => self::STATUS_ACTIVE,
            ]);
    }

    public function getFailedIssues()
    {
        return $this->hasMany(Issue::className(), ['group_id' => 'id'])
            ->where([
                'test_status' => self::TEST_RESULT_FAILED,
                'status' => self::STATUS_ACTIVE,
            ]);
    }

    public function getPendingIssues()
    {
        return $this->hasMany(Issue::className(), ['group_id' => 'id'])
            ->where([
                'test_status' => self::TEST_RESULT_PENDING,
                'status' => self::STATUS_ACTIVE,
            ]);
    }

    /**
     * Gets query for [[Issues]].
     *
     * @return \yii\db\ActiveQuery|IssueQuery
     */
    public function getIssues()
    {
        return $this->hasMany(Issue::className(), ['group_id' => 'id'])
            ->andWhere(['<>', 'status', self::STATUS_DELETED])
            ->orderBy([
                new Expression(
                    sprintf(
                        "FIELD(status, '%s', '%s', '%s')",
                        self::STATUS_ACTIVE,
                        self::STATUS_DEPLOYED,
                        self::STATUS_INACTIVE,
                    )
                ),
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
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStarredBy($userId = null)
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable(
                'group_user',
                ['group_id' => 'id'],
                $userId === null ? null : function ($query) use ($userId) {
                    return $query->where(['user_id' => $userId]);
                }
            );
    }

    /**
     * {@inheritdoc}
     * @return GroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GroupQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        // $this->test_status =
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
    }
}

class GroupQuery extends \yii\db\ActiveQuery
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
