<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=%app_name%',
            'username' => '%app_name%',
            'password' => '%app_password%',
            'charset' => 'utf8',
        ],
    ],
];
