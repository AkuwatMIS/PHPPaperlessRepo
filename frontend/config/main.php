<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'on beforeRequest' => function () {
        /* if(!Yii::$app->request->isSecureConnection){
             $url = Yii::$app->request->getAbsoluteUrl();
             $url = str_replace('http:', 'https:', $url);
             Yii::$app->getResponse()->redirect($url);
             Yii::$app->end();
         }*/
        /*Yii::$app->catchAll = [
            'site/offline',
        ];*/
    },
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'test' => [
            'class' => 'frontend\modules\test\test\Test',
        ],
        'apitest' => [
            'class' => 'frontend\modules\test\api\Api',
        ],
        'api' => [
            'class' => 'frontend\modules\api\Api',
        ],
        'reportsapi' => [
            'class' => 'frontend\modules\reportsapi\reportsapi',
        ],
        'branch' => [
            'class' => 'frontend\modules\branch\Branch',
        ],
        'user-management' => [
            'class' => 'frontend\modules\user_management\UserManagement',
        ],
        'cih' => [
            'class' => 'frontend\modules\cih\Cih',
        ],
    ],
    'components' => [
        'request' => [
            //'enableCsrfValidation' => false,
            'csrfParam' => '_csrf-frontend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\Users',
            'enableAutoLogin' => false,
            'enableSession' => (Yii::$app->id !== 'api'),
            'autoRenewCookie' => false,
            'authTimeout' => 3600,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            'class' => 'yii\web\Session',
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
            'cookieParams' => ['lifetime' => 3600],
            'timeout' => 3600, //session expire
            'useCookies' => true,
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
        'cache'=> [
            'class' => 'yii\caching\MemCache',
            'useMemcached' => true,
            'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211,
                    'weight' => 50000,
                ],
            ],
        ],
        'sendGrid' => [
            'class' => 'bryglen\sendgrid\Mailer',
            'username' => 'junaidfayyaz945',
            'password' => 'gta945945',
            'viewPath' => '@app/mail', // your view path here
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp-mail.outlook.com',
                'username' => 'mishelpdesk@akhuwat.org.pk',
                'password' => '@123@EmmanAhssanIkhlas',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],


        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'apitest/application/<action:\w+>' => 'apitest/applications/<action>',
                'apitest/branch/<action:\w+>' => 'apitest/branches/<action>',
                'apitest/loan/<action:\w+>' => 'apitest/loans/<action>',
                'apitest/group/<action:\w+>' => 'apitest/groups/<action>',
                'apitest/fundrequest/<action:\w+>' => 'apitest/fund-requests/<action>',
                'apitest/disbursement/<action:\w+>' => 'apitest/disbursements/<action>',
                'apitest/version/<action:\w+>' => 'apitest/versions/<action>',
                'apitest/member/<action:\w+>' => 'apitest/members/<action>',
                'api/application/<action:\w+>' => 'api/applications/<action>',
                'api/branch/<action:\w+>' => 'api/branches/<action>',
                'api/loan/<action:\w+>' => 'api/loans/<action>',
                'api/group/<action:\w+>' => 'api/groups/<action>',
                'api/fundrequest/<action:\w+>' => 'api/fund-requests/<action>',
                'api/disbursement/<action:\w+>' => 'api/disbursements/<action>',
                'api/version/<action:\w+>' => 'api/versions/<action>',
                'api/member/<action:\w+>' => 'api/members/<action>',
                'cih/member/<action:\w+>' => 'cih/members/<action>'
            ],
        ],

    ],
    'params' => $params,
];