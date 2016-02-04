<?php
return [
    'name' => '%app_name%',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'formatter' => [
            'decimalSeparator' => '.',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['user'],
        ],
        'i18n'        => [
            'translations' => [
                '*' => [
                    'class'                 => 'yii\i18n\DbMessageSource',
                    'forceTranslation'      => true,
                    'sourceLanguage'        => 'en',
                    'sourceMessageTable'    => '{{%i18n_source_message}}',
                    'messageTable'          => '{{%i18n_message}}',
                    //'enableCaching' => true,
                    //'cachingDuration' => 3600,
                    'on missingTranslation' => [ '\backend\modules\i18n\Module', 'missingTranslation' ],
                ],
            ],
        ],
        'notifier' => [
            'class' => 'common\components\Notifier',
        ],
    ],
];
