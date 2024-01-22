<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use yii\web\UploadedFile;
use app\models\FileUploadForm;
use yii\web\BadRequestHttpException;

class FileController extends ActiveController
{
    public $modelClass = 'app\models\File';

    public function actions(): array
    {
        return [];
    }

    public function actionWysiwyg()
    {
        $file = UploadedFile::getInstanceByName('file');
        $fileName = Yii::$app->security->generateRandomString(8);
        $path = 'wysiwyg/' . date('Y/m/d');
        $form = new FileUploadForm(['file' => $file]);
        $upload = $form->upload($fileName, $path);
        if ($upload) {
            return $upload;
        } elseif ($form->hasErrors()) {
            return $form;
        } else {
            throw new BadRequestHttpException('Unable to upload');
        }
    }
}
