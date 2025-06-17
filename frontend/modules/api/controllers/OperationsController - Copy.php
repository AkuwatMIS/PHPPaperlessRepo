<?php

namespace frontend\modules\api\controllers;


use common\components\Helpers\FundRequestHelper;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\OperationHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;

use common\models\FundRequests;
use common\models\FundRequestsDetails;
use common\models\GroupActions;

use common\models\LoanActions;
use common\models\Loans;

use common\models\Operations;
use common\models\search\FundRequestsSearch;

use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;

use Yii;


class OperationsController extends RestController
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
                    'index' => ['GET', 'POST'],
                    'create' => ['POST'],
                    'update' => ['PUT'],
                    'view' => ['GET'],
                    'getdetails' => ['GET'],
                    'getsummary' => ['GET'],
                    'delete' => ['DELETE'],
                    'posttakaful' => ['POST']
                ],
            ],

        ];
    }

    public function actionIndex()
    {
        $params = $_GET;
        $searchModel = new FundRequestsSearch();
        $response = $searchModel->searchApi($params);
        if($response['info']['totalCount'] > 0){
            $response['data'] = ApiParser::parseFundRequests($response['data']);
            $data = [];
            foreach ($response['data'] as $fund_request_data) {
                $fund_request_data['details'] =  FundRequestHelper::getFundRequestDetails($fund_request_data['id']);
                $data[] = $fund_request_data;

            }
            return $this->sendSuccessResponse(200,$data,$response['info']);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionPosttakaful()
    {
        $request = $this->request;
        $action_model = LoanActions::findOne(['parent_id' => $request['loan_id'], 'action' => 'takaful','status' => 1]);
        if(!isset($action_model) && empty($action_model)) {
            if ($request['status'] == 'collected') {
                $loan = Loans::find()->where(['id' => $this->request['loan_id']])->one();
                $takaf_save=OperationHelper::saveOperations($loan,'takaf',$request['amount'],substr(round(microtime(true)), 2),strtotime('now'),2);
                /*$model = new Operations();
                $model->platform = 2;


                $model->application_id = $loan->application_id;
                $model->loan_id = $request['loan_id'];
                $model->operation_type_id = 2;
                $model->credit = $request['amount'];
                $model->receipt_no = substr(round(microtime(true)), 2);
                $model->receive_date = strtotime('now');
                $model->branch_id = $loan->branch_id;
                $model->team_id = $loan->team_id;
                $model->field_id = $loan->field_id;
                $model->project_id = $loan->project_id;
                $model->region_id = $loan->region_id;
                $model->area_id = $loan->area_id;*/

                if($takaf_save == 1)
                {
                    $action_model = LoanActions::findOne(['parent_id' => $loan->id, 'action' => 'takaful']);
                    $action_model->status = 1;
                    $action_model->expiry_date = strtotime('+3 months', strtotime(date('Y-m-d H:i:s')));
                    $action_model->save();

                    $loans = Loans::find()->select('id')->where(['group_id' => $loan->group_id])->andWhere(['deleted' => 0])->all();
                    $flag = true;
                    foreach ($loans as $loan_data)
                    {
                        $action = LoanActions::findOne(['parent_id' => $loan_data->id, 'action' => 'takaful']);
                        if(!isset($action))
                        {
                            $flag = false;
                            break;
                        }
                    }

                    if($flag)
                    {
                        $action_model = GroupActions::findOne(['parent_id' => $loan->group_id, 'action' => 'takaful']);
                        $action_model->status = 1;
                        $action_model->expiry_date = strtotime('+3 months', strtotime(date('Y-m-d H:i:s')));
                        $action_model->save();
                    }
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
        if (($model = Operations::findOne(['id' => $id,'deleted' =>0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }
}