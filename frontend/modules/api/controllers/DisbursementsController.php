<?php

namespace frontend\modules\api\controllers;


use common\components\Helpers\ActionsHelper;
use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\GroupHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\LoanHelper;
use common\components\Helpers\StructureHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\Applications;
use common\models\Branches;
use common\models\Disbursements;
use common\models\FundRequests;
use common\models\GroupActions;
use common\models\Groups;
use common\models\Guarantors;
use common\models\Images;
use common\models\LoanActions;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\Members;
use common\models\Operations;
use common\models\search\DisbursementSearch;
use common\models\search\DisbursementsSearch;
use common\models\search\FundRequestsSearch;
use common\models\search\GroupsSearch;
use phpDocumentor\Reflection\DocBlock\Description;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;

use Yii;


class DisbursementsController extends RestController
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
                    'delete' => ['DELETE'],
                    'rejected' => ['POST']
                ],
            ],

        ];
    }

    public function actionIndex()
    {
        $params = $_GET;
        $searchModel = new DisbursementsSearch();
        $response = $searchModel->searchApi($params);
        if($response['info']['totalCount'] > 0){
            $response['data'] = ApiParser::parseDisbursements($response['data']);
            return $this->sendSuccessResponse(200,$response['data'], $response['info']);
        }else{
            return $this->sendFailedResponse(204, "Record not found");
        }

    }

    public function actionCreate()
    {
        $disbursement_details = $this->request['disbursement_details'];
        $model = new Disbursements();
        $model->attributes = $this->request;
        $model->platform = 2;
        foreach ($disbursement_details as $disbursement_detail) {
            $loan_ids[] = $disbursement_detail['loan_id'];
        }
        $loans = Loans::find()->select(['loans.*'])->where([ 'loans.deleted' => 0])->andFilterWhere(['in','id', $loan_ids])->groupBy(['loans.branch_id'])->all();
        if(count($loans) != 1)
        {
            return $this->sendFailedResponse(400, "Branch ID Not Match");
        } else {
            $model->branch_id = $loans[0]['branch_id'];
            $model->area_id = $loans[0]['area_id'];
            $model->region_id = $loans[0]['region_id'];
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($flag = $model->save()) {
                    foreach ($disbursement_details as $disbursement_detail) {
                        $loan_tranch = LoanTranches::find()->where(['loan_id' => $disbursement_detail['loan_id'], 'deleted' => 0])->andWhere(['loan_tranches.status' => 7, 'loan_tranches.disbursement_id' => 0])->one();
                        $project_flag = in_array($loan_tranch->loan->project_id,StructureHelper::trancheProjects());

                        if ($disbursement_detail['status'] == "collected") {
                            $loan_tranch->disbursement_id = $model->id;
                            $loan_tranch->date_disbursed = strtotime("now");
                            if($project_flag) {
                                $loan_tranch->status = 8;
                            } else {
                                $loan_tranch->status = 6;
                            }
                        }
                        $loan = Loans::findOne(['id' => $loan_tranch->loan_id]);
                        if(!$project_flag) {
                            $loan->status = $disbursement_detail['status'];
                            $loan->disbursed_amount = $loan->disbursed_amount + $loan_tranch->tranch_amount;
                        }

                        if($loan_tranch->tranch_no == "1") {
                            $loan->date_disbursed = strtotime("now");
                        }
                        if (!($flag = $loan->save())) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, $loan->getErrors());
                        }
                        if (!($flag = $loan_tranch->save())) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, $loan_tranch->getErrors());
                        } else {
                            if ($loan->status == "collected") {
                                if ($loan_tranch->tranch_no == "1") {
                                    if (!DisbursementHelper::GenerateSchedule($loan)) {
                                        $transaction->rollBack();
                                        return $this->sendFailedResponse(400, "Ledger Not Create");
                                    } else {
                                        FixesHelper::update_loan_expiry($loan);
                                    }
                                } else {
                                    FixesHelper::ledger_regenerate($loan);
                                }
                            } else {
                                $result =LoanHelper::absentLoan($loan_tranch);
                            }
                        }
                        ActionsHelper::updateAction('tranche',$loan_tranch->id,'disbursement');
                        //$response = ApiParser::parseDisbursement($model);
                    }
                    $transaction->commit();
                    $response['message'] = "Disbursement Done Successfully";
                    return $this->sendSuccessResponse(200, $response);
                }

            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
    }

    public function actionRejected()
    {
        $request = $this->request;
        $loan = Loans::find()->where(['id'=>$request['loan_id'], 'deleted'=>0])->one();
        if(!empty($loan)) {
            if ((GroupHelper::getGroupMembersCountByLoanId($loan->group_id) <= 3 && $loan->group->grp_type == 'GRP') || $loan->group->grp_type == 'IND') {
                if($request['reject_type'] == 'temporary')
                {
                    $loans = Loans::find()->where(['group_id' => $loan->group_id, 'deleted' => 0])->all();
                    foreach ($loans as $loan) {
                        $loan->status = 'not collected';
                        $loan->save();
                        $model = LoanTranches::findOne(['loan_id' => $loan->id, 'disbursement_id' => 0, 'status' => 4]);
                        $result = LoanHelper::absentLoan($model);

                    }
                    $response['message'] = "Group temporary rejected successfully";
                    return $this->sendSuccessResponse(200, $response);
                } else {
                    $group = Groups::find()->where(['id' => $loan->group_id, 'deleted' => 0])->one();
                    if (!empty($group)) {
                        $group->status = 'rejected';
                        $group->reject_reason = $request['rejected_reason'];
                        if ($group->save()) {
                            $loans = Loans::find()->where(['group_id' => $loan->group_id, 'deleted' => 0])->all();
                            foreach ($loans as $loan) {
                                $loan->status = 'rejected';
                                $loan->reject_reason = $request['rejected_reason'];
                                $loan->save();
                            }
                            $applications = Applications::find()->where(['group_id' => $loan->group_id, 'deleted' => 0])->all();
                            foreach ($applications as $application) {
                                $application->status = 'rejected';
                                $application->reject_reason = $request['rejected_reason'];
                                $application->save();
                            }
                            $response['message'] = "Group rejected successfully";
                            return $this->sendSuccessResponse(200, $response);
                        } else {
                            return $this->sendFailedResponse(400, $group->getErrors());
                        }
                    }
                }

            } else {
                if($request['reject_type'] == 'temporary') {
                    $loan->status = 'not collected';
                    $loan->save();
                    $model = LoanTranches::findOne(['loan_id' => $loan->id, 'disbursement_id' => 0, 'status' => 4]);
                    $result = LoanHelper::absentLoan($model);
                    $response['message'] = "Loan temporary rejected successfully";
                    return $this->sendSuccessResponse(200, $response);
                }
                else {
                    $loan->status = 'rejected';
                    $loan->reject_reason = $request['rejected_reason'];
                    $loan->save();
                    $application = Applications::find()->where(['id' => $loan->application_id, 'deleted' => 0])->one();
                    $application->status = 'rejected';
                    $application->reject_reason = $request['rejected_reason'];
                    $application->save();

                    $response['message'] = "Loan rejected successfully";
                    return $this->sendSuccessResponse(200, $response);
                }

            }
        }
        else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = $this->request;
        if($model->save())
        {
            $response = ApiParser::parseDisbursement($model);
            return $this->sendSuccessResponse(200, $response);
        }
        else {
            return $this->sendFailedResponse(400, $model->getErrors());
        }
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $response = ApiParser::parseDisbursement($model);
        return $this->sendSuccessResponse(200, $response);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted = 1;
        if ($model->save()) {

            $loans = Loans::find()->where(['disbursement_id' => $model->id])->all();
            foreach ($loans as $loan) {
                $loan->disbursement_id = 0;
                $loan->save();
            }
            $response = ApiParser::parseDisbursement($model);
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(400,"Enable to delete record.");
        }
    }

    protected function findModel($id)
    {
        if (($model = Disbursements::findOne(['id' => $id,'deleted' =>0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }
}