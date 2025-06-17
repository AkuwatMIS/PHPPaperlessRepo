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
class SiteController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [
                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => ['authorize', 'register', 'accesstoken', 'index', 'forgotpassword'],
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['logout', 'signup'],
                    'rules' => [
                        [
                            'actions' => ['signup'],
                            'allow' => true,
                            'roles' => ['?'],
                        ],
                        [
                            'actions' => ['logout', 'me'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                        [
                            'actions' => ['authorize', 'register', 'accesstoken'],
                            'allow' => true,
                            'roles' => ['*'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'logout' => ['GET'],
                        'authorize' => ['POST'],
                        'register' => ['POST'],
                        'accesstoken' => ['POST'],
                        'me' => ['GET'],
                        'resetpassword' => ['POST'],
                        'updateprofile' => ['POST'],
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

    public function actionAccesstoken()
    {
        if (!isset($this->request["authorization_code"])) {
            return $this->sendFailedResponse(400, "Authorization code missing");
        }

        $authorization_code = $this->request["authorization_code"];

        $auth_code = AuthorizationCodes::isValid($authorization_code);
        if (!$auth_code) {
            return $this->sendFailedResponse(400, "Invalid Authorization Code");
        }

        $accesstoken = Yii::$app->api->createAccesstoken($authorization_code);

        $response = [];
        $user = Users::findIdentityByAccessToken($accesstoken->token);
        $response['access_token'] = $accesstoken->token;
        $response['expires_at'] = $accesstoken->expires_at;
        $response['message'] = "Login Successfully";
        $response['user'] = ApiParser::parseUser($user);
        $response['user']['roles'] = MobileRolesHelper::getRoles($user->id);
        $response['user']['designation'] = UsersHelper::getDesignation($user->id);
        $image = Images::findOne(['parent_id' => $response['user']['id'], 'parent_type' => 'users', 'image_type' => 'profile']);
        $response['user']['profile_pic'] = ApiParser::parseImage($image, $response['user']['id']);
        $response['do_reset_password'] = $user->do_reset_password;
        $response['do_complete_profile'] = $user->do_complete_profile;
        return $this->sendSuccessResponse(200, $response);

    }

    public function actionAuthorize()
    {
        $model = new LoginForm();
        $model->attributes = $this->request;

        if ($model->validate() && $model->login()) {

            $auth_code = Yii::$app->api->createAuthorizationCode(Yii::$app->user->identity['id']);

            $response = [];
            $response['authorization_code'] = $auth_code->code;
            $response['expires_at'] = $auth_code->expires_at;

            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(401, $model->errors);
        }
    }

    public function actionForgotpassword()
    {
        $authenticate = $this->request;

        $response = [];
        if (isset($authenticate['username']) && isset($authenticate['type']) && isset($authenticate['cnic'])) {

            if ($authenticate['type'] == 'email') {
                $user = Users::find()->where(['email' => $authenticate['username'], 'cnic' => $authenticate['cnic']])->one();
                if ($user) {
                    $model = new PasswordResetRequestForm();
                    $model->email = $authenticate['username'];

                    if ($model->validate()) {
                        $code = CodeHelper::getCode();
                        $user->password = $code;
                        $user->setPassword($user->password);
                        $msg = SmsHelper::getCodeText($code);
                        $user->do_reset_password = 0;
                        $user->do_complete_profile = 0;
                        if ($user->save()) {
                            /*  if ($model->sendEmail($code)) {*/
                                $sms = SmsHelper::Sendsms($user->mobile, $msg);
                                if ($sms->corpsms[0]->type == 'Success') {
                                    SmsHelper::SmsLogs('register', $user);
                                    $response['message'] = 'Your password for Akhuwat paperless has been sent to your mobile';
                                    return $this->sendSuccessResponse(200, $response);
                                } else {
                                    return $this->sendFailedResponse(500, 'Internal server error');
                                }
//                                $response['message'] = "Passcode has been sent to your email address";
//                                return $this->sendSuccessResponse(200, $response);
                           /* } else {
                                return $this->sendFailedResponse(500, 'Internal server error');
                            }*/
                        }else{
                            return $this->sendFailedResponse(500, 'Internal server error on update user data.');
                        }
                    } else {
                        return $this->sendFailedResponse(401, "Invalid User. Please Contact Support Person at: mishelpdesk@akhuwat.org.pk");
                    }
                }
                else{
                    return $this->sendFailedResponse(401, "Invalid Email or CNIC");
                }
            } else if ($authenticate['type'] == 'emp_code') {
                $user = Users::find()->where(['emp_code' => $authenticate['username'], 'cnic' => $authenticate['cnic']])->one();

                if ($user) {
                    $code = CodeHelper::getCode();
                    $user->password = $code;
                    $user->setPassword($user->password);
                    //$user->password = crypt($code,'13');
                    $msg = SmsHelper::getCodeText($code);
                    $user->do_reset_password = 0;
                    $user->do_complete_profile = 0;
                    if ($user->save()) {
                        $sms = SmsHelper::Sendsms($user->mobile, $msg);
                        if ($sms->corpsms[0]->type == 'Success') {
                            SmsHelper::SmsLogs('register', $user);
                            $response['message'] = 'Your password for Akhuwat paperless has been sent to ' . StringHelper::encryptMobile($user->mobile);
                            return $this->sendSuccessResponse(200, $response);
                        } else {
                            return $this->sendFailedResponse(500, 'Internal server error');
                        }
                    } else {
                        return $this->sendFailedResponse(401, $user->errors);
                    }
                } else {
                    return $this->sendFailedResponse(401, "Invalid User. Please Contact Support Person at: mishelpdesk@akhuwat.org.pk");
                }
            }
        } else {
            return $this->sendFailedResponse(400, "Type, CNIC, Username cannot be empty.");
        }

    }

    public function actionResetpassword()
    {
        $authenticate = $this->request;
        /*$headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');*/
        $response = [];

        if (isset($authenticate['new_password']) && isset($authenticate['confirm_password'])) {
            $user = Yii::$app->user->identity;
            if ($user) {
                if ($authenticate['new_password'] == $authenticate['confirm_password']) {
                    $user->password = Yii::$app->security->generatePasswordHash($authenticate['new_password']);
                    $user->do_reset_password = 1;
                    if ($user->save()) {
                        $response['message'] = "Password Reset Successfully";
                        return $this->sendSuccessResponse(200, $response);
                    } else {
                        return $this->sendFailedResponse(401, $user->getErrors());
                    }
                } else {
                    return $this->sendFailedResponse(400, "Password not match");
                }
            } else {
                return $this->sendFailedResponse(400, "Invalid User");
            }
        } else {
            return $this->sendFailedResponse(400, "New Password, Confirm Password cannot be empty.");
        }
    }

    public function actionUpdateprofile()
    {
        $response = [];
        $user = Yii::$app->user->identity;
        if ($user) {
            $user->attributes = $this->request;
            if (isset($this->request['profile_pic'])) {
                $base_code = $this->request['profile_pic'];
                $image_name = 'profile_' . rand(111111, 999999) . '.png';
                if (!(ImageHelper::imageUpload($user->id, 'users', 'profile', $image_name, $base_code))) {
                    return $this->sendFailedResponse(400, "Profile Picture not Saved");
                }
            }
            $user->do_complete_profile = 1;
            if ($user->save()) {
                $response['message'] = "Profile Updated Successfully";
                $response['user'] = ApiParser::parseUser($user);
                $response['user']['designation'] = UsersHelper::getDesignation($user->id);
                $image = Images::findOne(['parent_id' => $response['user']['id'], 'parent_type' => 'users', 'image_type' => 'profile']);
                $response['user']['profile_pic'] = ApiParser::parseImage($image, $response['user']['id']);
                //$response['user']['thumb_impression'] = MemberHelper::getUserThumbImpression($response['user']['id']);
                $response['do_reset_password'] = isset($user->password) ? 1 : 0;
                $response['do_complete_profile'] = isset($user->do_complete_profile) ? $user->do_complete_profile : 0;
                return $this->sendSuccessResponse(200, $response);
            } else {
                return $this->sendFailedResponse(401, $user->getErrors());
            }
        } else {
            return $this->sendFailedResponse(400, "Invalid User");
        }
    }

    public function actionLogout()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');
        $response = [];
        if (!$access_token) {
            $access_token = Yii::$app->getRequest()->getQueryParam('access-token');
        }

        $model = AccessTokens::findOne(['token' => $access_token]);

        if ($model->delete()) {
            $response['message'] = "Logged Out Successfully";
            return $this->sendSuccessResponse(200, $response);

        } else {
            return $this->sendFailedResponse(400, "Invalid Request");
        }
    }

}
