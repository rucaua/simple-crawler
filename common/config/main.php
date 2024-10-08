<?php

use yii\caching\FileCache;


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
    'bootstrap' => [
        'log',
    ],
];

