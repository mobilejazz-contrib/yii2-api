<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'i18n'        => [
            'class'        => 'backend\modules\i18n\Module',
            'defaultRoute' => 'i18n-message/index',
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'bootstrap' =>
        [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'languages' =>
                    [
                        %languages%
                    ],
            ],
        ],
    'as locale'           => [
        'class'                   => 'common\behaviors\LocaleBehavior',
        'enablePreferredLanguage' => true,
    ],
    'params' => $params,
];
