<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use app\models\TestResultSearch;
use app\models\User;

class TestResultController extends ActiveController
{
    public $modelClass = 'app\models\TestResult';

    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [User::ROLE_ADMIN],
                    'actions' => ['delete'],
                ],
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function prepareDataProvider()
    {
        $search = new TestResultSearch();
        return $search->search(Yii::$app->request->getQueryParams());
    }
}
