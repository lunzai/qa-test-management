<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use app\models\IssueSearch;

class IssueController extends ActiveController
{
    public $modelClass = 'app\models\Issue';

    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function prepareDataProvider()
    {
        $search = new IssueSearch();
        return $search->search(Yii::$app->request->getQueryParams());
    }

    public function actionAssigned()
    {
        $search = new IssueSearch([
            'assignedTo' => Yii::$app->getUser()->getId(),
        ]);
        return $search->search(Yii::$app->request->getQueryParams());
    }
}
