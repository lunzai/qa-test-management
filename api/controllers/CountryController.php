<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use app\models\CountrySearch;
use app\models\User;

class CountryController extends ActiveController
{
    public $modelClass = 'app\models\Country';

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
        $search = new CountrySearch();
        return $search->search(Yii::$app->request->getQueryParams());
    }
}
