<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use app\models\HolidaySearch;
use app\models\User;

class HolidayController extends ActiveController
{
    public $modelClass = 'app\models\Holiday';

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
                    'roles' => ['@'],
                    'actions' => ['index'],
                ],
                [
                    'allow' => true,
                    'roles' => [User::ROLE_ADMIN],
                ],
            ],
        ];
        return $behaviors;
    }

    public function prepareDataProvider()
    {
        $search = new HolidaySearch();
        return $search->search(Yii::$app->request->getQueryParams());
    }
}
