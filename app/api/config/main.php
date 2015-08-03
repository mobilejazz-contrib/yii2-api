<?php
    use yii\web\Response;

    $params = array_merge(
        require(__DIR__ . '/../../common/config/params.php'),
        require(__DIR__ . '/../../common/config/params-local.php'),
        require(__DIR__ . '/params.php'),
        require(__DIR__ . '/params-local.php')
    );

    return [
        'id' => 'api',
        'basePath' => dirname(__DIR__),
        'controllerNamespace' => 'api\controllers',
        'modules' => [
            'oauth2' => [
                'class' => 'mobilejazz\yii2\oauth2server\Module',
				'options' => [
					'token_param_name' => 'accessToken',
					'access_lifetime' => 3600 * 24,
				],
                'storageMap' => [
                    'user_credentials' => 'common\models\User'
                ],
                'grantTypes' => [
                    'user_credentials' => [
                        'class' => 'OAuth2\GrantType\UserCredentials',
                    ],
                    'client_credentials' => [
                        'class' => 'OAuth2\GrantType\ClientCredentials',
                    ],
                    'refresh_token' => [
                        'class' => 'OAuth2\GrantType\RefreshToken',
                        'always_issue_new_refresh_token' => true
                    ]
                ]

            ],
        ],
		'bootstrap' => [
			[
				'class' => 'yii\filters\ContentNegotiator',
				'languages' => [
					'en',
				],
			],
		],
        'components' => [
            'user' => [
                'identityClass' => 'common\models\User',
                'enableAutoLogin' => false,
                'enableSession' => false,
                'loginUrl' => null
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
            'urlManager' => [
                'enablePrettyUrl' => true,
                'enableStrictParsing' => true,
                'showScriptName' => false,
                'rules' => [
                    ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
					'POST /users/reset-password' => 'user/reset-password',
					'POST oauth2/<action:\w+>' => 'oauth2/default/<action>',
                ],
            ],
			'urlManagerFrontEnd' => [
				'class' => 'yii\web\urlManager',
				'baseUrl' => '/',
				'enablePrettyUrl' => true,
				'showScriptName' => false,
			],
            'request' => [
                'parsers' => [
                    'application/json' => 'yii\web\JsonParser',
                ]
            ],
			'response' => [
				'class' => 'yii\web\Response',
				'on beforeSend' => function ($event) {
					$response = $event->sender;
					if ($response->data !== null && $response->statusCode!=200)
					{
						//Remove some useless data
						if (!YII_DEBUG)
						{
							unset($response->data["stack-trace"]);
							unset($response->data["type"]);
							unset($response->data["file"]);
							unset($response->data["line"]);
						}

						//Create a new structure
						$response->data = [
							'success' => $response->isSuccessful,
							'data' => $response->data,
						];
					}
				},
			],
        ],
        'params' => $params,
    ];
