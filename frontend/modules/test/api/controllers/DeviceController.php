<?php

namespace frontend\modules\test\api\controllers;


use common\components\Helpers\FundRequestHelper;
use common\components\Helpers\GroupHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\Applications;
use common\models\Branches;
use common\models\Devices;
use common\models\FundRequests;
use common\models\FundRequestsDetails;
use common\models\GroupActions;
use common\models\Groups;
use common\models\Guarantors;
use common\models\Images;
use common\models\Loans;
use common\models\Members;
use common\models\search\FundRequestsSearch;
use common\models\search\GroupsSearch;
use common\models\UserDevices;
use common\models\Users;
use phpDocumentor\Reflection\DocBlock\Description;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;

use Yii;
use yii\web\NotFoundHttpException;


class DeviceController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

           'apiauth' => [
               'class' => Apiauth::className(),
               'exclude' => [],
               'callback'=>[]
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
                    'registerpush' => ['POST'],
                    'register' => ['POST'],
                ],
            ],

        ];
    }

    public function actionRegister()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');
        $user = Users::findIdentityByAccessToken($access_token);

        $flag = true;
        $uu_id = $this->request['uu_id'];
        $imei_no = $this->request['imei_no'];
        $os_version = $this->request['os_version'];
        $device_model = $this->request['device_model'];

        $device = Devices::find()->where(['uu_id' => $uu_id])->orFilterWhere(['imei_no' => $imei_no])->one();
        if(isset($device))
        {
            $device->uu_id = $uu_id;
            $device->imei_no = $imei_no;
            $device->os_version = $os_version;
            $device->device_model = $device_model;
            $device->access_token = $access_token;
            if(!$flag = $device->save())
            {
                return $this->sendFailedResponse(400, $device->getErrors());
            }
        } else {
            $device = new Devices();
            $device->uu_id = $uu_id;
            $device->imei_no = $imei_no;
            $device->os_version = $os_version;
            $device->device_model = $device_model;
            $device->access_token = $access_token;
            if(!$flag = $device->save())
            {
                return $this->sendFailedResponse(400, $device->getErrors());
            }
        }

        $user_device = UserDevices::findOne(['user_id'=>$user->id, 'device_id' => $device->id]);
        if(!isset($user_device))
        {
            $user_device = new UserDevices();
            $user_device->user_id = $user->id;
            $user_device->device_id = $device->id;
            $user_device->created_at = strtotime('now');
            $flag = $user_device->save();
        }

        if($flag) {
            $response = ApiParser::parseDevice($device);
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(400, $user_device->getErrors());
        }
    }

    public function actionRegisterpush()
    {
        if(isset($this->request['device_id']))
        {
            if(isset($this->request['push_id']))
            {
                $id = $this->request['device_id'];
                $model = $this->findModel($id);
                $devices['Devices'] = $this->request;

                if($model->load($devices)) {
                    if ($model->save()) {
                        $response["message"] = 'Push Notification Id Registered';
                        return $this->sendSuccessResponse(200, $response);
                    }
                } else {
                    return $this->sendFailedResponse(400, $model->getErrors());
                }
            } else {
                return $this->sendFailedResponse(400, "Push ID is Required.");
            }
        } else {
            return $this->sendFailedResponse(400, "Device ID is Required.");
        }

    }

    protected function findModel($id)
    {
        if (($model = Devices::findOne(['id' => $id,'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204, "Invalid Record requested");
        }
    }

}