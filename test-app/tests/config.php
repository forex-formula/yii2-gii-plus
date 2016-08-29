<?php

return [
    'class' => 'yii\console\Application',
    'id' => 'test-app',
    'language' => 'ru',
    'basePath' => dirname(__DIR__),
    'vendorPath' => dirname(dirname(YII2_PATH)),
    'bootstrap' => ['gii'],
    'modules' => ['gii' => 'yii\gii\plus\Module'],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2_gii_plus_tests',
            'username' => 'travis',
            'charset' => 'utf8'
        ]
    ]
];
