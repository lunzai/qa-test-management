<?php

return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        'POST file/wysiwyg' => 'file/wysiwyg',
        'POST cypress/clean-up' => 'cypress/clean-up',
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'user',
            'extraPatterns' => [
                'OPTIONS <action>' => 'options',
                'OPTIONS <action>/<id:\d+>' => 'options',
                'GET check-access' => 'check-access',
                'POST auth' => 'auth',
                'POST logout' => 'logout',
                'POST create' => 'create',
                'PUT,PATCH change-password/<id:\d+>' => 'change-password',
                'PUT,PATCH forget-password' => 'forget-password',
                'PUT,PATCH reset-password' => 'reset-password',
                'PUT,PATCH assign-role/<id:\d+>' => 'assign-role',
                'PUT,PATCH revoke-role/<id:\d+>' => 'revoke-role',
            ],
        ],
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'group',
            'extraPatterns' => [
                'OPTIONS <action>' => 'options',
                'OPTIONS <action>/<id:\d+>' => 'options',
                'GET starred' => 'starred',
                'PUT,PATCH star/<id:\d+>' => 'star',
                'PUT,PATCH unstar/<id:\d+>' => 'unstar',
            ],
        ],
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'issue',
            'extraPatterns' => [
                'OPTIONS <action>' => 'options',
                'GET assigned' => 'assigned',
            ]
        ],
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'test-case',
            'extraPatterns' => [
            ]
        ],
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'test-result',
            'extraPatterns' => [
            ]
        ],
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'timeline',
            'extraPatterns' => [
                'GET resource' => 'resource',
                'GET holiday' => 'holiday',
                'GET country' => 'country',
            ]
        ],
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'holiday',
            'extraPatterns' => [
            ]
        ],
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'country',
            'extraPatterns' => [
            ]
        ],
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'bug-feature',
            'extraPatterns' => [
            ]
        ],
        [
            'class' => \app\components\UrlRule::class,
            'controller' => 'follow-up',
            'extraPatterns' => [
            ]
        ],
    ],
];
