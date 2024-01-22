<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use app\models\BugFeatureSearch;

class BugFeatureController extends ActiveController
{
    public $modelClass = 'app\models\BugFeature';

    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public function prepareDataProvider()
    {
        $search = new BugFeatureSearch();
        return $search->search(Yii::$app->request->getQueryParams());
    }
}
