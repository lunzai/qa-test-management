<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use app\models\User;
use app\models\SignupForm;
use app\models\LoginForm;
use app\models\PasswordForm;
use app\models\UserSearch;
use yii\web\ServerErrorHttpException;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

class UserController extends ActiveController
{
    public $modelClass = User::class;
    
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['auth']['except'] = ['auth', 'create'];
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['auth', 'create'],
                    'roles' => ['?'],
                ],
                [
                    'actions' => ['delete'],
                    'allow' => true,
                    'roles' => [User::ROLE_ADMIN],
                ],
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }
    
    protected function verbs(): array
    {
        $verbs = parent::verbs();
        return $verbs;
    }
    
    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }
    
    public function checkAccess($action, $model = null, $params = [])
    {
//        if ($model && ($action == 'update' || $action == 'view')) {
//            if (!Yii::$app->user->can(User::PERMISSION_UPDATE, ['user_id' => $model->id])) {
//                throw new ForbiddenHttpException('You are not authorized.');
//            }
//        }
    }

    public function prepareDataProvider()
    {
        $search = new UserSearch();
        return $search->search(Yii::$app->request->getQueryParams());
    }
    
    public function actionCheckAccess()
    {
        $response = Yii::$app->getResponse();
        $response->setStatusCode(204, 'OK');
        return [];
    }

    public function actionLogout()
    {
        if (Yii::$app->user->logout()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(204, 'Logout OK');
            return [];
        } else {
            throw new ServerErrorHttpException('Failed to process.');
        }
    }
    
    public function actionAuth()
    {
        $loginForm = new LoginForm();
        $loginForm->load(Yii::$app->getRequest()->getBodyParams(), '');
        
        if ($loginForm->login()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(204, 'Login OK');
            $user = $loginForm->getUser();
            return [
                'user' => $user,
                'token' => Yii::$app->jwt->encodeToken($user->getToken(), $user->id),
                'token_expired_at' => Yii::$app->getFormatter()->asDatetime($user->jwt_token_expired_at),
                'token_expired_ts' => $user->jwt_token_expired_at,
            ];
        }
        if ($loginForm->hasErrors()) {
            return $loginForm;
        } else {
            throw new ServerErrorHttpException('Failed to process.');
        }
    }
    
    public function actionCreate()
    {
        $signupForm = new SignupForm();
        $signupForm->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($signupForm->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201, 'User created');
            $user = $signupForm->getUser();
            return [
                'user' => $user,
                'token' => Yii::$app->jwt->encodeToken($user->getToken(), $user->getId()),
                'token_expired_at' => Yii::$app->getFormatter()->asDatetime($user->jwt_token_expired_at),
                'token_expired_ts' => $user->jwt_token_expired_at,
            ];
        }
        if ($signupForm->hasErrors()) {
            return $signupForm;
        } else {
            throw new ServerErrorHttpException('Failed to update.');
        }
    }
    
    public function actionChangePassword($id)
    {
        $model = $this->findModel($id);
        $passwordForm = new PasswordForm([
            'user' => $model,
        ]);
        $passwordForm->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($passwordForm->save()) {
            Yii::$app->getResponse()->setStatusCode(204);
            return [];
        }
        if ($passwordForm->hasErrors()) {
            return $passwordForm;
        } else {
            throw new ServerErrorHttpException('Failed to update.');
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() || $model->hasErrors()) {
            return $model;
        } else {
            throw new ServerErrorHttpException('Failed to update.');
        }
    }

    public function actionAssignRole($id)
    {
        $model = $this->findModel($id);
        $body = Yii::$app->getRequest()->getBodyParams();
        $auth = Yii::$app->authManager;
        if (!isset($body['role'])) {
            throw new BadRequestHttpException('Missing parameters.');
        }
        $role = $auth->getRole(ucfirst($body['role']));
        if (!$role) {
            throw new NotFoundHttpException('Role not found.');
        }
        try {
            $auth->assign($role, $model->id);
        } catch (\Throwable $th) {
            throw new BadRequestHttpException('Duplicated assignment.');
        }
        return [];
    }

    public function actionRevokeRole($id)
    {
        $model = $this->findModel($id);
        $body = Yii::$app->getRequest()->getBodyParams();
        $auth = Yii::$app->authManager;
        if (!isset($body['role'])) {
            throw new NotFoundHttpException('Role not found.');
        }
        $role = $auth->getRole(ucfirst($body['role']));
        if (!$role) {
            throw new NotFoundHttpException('Role not found.');
        }
        $auth->revoke($role, $model->id);
        return [];
    }
    
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
