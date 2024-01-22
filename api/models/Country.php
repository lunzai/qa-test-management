<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\models\SoftDeleteModel;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $name
 * @property string $iso2
 * @property string $iso3
 * @property string $status
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int|null $deleted_by
 *
 * @property Holiday[] $holidays
 */
class Country extends SoftDeleteModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'country';
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
        return [
            [['name', 'iso2', 'iso3'], 'required'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_by', 'deleted_at'], 'integer'],
            [['name', 'iso2', 'iso3', 'status'], 'string', 'max' => 255],
            [['iso2', 'iso3'], 'filter', 'filter' => 'strtoupper'],
            [['status'], 'in', 'range' => array_values(self::getConstants('status'))],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'iso2' => 'Iso2',
            'iso3' => 'Iso3',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'deleted_at' => 'Deleted By',
            'deleted_by' => 'Deleted By',
        ];
    }

    public function getHolidays()
    {
        return $this->hasMany(Holiday::className(), ['country_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return CountryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CountryQuery(get_called_class());
    }
}

class CountryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Country[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Country|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
