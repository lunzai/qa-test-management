<?php

namespace app\controllers;

use Yii;
use yii\base\Controller;
use yii\base\ErrorHandler;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                '*' => Response::FORMAT_JSON,
            ],
        ];
        return $behaviors;
    }

    public function actionError()
    {
        $handler = Yii::$app->errorHandler;
        $output = [
            'name' => $handler->getExceptionName($handler->exception),
            'message' => $handler->exception->getMessage(),
            'code' => $handler->exception->getCode(),
        ];
        if (YII_DEBUG) {
            $output['content'] = ErrorHandler::convertExceptionToVerboseString($handler->exception);
        }
        return $output;
    }
}
