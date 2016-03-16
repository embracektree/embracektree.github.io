<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');
error_reporting(E_ERROR);
//ini_set('display_errors','on');
$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    //'controllerNamespace' => 'modules\controllers',
    'modules' => [
        'gii' => 'yii\gii\Module',
	'users'=>[
		'class'=>'app\modules\users\UsersModule',
	    ],

    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];
