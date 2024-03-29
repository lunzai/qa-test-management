<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../env.php';

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', getenv('DEV_DEBUG'));
defined('YII_ENV') or define('YII_ENV', getenv('DEV_ENV'));

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
