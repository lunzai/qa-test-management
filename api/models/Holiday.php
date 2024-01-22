<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\models\SoftDeleteModel;

/**
 * This is the model class for table "holiday".
 *
 * @property int $id
 * @property string $title
 * @property string $start
 * @property string $end
 * @property int $start_ts
 * @property int $end_ts
 * @property string $status
 * @property int $country_id
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int|null $deleted_by
 *
 * @property Country $country
 */
class Holiday extends SoftDeleteModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'holiday';
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
        return ['country'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'start', 'end', 'status', 'country_id'], 'required'],
            [['start', 'end'], 'safe'],
            [['start_ts', 'end_ts', 'country_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_by', 'deleted_at'], 'integer'],
            [['title', 'status'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => array_values(self::getConstants('status'))],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['start', 'end'], 'date', 'format' => 'yyyy-MM-dd'],
            ['end', function ($attribute, $params, $validator) {
                if (!$this->start || !$this->end) {
                    $this->addError($attribute, 'Invalid date range.');
                } else {
                    if (strtotime($this->start) > strtotime($this->end)) {
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
            'title' => 'Title',
            'start' => 'Start',
            'end' => 'End',
            'start_ts' => 'Start Ts',
            'end_ts' => 'End Ts',
            'status' => 'Status',
            'country_id' => 'Country ID',
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
        return $fields;
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery|CountryQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * {@inheritdoc}
     * @return HolidayQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HolidayQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        $this->start_ts = strtotime($this->start);
        $this->end_ts = strtotime($this->end);
        return parent::beforeSave($insert);
    }
}

class HolidayQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Holiday[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Holiday|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
