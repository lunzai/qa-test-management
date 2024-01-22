<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => getenv('DB_DSN'),
    'username' => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'charset' => getenv('DB_CHARSET'),

    // Schema cache options (for production environment)
    'enableSchemaCache' => getenv('SCHEMA_CACHE'),
    'schemaCacheDuration' => getenv('SCHEMA_CACHE_DURATION'),
    'schemaCache' => 'cache',
    'enableLogging' => getenv('DB_LOGGING'),
    'enableProfiling' => getenv('DB_PROFILING'),
    'enableQueryCache' => getenv('QUERY_CACHE'),
    'queryCache' => 'cache',
    'queryCacheDuration' => getenv('QUERY_CACHE_DURATION'),
];
