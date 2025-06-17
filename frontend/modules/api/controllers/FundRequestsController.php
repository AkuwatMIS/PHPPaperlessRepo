<?php

namespace frontend\modules\api\controllers;


use common\components\Helpers\ActionsHelper;
use common\components\Helpers\FundRequestHelper;
use common\components\Helpers\GroupHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\Applications;
use common\models\Branches;
use common\models\FundRequests;
use common\models\FundRequestsDetails;
use common\models\GroupActions;
use common\models\Groups;
use common\models\Guarantors;
use common\models\Images;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\Members;
use common\models\search\FundRequestsSearch;
use common\models\search\GroupsSearch;
use common\models\Users;
use phpDocumentor\Reflection\DocBlock\Description;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;

use Yii;


class FundRequestsController extends RestController
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
                    'delete' => ['DELETE']
                ],
            ],

        ];
    }

    public function actionIndex()
    {
        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));

        $searchModel = new FundRequestsSearch();
        $response_data = $searchModel->searchApi($params);
        if($response_data['info']['totalCount'] > 0){
            $response_data['data'] = ApiParser::parseFundRequests($response_data['data']);
            $data = [];
            foreach ($response_data['data'] as $fund_request_data) {
                $fund_request_data['details'] =  FundRequestHelper::getFundRequestDetails($fund_request_data['id']);
                $data[] = $fund_request_data;

            }
            $response = [
                'funds' => $data,
                'page' => $response_data['info']['page'],
                'size' => $response_data['info']['size'],
                'total_count' => $response_data['info']['totalCount'],
                'total_records' => $response_data['info']['totalRecords']
                //'total_count' => 5
            ];
            return $this->sendSuccessResponse(200,$response);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionCreate()
    {
        if($this->request['requested_amount'] == 0 || !isset($this->request['requested_amount'])) {
            return $this->sendFailedResponse(400, 'Requested Amount cannot be zero');
        } else {
            $model = new FundRequests();
            $model->branch_id = $this->request['branch_id'];
            $model->requested_amount = $this->request['requested_amount'];
            $model->total_loans = $this->request['total_loans'];
            $model->platform = 2;

            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($flag = $model->save()) {

                    $loan_tranches = LoanTranches::find()
                        ->joinWith('loan')
                        //->join('inner join','loan_tranches_actions','loan_tranches_actions.parent_id=loan_tranches.id')
                        //->where(['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>1])
                        //->where(['>=','loan_tranches_actions.expiry_date', date('Y-m-d H:i:s')])
                        ->where(['in','loans.status' ,["pending","collected"]])
                        ->andWhere(['loan_tranches.status' => 4, 'loans.branch_id' => $this->request['branch_id'], 'loan_tranches.fund_request_id' => 0,'loan_tranches.disbursement_id' => 0])->all();

                    foreach ($loan_tranches as $loan_tranch) {
                        $update_query = "update loan_tranches set fund_request_id = '" . $model->id . "'  where id = '" . $loan_tranch->id . "'";
                        Yii::$app->db->createCommand($update_query)->execute();
                    }

                    foreach ($this->request['details'] as $loan) {
                        $fund_request_detail_model = new FundRequestsDetails();
                        $fund_request_detail_model->branch_id = $loan['branch_id'];
                        $fund_request_detail_model->project_id = $loan['project_id'];
                        $fund_request_detail_model->total_requested_amount = $loan['total_requested_amount'];
                        $fund_request_detail_model->total_loans = $loan['total_loans'];
                        $fund_request_detail_model->fund_request_id = $model->id;
                        if (!($flag = $fund_request_detail_model->save())) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, $fund_request_detail_model->getErrors());
                        }
                    }
                }
                if ($flag) {
                    $transaction->commit();
                    $loan_tranches = LoanTranches::find()->where(['fund_request_id' => $model->id, 'deleted' => 0])->all();
                    foreach ($loan_tranches as $loan_tranch) {
                        ActionsHelper::updateAction('tranche',$loan_tranch->id,'fund_request');
                    }

                    $response = ApiParser::parseFundRequest($model);
                    $response['details'] = FundRequestHelper::getFundRequestDetails($model->id);
                    return $this->sendSuccessResponse(200, $response);
                } else {
                    $transaction->rollBack();
                    return $this->sendFailedResponse(400, $model->getErrors());
                }

            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
    }

    public function actionApprove($id,$status)
    {
        $model = $this->findModel($id);
        $user = Users::findIdentityByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        //$model->attributes = $this->request;
        //$model->status = 'approved';
        $model->status = $status;
        $model->approved_by = $user->id;
        $model->approved_on = strtotime('now');
        if($model->save())
        {
            if($model->status == 'rejected')
            {
                $tranches = LoanTranches::find()
                    ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                    ->andFilterWhere(['loan_tranches.fund_request_id' => $model->id])
                    ->all();
                foreach ($tranches as $t) {
                    $t->fund_request_id = 0;
                    $t->status = 3;
                    $t->save();
                    $actions = LoanTranchesActions::find()->where(['parent_id' => $t->id])->andWhere(['in', 'action', ['fund_request', 'cheque_printing']])->all();
                    foreach ($actions as $act) {
                        $act->status = 0;
                        $act->save();
                    }
                }
            }
            //$response = ApiParser::parseFundRequest($model);
            return $this->sendSuccessResponse(200, array('message'=>'Fund request approved successfully'));
        }else {
            return $this->sendFailedResponse(400, $model->getErrors());
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = $this->request;
        if($model->save())
        {
            $response = ApiParser::parseFundRequest($model);
            return $this->sendSuccessResponse(200, $response);
        }
        else {
            return $this->sendFailedResponse(400, $model->getErrors());
        }
    }

    public function actionGetdetails($id)
    {
        $model = $this->findModel($id);
        $response = ApiParser::parseFundRequest($model);
        $response['details'] = FundRequestHelper::getFundRequestDetails($model->id);
        return $this->sendSuccessResponse(200, $response);
    }

    public function actionGetsummary($branch_id)
    {
        $data = FundRequestHelper::getFundRequest($branch_id);
        return $this->sendSuccessResponse(200, $data);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $response = ApiParser::parseFundRequest($model);
        return $this->sendSuccessResponse(200, $response);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted = 1;
        if ($model->save()) {

            $loans = Loans::find()->where(['fund_request_id' => $model->id])->all();
            foreach ($loans as $loan) {
                $loan->fund_request_id = 0;
                $loan->save();
            }
            $response = ApiParser::parseFundRequest($model);
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(400,"Enable to delete record.");
        }
    }

    protected function findModel($id)
    {
        if (($model = FundRequests::findOne(['id' => $id,'deleted' =>0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }
}