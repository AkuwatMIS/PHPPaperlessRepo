<?php

namespace frontend\modules\api\controllers;

use common\components\Helpers\ActionsHelper;
use common\components\Helpers\GroupHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\LoanHelper;
use common\components\Helpers\LogsHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\Applications;
use common\models\Branches;
use common\models\GroupActions;
use common\models\Groups;
use common\models\Guarantors;
use common\models\Images;
use common\models\LoanActions;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\search\GroupsSearch;
use common\models\search\LoansSearch;
use phpDocumentor\Reflection\DocBlock\Description;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;

use Yii;


class TakafulController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => [],
                    'callback' => []
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
                        'approvedtakaful' => ['GET'],
                    ],
                ],

            ];
    }


    public function actionTakafInfo()
    {
        if (isset($_GET['key']) && isset($_GET['value'])) {
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));

            $cnic = $sanction_no = '';
            $key = $_GET['key'];
            $value = $_GET['value'];
            if ($key == 'cnic') {
                $params['cnic'] = $value;
                $searchModel = new LoansSearch();
                $response = $searchModel->searchForTakaful($params);
            } else if ($key == 'sanction_no') {
                $params['sanction_no'] = $value;
                $searchModel = new LoansSearch();
                $response = $searchModel->searchForTakaful($params);
            } else {
                return $this->sendFailedResponse(400, "Invalid data format");
            }
            if ($response['info']['totalCount'] > 0) {
                $response['data'] = ApiParser::parseTakafulInfo($response['data']);
                $action_model = LoanActions::findOne(['parent_id' => $response['data']['id'], 'action' => 'takaful', 'status' => 1]);
                if(!isset($action_model) && empty($action_model)) {
                    return $this->sendSuccessResponse(200, $response['data']);
                } else {
                    return $this->sendFailedResponse(400, "Takaful Already Taken.");
                }
            } else {
                return $this->sendFailedResponse(204, "Record not found");
            }
        } else {
            return $this->sendFailedResponse(400, "Key and value cannot be empty.");
        }
    }

    public function actionPostTakaful()
    {
        $request = $this->request;
        $action_model = LoanActions::findOne(['parent_id' => $request['loan_id'], 'action' => 'takaful','status' => 1]);
        if(!isset($action_model) && empty($action_model)) {
            if ($request['status'] == 'collected') {
                $loan = Loans::find()->where(['id' => $this->request['loan_id']])->one();
                $takaf_save=OperationHelper::saveOperations($loan,'takaf',$request['amount'],substr(round(microtime(true)), 2),strtotime('now'),2);

                if($takaf_save == 1)
                {
                    ActionsHelper::updateAction('loan',$loan->id,'takaful');
                    $operation_model = Operations::findOne(['application_id' => $loan->application_id,'operation_type_id'=>2]);
                    $msg = SmsHelper::getTakafulText($operation_model);
                    $mobile = isset($loan->application->member->membersMobile->phone) ? $loan->application->member->membersMobile->phone : '';

                    $sms = SmsHelper::SendUrdusms($mobile, $msg);
                    if ($sms->corpsms[0]->type == 'Success') {
                        SmsHelper::SmsLogs('operation', $operation_model);
                        $response['message'] = "Takaful posted successfully";
                        return $this->sendSuccessResponse(201, $response);
                    } else {
                        return $this->sendFailedResponse(500, 'Internal server error SMS not send.');
                    }
                } else
                {
                    return $this->sendFailedResponse(400, $takaf_save);
                }
            } else if ($request['status'] == 'skipped') {
                if (isset($request['image'])) {
                    $parent_id = $request['loan_id'];
                    $parent_type = 'loans';
                    $image_type = 'stamp_paper';
                    $image_name = 'stamp_paper' . '_' . rand(111111, 999999) . '.png';
                    $base_code = $request['image'];
                    ImageHelper::imageUpload($parent_id, $parent_type, $image_type, $image_name, $base_code);
                    $action_model = LoanActions::findOne(['parent_id' => $request['loan_id'], 'action' => 'takaful']);
                    $action_model->status = 1;
                    $action_model->expiry_date = strtotime('+3 months', strtotime(date('Y-m-d H:i:s')));
                    $action_model->save();
                    $response['message'] = "Loan skipped successfully";
                    return $this->sendSuccessResponse(201, $response);
                } else {
                    return $this->sendFailedResponse(400, "Image not saved.");
                }
            } else {
                return $this->sendFailedResponse(400, "Invalid data format");
            }
        } else {
            return $this->sendFailedResponse(400, "Takaful Already Exist.");
        }
    }

    protected function findModel($id)
    {
        if (($model = Loans::findOne(['id' => $id, 'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204, "Invalid Record requested");
        }
    }
}