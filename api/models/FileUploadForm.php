<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\File;
use yii\imagine\Image;

class FileUploadForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'mimeTypes' => 'image/*'],
        ];
    }

    public function upload($name = null, $path = null, $height = null, $width = null)
    {
        if (!$this->validate()) {
            return false;
        }
        $file = $this->file;
        $size = $file->size;
        $stream = fopen($file->tempName, 'r+');
        $name = $name ?: $file->name;
        if ($file->extension) {
            $name .= ".{$file->extension}";
        }
        $path = $path ? rtrim($path, '/') : '';
        $uploadPath = "{$path}/{$name}";
        if (!Yii::$app->fs->writeStream($uploadPath, $stream)) {
            return false;
        }
        // what a pain in the arse to get the following!
        $s3 = Yii::$app->fs->getFilesystem()->getAdapter()->getClient();
        $s3Bucket = Yii::$app->fs->getFilesystem()->getAdapter()->getBucket();
        $absolutePath = $s3->getObjectUrl($s3Bucket, $uploadPath);
        $model = new File([
            'name' => $name,
            'original_name' => $file->name ?: '',
            'extension' => $file->extension ?: null,
            'path_prefix' => Yii::$app->fs->baseUrl,
            'file_path' => $uploadPath,
            'absolute_path' => $absolutePath,
            'content_type' => $file->type,
            'size' => $size,
        ]);
        if ($model->save()) {
            return $model;
        } else {
            Yii::$app->fs->delete($uploadPath);
            return false;
        }
    }
}
