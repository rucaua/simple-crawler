<?php

use yii\caching\FileCache;
use yii\debug\Module as DebugModule;


return [
    'name' => 'Carmanah Signs: Code Challenge',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => FileCache::class,
        ],
    ],
    'bootstrap' => ['log', 'debug'],
    'modules' => [
        'debug' => [
            'class' => DebugModule::class,
            'allowedIPs' => ['*'],
        ]
    ]
];

