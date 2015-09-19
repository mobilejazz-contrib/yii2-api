<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=%database_name%',
            'username' => '%database_name%',
            'password' => '%app_password%',
            'charset' => 'utf8',
        ],
		'mailer' => [
			'class' => 'nickcv\mandrill\Mailer',
			'apikey' => '%mandrill_api_key%',
			'useMandrillTemplates' => true,
		],
		'pn' => [
			'class' => 'common\components\PushNotification',
			'appid' => '%parse_appid%',
			'masterkey' => '%parse_masterkey%',
			'apikey' => '%parse_apikey%'
		],
    ],
];
