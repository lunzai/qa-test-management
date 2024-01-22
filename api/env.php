<?php

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->load();

$dotenv->required([
    'DB_ENGINE', 'DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD', 'DB_CHARSET', 'DB_DSN',
    'COOKIE_VALIDATION_KEY', 'DEV_ENV',
    'AWS_ACCESS', 'AWS_SECRET', 'S3_BUCKET', 'S3_REGION',
    'JWT_SECRET', 'JWT_ALGO', 'JWT_EXPIRY_DURATION',
    'CACHE_DEFAULT_DURATION', 'CACHE_PREFIX'
])->notEmpty();

$dotenv->required([
    'DEV_DEBUG',
])->isBoolean();

$dotenv->required([
    'DEV_ENV',
])->allowedValues(['prod', 'dev', 'test']);

// https://packagist.org/packages/vlucas/phpdotenv
//$dotenv->required()->isInteger();
//$dotenv->required()->allowedRegexValues('');
