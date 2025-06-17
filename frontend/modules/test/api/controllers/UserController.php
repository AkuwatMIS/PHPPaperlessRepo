<?php

namespace frontend\modules\test\api\controllers;


use common\components\Helpers\ImageHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\MobileRolesHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\Images;
use common\models\MobilePermissions;
use common\models\Users;
use frontend\models\PasswordResetRequestForm;
use Psr\Log\InvalidArgumentException;
use Yii;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\AuthorizationCodes;
use common\models\AccessTokens;

use frontend\modules\test\api\models\SignupForm;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;
use common\components\Helpers\CodeHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\StringHelper;

/**
 * Site controller
 */
class UserController extends RestController
{
    public $rbac_type = 'api';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [
                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => ['roles'],
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'denyCallback' => function ($rule, $action) {
                        return print_r(json_encode($this->sendFailedResponse('401','You are not allowed to perform this action.')));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'roles' => ['GET'],
                    ],
                ],
            ];
    }


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionRole()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');
        $response = [];
        $user = Users::findIdentityByAccessToken($access_token);
        $response = MobileRolesHelper::getRoles($user->id);
        return $this->sendSuccessResponse(200, $response);

    }
}
