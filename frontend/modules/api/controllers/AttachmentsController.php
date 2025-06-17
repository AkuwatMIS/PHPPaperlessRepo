<?php

namespace frontend\modules\api\controllers;


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
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;
use common\components\Helpers\CodeHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\StringHelper;

/**
 * Site controller
 */
class AttachmentsController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [
               /* 'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => ['authorize', 'register', 'accesstoken', 'index', 'forgotpassword'],
                ],*/
                /*'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['profile'],
                    'rules' => [
                        [
                            'actions' => ['profile'],
                            'allow' => true,
                            'roles' => ['*'],
                        ]
                    ],
                ],*/
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'profile' => ['GET'],
                        'image' => ['GET'],
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

    public function actionImage()
    {
        $input = $_GET;

        /*if (!isset($input["token"])) {
            return $this->sendFailedResponse(400, "Token missing");
        }*/

        //$date = base64_decode($input["token"]);

        /*if($date <= strtotime('-1 day',strtotime('now')) || $date > strtotime('now'))
        {
            return $this->sendFailedResponse(400, "Invalid Token");
        }*/
        return ImageHelper::getImageFromDisk($input["type"],$input['id'],$input["file_name"],true);

        //header('Content-Type: image/jpeg');
       /* header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");*/
        //header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
        //echo file_get_contents($file_url);
        //echo readfile($file_url);
        //file_put_contents(basename($file_url),file_get_contents($file_url));
        /*$response[] = (strtotime('-1 day',strtotime('now')));
        return $this->sendSuccessResponse(200, $response);*/

    }
}
