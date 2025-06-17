<?php

namespace frontend\modules\reportsapi\controllers;


use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\CodeHelper;
use common\components\Helpers\ReportsHelper\UserHelper;
use common\components\Helpers\SmsHelper;
use common\models\AccessTokens;
use common\models\Designations;
use common\models\LocationLogs;
use yii\rest\ActiveController;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use common\models\Users;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Response;
use common\components\Helpers\ReportsHelper\StringHelper;
use common\components\Parsers\ReportsParser\ApiParser;
use common\components\Helpers\ReportsHelper\ApiHelper;
use yii\helpers\Url;

class UsersController extends \yii\web\Controller
{
    public $modelClass = 'common\models\Users';

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                //'only' => ['view', 'index'],  // in a controller
                // if in a module, use the following IDs for user actions
                // 'only' => ['user/view', 'user/index']
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
                'languages' => [
                    'en',
                    'de',
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    //'index'  => ['get'],
                    'login' => ['POST'],
                ],
            ]
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    // Login user api=============================================================================
    public function actionLogin()
    {
        $authenticate = Yii::$app->request->getBodyParams();
        $response = [];
        $headers        = Yii::$app->request->headers;
        $version_code   = $headers->get('version_code');

        if (isset($authenticate['username']) &&
            isset($authenticate['password'])
        ) {
            $model = new LoginForm();

            $model->username = $authenticate['username'];
            $model->password = $authenticate['password'];
            $model->type = "email";

            if(!ApiHelper::checkVersion($version_code)){
                $response['meta']['success'] = false;
                $response['meta']['code'] = 500;
                $response['data']['errors']['message'] = 'Please download latest App from play store.';
                $response['data']['errors']['type'] = "";
                return $response;
            }

            if ($model->validate()) {
                $user = $model->getUser();
                if ($user) {

                    $analytics['user_id'] = $user->id;
                    $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                    $analytics['type'] = 'reports';
                    AnalyticsHelper::create($analytics);

                    $designation_ids = array();
                    $designations = Designations::find()->where(['mobile'=>1])->all();
                    foreach ($designations as $d){
                        $designation_ids[] = $d->id;
                    }
                    if(in_array($user->designation_id,$designation_ids)){
                        $user->last_login_token = StringHelper::getRandom();
                        if ($user->save(false)) {
                            $response['meta']['success'] = true;
                            $response['meta']['code'] = 200;
                            $response['data']['access_token'] = $user->last_login_token;
                            $response['data']['user'] = ApiParser::parseUser($user);
                            $response['data']['modules'] = ApiParser::parseModules($user);
                        } else {
                            $response['meta']['success'] = false;
                            $response['meta']['code'] = 500;
                            $response['data']['errors']['message'] = 'Access Denied, please try again';
                            $response['data']['errors']['type'] = "";
                        }
                    }else{
                        $response['meta']['success'] = false;
                        $response['meta']['code'] = 500;
                        $response['data']['errors']['message'] = 'Unauthorized user are not allowed';
                        $response['data']['errors']['type'] = "";
                    }
                } else {
                    $response['meta']['success'] = false;
                    $response['meta']['code'] = 500;
                    $response['data']['errors']['message'] = 'Invalid User';
                    $response['data']['errors']['type'] = "";
                }

            } else {
                $response['meta']['success'] = false;
                $response['meta']['code'] = 500;
                $response['data']['errors']['message'] = 'Invalid Login info';
                $response['data']['errors']['type'] = "";
            }
        } else {
            $response['meta']['success'] = false;
            $response['meta']['code'] = 500;
            $response['data']['errors']['message'] = 'Invalid Data Format';
            $response['data']['errors']['type'] = "";
        }

        return $response;
    }

    // ForgotPassword user api=============================================================================
    public function actionForgotpassword()
    {
        $authenticate = Yii::$app->request->getBodyParams();
        $response = [];

        if (isset($authenticate['username'])) {
            $user = Users::find()->where(['email' => $authenticate['username']])->one();
            if ($user) {

                $analytics['user_id'] = $user->id;
                $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                $analytics['type'] = 'reports';
                AnalyticsHelper::create($analytics);

                $model = new PasswordResetRequestForm();
                $model->email = $authenticate['username'];
                if ($model->validate()) {
                    $code = CodeHelper::getCode();
                    $user->password = $code;
                    $user->setPassword($user->password);
                    $msg = SmsHelper::getPassCodeText($code,$authenticate['hash_signature']);
                    $sms = SmsHelper::Sendsms($user->mobile, $msg);
                    if ($sms->corpsms[0]->type == 'Success') {
                        SmsHelper::SmsLogs('register', $user);
                        $response['meta']['success'] = true;
                        $response['meta']['code'] = 200;
                        $response['data']['message'] = 'Your password for Akhuwat reports has been sent on your mobile';
                    } else {
                        $response['meta']['success'] = true;
                        $response['meta']['code'] = 200;
                        $response['data']['message'] = 'Sorry, we are unable to reset password for the provided email address.';
                    }
                }
                if ($user->save(false)) {
                    $response['meta']['success'] = true;
                    $response['meta']['code'] = 200;
                    $response['data']['message'] = 'Your password for Akhuwat reports has been sent on your mobile';

                } else {
                    $response['meta']['success'] = true;
                    $response['meta']['code'] = 200;
                    $response['data']['message'] = 'Password not changed';
                }
            } else {
                $response['meta']['success'] = false;
                $response['meta']['code'] = 500;
                $response['data']['errors']['message'] = 'Invalid User';
                $response['data']['errors']['type'] = "";
            }

        } else {
            $response['meta']['success'] = false;
            $response['meta']['code'] = 500;
            $response['data']['errors']['message'] = 'Invalid Data Format';
            $response['data']['errors']['type'] = "";
        }

        return $response;
    }

    // RessetPassword user api=============================================================================
    public function actionRessetpassword()
    {
        $authenticate = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $response = [];

        if (isset($authenticate['old_password']) && isset($authenticate['new_password'])) {
            $user = Users::find()->where(['last_login_token' => $access_token])->one();
            if ($user) {

                $analytics['user_id'] = $user->id;
                $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                $analytics['type'] = 'reports';
                AnalyticsHelper::create($analytics);

                if (Yii::$app->security->validatePassword($authenticate['old_password'], $user->password)) {
                    $user->password = Yii::$app->security->generatePasswordHash($authenticate['new_password']);
                    if ($user->save(false)) {

                        $response['meta']['success'] = true;
                        $response['meta']['code'] = 200;
                        $response['data']['message'] = 'Password Updated Successfully';
                        $response['data']['prompt'] = false;

                    } else {
                        $response['meta']['success'] = false;
                        $response['meta']['code'] = 500;
                        $response['data']['message'] = 'Failed';
                    }
                } else {
                    $response['meta']['success'] = false;
                    $response['meta']['code'] = 500;
                    $response['data']['errors']['message'] = 'Invalid Old Password';
                }

            } else {
                $response['meta']['success'] = false;
                $response['meta']['code'] = 500;
                $response['data']['errors']['message'] = 'Invalid User';
                $response['data']['errors']['type'] = "";
            }
        } else {
            $response['meta']['success'] = false;
            $response['meta']['code'] = 500;
            $response['data']['errors']['message'] = 'Invalid Data Format';
            $response['data']['errors']['type'] = "";
        }

        return $response;
    }

    // User logout api=========================================================================
    public function actionLogout()
    {
        $logout = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $response = [];

        $user = Users::find()->where(['last_login_token' => $access_token])->one();

        if ($user) {

            $analytics['user_id'] = $user->id;
            $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
            $analytics['type'] = 'reports';
            AnalyticsHelper::create($analytics);

            $user->last_login_token = "logout";
            $user->save();
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['data']['message'] = "User logout successfully";
        } else {
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "invalid access token or already logout";
        }


        return $response;
    }

    public function actionUpdatelocation()
    {
        $input = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        if (!empty($access_token)) {
            $user = Users::find()->where(['last_login_token' => $access_token])->one();
            if ($user) {

                $analytics['user_id'] = $user->id;
                $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                $analytics['type'] = 'reports';
                AnalyticsHelper::create($analytics);

                if (isset($input['latitude'])) {
                    if (isset($input['longitude'])) {
                        $users['Users'] = $input;
                        if ($user->load($users)) {
                            $user->longitude = $input['longitude'];
                            $user->latitude = $input['latitude'];
                            if ($user->save()) {
                                $location_logs = new LocationLogs();
                                $location_logs->user_id = $user->id;
                                $location_logs->device_id = $user->id;
                                $location_logs->latitude = $user->latitude;
                                $location_logs->longitude = $user->longitude;
                                $location_logs->created_on = date('Y-m-d h:i:s');
                                $location_logs->save();
                                $response['meta']['success'] = true;
                                $response['meta']['code'] = 200;
                                $response['meta']['message'] = 'Location updated successfully';
                            } else {
                                $error = '';
                                foreach ($user->getErrors() as $m) {
                                    $error = $m[0];
                                }
                                $response['meta']['success'] = false;
                                $response['meta']['message'] = $error;
                                $response['meta']['code'] = 500;
                                //throw new \yii\web\HttpException(500, Yii::t('app',$error),500);
                            }
                        } else {
                            $response['meta']['success'] = false;
                            $response['meta']['message'] = 'Data not load successfully!';
                            $response['meta']['code'] = 500;
                            //throw new \yii\web\HttpException(500, Yii::t('app','Data not load successfully!'),500);
                        }
                    } else {
                        $response['meta']['success'] = false;
                        $response['meta']['message'] = 'Longitude is required!';
                        $response['meta']['code'] = 500;
                        //throw new \yii\web\HttpException(500, Yii::t('app','Amount is required!'),500);
                    }
                } else {
                    $response['meta']['success'] = false;
                    $response['meta']['message'] = 'Latitude is required!';
                    $response['meta']['code'] = 500;
                    //throw new \yii\web\HttpException(500, Yii::t('app','Loan ID is required!'),500);
                }
            } else {
                $response['meta']['success'] = false;
                $response['meta']['message'] = 'Invalid access token!';
                $response['meta']['code'] = 401;
                //throw new \yii\web\HttpException(401, Yii::t('app','Invalid access token or post token!'),401);
            }
        } else {
            $response['meta']['success'] = false;
            $response['meta']['message'] = 'access token is required!';
            $response['meta']['code'] = 500;
            //throw new \yii\web\HttpException(500, Yii::t('app','access token is required!'),500);
        }
        return $response;
    }


}

