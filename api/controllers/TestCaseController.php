<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use app\models\TestCaseSearch;

class TestCaseController extends ActiveController
{
    public $modelClass = 'app\models\TestCase';

    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $search = new TestCaseSearch();
        return $search->search(Yii::$app->request->getQueryParams());
    }
}
