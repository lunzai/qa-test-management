<?php

use yii\helpers\ArrayHelper;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$route = require __DIR__ . '/route.php';
$cache = [];
if (file_exists(__DIR__ . '/cache-local.php')) {
    $cache = require __DIR__ . '/cache-local.php';
}

$config = [
    'id' => 'qa-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'fs' => [
            'class' => creocoder\flysystem\AwsS3Filesystem::class,
            'key' => getenv('AWS_ACCESS'),
            'secret' => getenv('AWS_SECRET'),
            'bucket' => getenv('S3_BUCKET'),
            'region' => getenv('S3_REGION'),
            'baseUrl' => getenv('DEV_ENV'),
        ],
        'jwt' => [
            'class' => \app\components\jwt\Jwt::class,
            'secret' => getenv('JWT_SECRET'),
            'algo' => getenv('JWT_ALGO'),
            'expiryDuration' => getenv('JWT_EXPIRY_DURATION'),
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => getenv('COOKIE_VALIDATION_KEY'),
            'parsers' => [
                'application/json' => \yii\web\JsonParser::class,
            ],
        ],
        'cache' => ArrayHelper::merge([
            'class' => \yii\caching\DummyCache::class,
            'defaultDuration' => getenv('CACHE_DEFAULT_DURATION'),
            'keyPrefix' => getenv('CACHE_PREFIX'),
        ], $cache),
        'user' => [
            'class' => \app\components\User::class,
        ],
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'flushInterval' => 1,
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'exportInterval' => 1,
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => $route,
        'response' => [
            'class' => 'yii\web\Response',
            'charset' => 'UTF-8',
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => \yii\web\JsonResponseFormatter::class,
                    'prettyPrint' => false,
                ],
            ],
            'on beforeSend' => function ($event) {
                // allow GII/DEBUG to return HTML
                if (YII_ENV_DEV && Yii::$app->controller && Yii::$app->controller->module) {
                    if (Yii::$app->controller->module instanceof yii\gii\Module || Yii::$app->controller->module instanceof yii\debug\Module) {
                        return;
                    }
                }
                $response = $event->sender;
                if ($response->data !== null) {
                    $response->format = yii\web\Response::FORMAT_JSON;
                    $header = $response->headers;
                    if ($header->has('Access-Control-Allow-Origin')) {
                        $header->set('Access-Control-Allow-Origin', '*');
                    } else {
                        $header->add('Access-Control-Allow-Origin', '*');
                    }
                    $output = [
                        'success' => $response->getIsSuccessful(),
                        'status' => $response->getStatusCode(),
                        'message' => $response->statusText,
                        // 'data' => $response->getIsSuccessful() ? $response->data : [],
                        'data' => $response->data,
                    ];
                    $response->data = $output;
                    $response->setStatusCode(200);
                }
            },
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
