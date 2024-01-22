<?php

namespace app\components;

use Yii;
use app\models\User;
use yii\web\ForbiddenHttpException;
use app\components\jwt\HttpBearerAuth;
use yii\filters\Cors;
use yii\filters\AccessControl;
use yii\filters\RateLimiter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Response;

class ActiveController extends \yii\rest\ActiveController
{
    public $enableCsrfValidation = false;
    
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    
    public function behaviors()
    {
        // $behaviors = parent::behaviors();
        // unset($behaviors['authenticator']);
        $behaviors = [];
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['POST', 'PUT', 'GET', 'DELETE', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];
        $behaviors['auth'] = [
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                '*' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
            'actions' => $this->verbs(),
        ];
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
        ];
        
        return $behaviors;
    }
    
    public function actions(): array
    {
        $actions = parent::actions();
        //$actions['create']['class'] = CreateAction::class;
        return $actions;
    }
    
    public function checkAccess($action, $model = null, $params = [])
    {
//        if ($model && ($action == 'update' || $action == 'view')) {
//            if (!Yii::$app->user->can(User::PERMISSION_UPDATE, ['user_id' => $model->user_id])) {
//                throw new ForbiddenHttpException('You are not authorized.');
//            }
//        }
    }
}
