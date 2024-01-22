<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $name
 * @property string $original_name
 * @property string|null $extension
 * @property string|null $path_prefix
 * @property string $file_path
 * @property string $absolute_path
 * @property int $size
 * @property string|null $content_type
 * @property int|null $created_at
 * @property int|null $created_by
 */
class File extends \app\components\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => false,
        ];
        $behaviors['blameable'] = [
            'class' => BlameableBehavior::class,
            'createdByAttribute' => 'created_by',
            'updatedByAttribute' => false,
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'original_name', 'file_path', 'absolute_path'], 'required'],
            [['size', 'created_at', 'created_by'], 'integer'],
            [['name', 'original_name', 'extension', 'path_prefix', 'file_path', 'absolute_path', 'content_type'], 'string', 'max' => 255],
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
            'original_name' => 'Original Name',
            'extension' => 'Extension',
            'path_prefix' => 'Path Prefix',
            'file_path' => 'File Path',
            'absolute_path' => 'Absolute Path',
            'size' => 'Size',
            'content_type' => 'Content Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }
}
