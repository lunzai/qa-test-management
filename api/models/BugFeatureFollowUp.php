<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "bug_feature_follow_up".
 *
 * @property int $id
 * @property int $bug_feature_id
 * @property int|null $actor_user_id
 * @property string $status
 * @property string|null $description
 * @property int|null $due_at
 * @property int|null $is_resolved
 * @property int|null $resolved_at
 * @property int|null $resolved_by
 * @property string|null $resolved_detail
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $deleted_at
 * @property int|null $deleted_by
 *
 * @property User $actor
 * @property BugFeature $bugFeature
 */
class BugFeatureFollowUp extends \app\models\SoftDeleteModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bug_feature_follow_up';
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
        return ['bugFeature'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bug_feature_id', 'status'], 'required'],
            [['bug_feature_id', 'actor_user_id', 'due_at', 'resolved_at', 'resolved_by', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'], 'integer'],
            [['description'], 'string'],
            ['is_resolved' => 'boolean'],
            [['status', 'resolved_detail'], 'string', 'max' => 255],
            [['actor_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['actor_user_id' => 'id']],
            [['bug_feature_id'], 'exist', 'skipOnError' => true, 'targetClass' => BugFeature::className(), 'targetAttribute' => ['bug_feature_id' => 'id']],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'bug_feature_id' => Yii::t('app', 'Bug Feature ID'),
            'actor_user_id' => Yii::t('app', 'Actor User ID'),
            'status' => Yii::t('app', 'Status'),
            'description' => Yii::t('app', 'Description'),
            'due_at' => Yii::t('app', 'Due At'),
            'is_resolved' => Yii::t('app', 'Is Resolved'),
            'resolved_at' => Yii::t('app', 'Resolved At'),
            'resolved_by' => Yii::t('app', 'Resolved By'),
            'resolved_detail' => Yii::t('app', 'Resolved Detail'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }

    public function isFollowUp()
    {
        return ($this->due_at);
    }

    public function isComment()
    {
        return !$this->due_at;
    }

    public function isExpired()
    {
    }

    /**
     * Gets query for [[ActorUser]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getActor()
    {
        return $this->hasOne(User::className(), ['id' => 'actor_user_id']);
    }

    /**
     * Gets query for [[BugFeature]].
     *
     * @return \yii\db\ActiveQuery|BugFeatureQuery
     */
    public function getBugFeature()
    {
        return $this->hasOne(BugFeature::className(), ['id' => 'bug_feature_id']);
    }

    /**
     * {@inheritdoc}
     * @return BugFeatureFollowUpQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BugFeatureFollowUpQuery(get_called_class());
    }
}

class BugFeatureFollowUpQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BugFeatureFollowUp[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BugFeatureFollowUp|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
