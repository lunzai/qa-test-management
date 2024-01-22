<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use app\models\GroupSearch;
use app\models\Group;
use yii\web\NotFoundHttpException;

class GroupController extends ActiveController
{
    public $modelClass = 'app\models\Group';

    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function actionStar($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->getUser()->getIdentity();
        $model->link('starredBy', $user);
        $response = Yii::$app->getResponse();
        $response->setStatusCode(204, 'OK');
        return [];
    }

    public function actionUnstar($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->getUser()->getIdentity();
        $model->unlink('starredBy', $user, true);
        $response = Yii::$app->getResponse();
        $response->setStatusCode(204, 'OK');
        return [];
    }

    public function actionStarred()
    {
        $user = Yii::$app->getUser()->getIdentity();
        $groups = $user->starredGroups;
        return $groups ?: [];
    }

    public function prepareDataProvider()
    {
        $search = new GroupSearch();
        return $search->search(Yii::$app->request->getQueryParams());
    }

    protected function findModel($id)
    {
        if (($model = Group::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
