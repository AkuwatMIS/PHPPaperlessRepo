<?php

namespace frontend\modules\api\controllers;


use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\LogsHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\Images;
use common\models\Loans;
use common\models\Members;
use common\models\MembersAddress;
use common\models\MembersEmail;
use common\models\MembersPhone;
use common\models\Recoveries;
use common\models\search\MembersSearch;
use common\models\Users;
use frontend\modules\test\api\models\Member;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\models\Employee;
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;

use Yii;


class RecoveriesController extends RestController
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
                    JsonHelper::asJson($this->sendFailedResponse('401','You are not allowed to perform this action.'));
                },
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
            ],
            'verbs' => [
                'class' => Verbcheck::className(),
                'actions' => [
                    'get-loan-recovery' => ['GET'],
                    'post' => ['POST']
                ],
            ],

        ];
    }

    public function actionPost(){
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');
        $post_token = $headers->get('x-post-token');
        $user = Users::findIdentityByAccessToken($access_token);

        if(!isset($post_token))
        {
            return $this->sendFailedResponse(400,'Post Token is required');
        } else if($post_token != $user->post_token) {
            return $this->sendFailedResponse(400,'Invalid Post Token');
        } else {
            $model = new Recoveries();
            $model->platform = 2;
            $model->attributes = $this->request;
            $model->receive_date = strtotime("now");
            $model->receipt_no = substr(round(microtime(true)), 2);

            if ($model->save()) {
                $msg = SmsHelper::getRecoveryText($model);
                $mobile = isset($model->application->member->membersMobile->phone) ? $model->application->member->membersMobile->phone : '';
                $sms = SmsHelper::SendUrdusms($mobile, $msg);
                if ($sms->corpsms[0]->type == 'Success') {
                    SmsHelper::SmsLogs('recovery', $model);
                    $response['message'] = "Recovery posted successfully";
                    $response['recovery'] = ApiParser::parseRecovery($model);
                    return $this->sendSuccessResponse(201, $response);
                } else {
                    return $this->sendFailedResponse(500, 'Internal server error');
                }
            } else {
                return $this->sendFailedResponse(400, $model->errors);
            }
        }
    }

    public function actionGetLoanRecovery()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');
        $user = Users::findIdentityByAccessToken($access_token);

        if(isset($_GET['key']) && isset($_GET['value'])){
            $cnic = $sanction_no = '';
            $key = $_GET['key'];
            $value = $_GET['value'];
            $loans = new Loans();
            if($key == 'cnic'){
                $cnic = $value;
                $loans = Loans::find()
                    ->joinWith('application')
                    ->joinWith('application.member')
                    ->where(['members.cnic'=>$cnic,'loans.status'=>'collected'])
                    ->all();
            }else if ($key == 'sanction_no'){
                $sanction_no = $value;
                $loans = Loans::find()->where(['sanction_no'=>$sanction_no,'loans.status'=>'collected'])
                    ->all();
            }else{
                return $this->sendFailedResponse(400, "Invalid data format");
            }

            $user->post_token = md5(uniqid());
            $user->created_date = strtotime('now');
            $user->save();

            $response = [];
            if(!empty($loans)){
                $response['data']['message'] = "Get Borrower Ledger Detail";
                $response['data']['post_token'] =  $user->post_token;
                $response['data']['details'] = ApiParser::parseLoanRecovery($loans);
                $response['info'] = '';
                return $this->sendSuccessResponse(200,$response['data'],$response['info']);
            }
            else{
                return $this->sendFailedResponse(204, "Record not found");
            }
        }else{
            return $this->sendFailedResponse(400, "Key and value cannot be empty.");
        }
    }

    protected function findModel($id)
    {
        if (($model = Members::findOne(['id' => $id,'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }

}