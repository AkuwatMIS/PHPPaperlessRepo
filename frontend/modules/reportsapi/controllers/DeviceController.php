<?php

namespace frontend\modules\reportsapi\controllers;


use common\components\Helpers\AnalyticsHelper;
use common\components\Helpers\ReportsHelper\ApiHelper;
use common\components\Helpers\ReportsHelper\UserHelper;
use common\models\AccessTokens;
use common\models\Devices;
use common\models\UserDevices;
use Yii;
use common\models\Users;
use common\models\Branches;
use common\models\Projects;
use common\models\ProgressReports;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\components\Parsers\ReportsParser\ApiParser;

class DeviceController extends \yii\web\Controller
{
    public $modelClass = 'common\models\Branches';

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
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }


    public function actionRegister()
    {
        $response = [];
        $input = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $version_code   = $headers->get('version_code');

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }

        if (!empty($access_token)) {
            $user = Users::find()->where(['last_login_token' => $access_token])->one();
            if ($user) {
                $analytics['user_id'] = $user->id;
                $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                $analytics['type'] = 'reports';
                AnalyticsHelper::create($analytics);
                if (isset($input['uu_id'])) {
                    if (isset($input['imei_no'])) {
                        if (isset($input['os_version'])) {
                            if (isset($input['device_model'])) {

                                $devices['Devices'] = $input;
                                $model = Devices::find()->where(['uu_id' => $input['uu_id']])->orFilterWhere(['imei_no' => $input['imei_no']])->one();
                                if(!isset($model))
                                {
                                    $model = new Devices();
                                }
                                if ($model->load($devices)) {
                                    $model->access_token = $access_token;
                                    $model->created_by = $user->id;
                                    $model->assigned_to = $user->id;
                                    if ($model->save()) {

                                        $userDevice['UserDevices']['user_id'] = $user->id;
                                        $userDevice['UserDevices']['device_id'] = $model->id;
                                        $user_device = UserDevices::findOne(['user_id'=>$user->id, 'device_id' => $model->id]);
                                        if(!isset($user_device)) {
                                            $userDevices = new UserDevices();
                                            if ($userDevices->load($userDevice)) {
                                                $userDevices->created_at =  strtotime('now');
                                                if ($userDevices->save()) {
                                                    $response['meta']['success'] = true;
                                                    $response['meta']['code'] = 200;
                                                    $response['meta']['message'] = 'Device Registered successfully';
                                                    $response['data'] = ApiParser::parseDevice($model);
                                                } else {
                                                    $error = '';
                                                    foreach ($userDevices->getErrors() as $m) {
                                                        $error = $m[0];
                                                    }
                                                    $response['meta']['success'] = false;
                                                    $response['meta']['message'] = $error;
                                                    $response['meta']['code'] = 500;
                                                }
                                            } else {
                                                $response['meta']['success'] = false;
                                                $response['meta']['message'] = 'User Device Data not load successfully!';
                                                $response['meta']['code'] = 500;
                                            }
                                        } else {
                                            $response['meta']['success'] = true;
                                            $response['meta']['code'] = 200;
                                            $response['meta']['message'] = 'Device Registered successfully';
                                            $response['data'] = ApiParser::parseDevice($model);
                                        }
                                    } else {
                                        $error = '';
                                        foreach ($model->getErrors() as $m) {
                                            $error = $m[0];
                                        }
                                        $deviceModel = Devices::find()->where(['uu_id'=>$model->uu_id])->one();
                                        if($deviceModel){
                                            $response['meta']['success'] = true;
                                            $response['meta']['message'] = $error;
                                            $response['meta']['code'] = 201;
                                            $response['data'] = ApiParser::parseDevice($deviceModel);
                                        }else{
                                            $response['meta']['success'] = false;
                                            $response['meta']['message'] = $error;
                                            $response['meta']['code'] = 500;
                                            //throw new \yii\web\HttpException(500, Yii::t('app',$error),500);
                                        }
                                    }
                                } else {
                                    $response['meta']['success'] = false;
                                    $response['meta']['message'] = 'Data not load successfully!';
                                    $response['meta']['code'] = 500;
                                    //throw new \yii\web\HttpException(500, Yii::t('app','Data not load successfully!'),500);
                                }

                            } else {
                                $response['meta']['success'] = false;
                                $response['meta']['message'] = 'Device moedl is required!';
                                $response['meta']['code'] = 500;
                                //throw new \yii\web\HttpException(500, Yii::t('app','MDP is required!'),500);
                            }
                        } else {
                            $response['meta']['success'] = false;
                            $response['meta']['message'] = 'OS version is required!';
                            $response['meta']['code'] = 500;
                            //throw new \yii\web\HttpException(500, Yii::t('app','Recv Date is required!'),500);
                        }
                    } else {
                        $response['meta']['success'] = false;
                        $response['meta']['message'] = 'imei no is required!';
                        $response['meta']['code'] = 500;
                        //throw new \yii\web\HttpException(500, Yii::t('app','Amount is required!'),500);
                    }
                } else {
                    $response['meta']['success'] = false;
                    $response['meta']['message'] = 'uu_id is required!';
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

    public function actionRegisterpush()
    {
        $input = Yii::$app->request->getBodyParams();
        $headers = Yii::$app->request->headers;
        $access_token = $headers->get('access_token');
        $version_code   = $headers->get('version_code');

        if(!ApiHelper::checkVersion($version_code)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 600;
            $response['data']['message'] = "Please download latest app from play store";
            return $response;
        }
        if (!empty($access_token)) {
            $user = Users::find()->where(['last_login_token' => $access_token])->one();
            if ($user) {
                $analytics['user_id'] = $user->id;
                $analytics['api'] = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;
                $analytics['type'] = 'reports';
                AnalyticsHelper::create($analytics);
                if (isset($input['device_id'])) {
                    if (isset($input['push_id'])) {
                        $id = $input['device_id'];
                        $model = $this->findModel($id);
                        $devices['Devices'] = $input;

                        if ($model->load($devices)) {
                            if ($model->save()) {
                                $response['meta']['success'] = true;
                                $response['meta']['code'] = 200;
                                $response['meta']['message'] = 'Push notification ID updated successfully';
                            } else {
                                $error = '';
                                foreach ($model->getErrors() as $m) {
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
                        $response['meta']['message'] = 'Push Notification ID is required!';
                        $response['meta']['code'] = 500;
                        //throw new \yii\web\HttpException(500, Yii::t('app','Amount is required!'),500);
                    }
                } else {
                    $response['meta']['success'] = false;
                    $response['meta']['message'] = 'Device ID is required!';
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
    /**
     * Finds the ArchiveReports model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Devices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Devices::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

