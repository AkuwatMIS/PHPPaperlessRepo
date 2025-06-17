<?php

namespace frontend\modules\test\api\controllers;


use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\GroupHelper;
use common\components\Helpers\ImageHelper;
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
use common\models\Members;
use common\models\Operations;
use common\models\search\DisbursementSearch;
use common\models\search\DisbursementsSearch;
use common\models\search\FundRequestsSearch;
use common\models\search\GroupsSearch;
use phpDocumentor\Reflection\DocBlock\Description;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;

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
                    return print_r(json_encode($this->sendFailedResponse('401','You are not allowed to perform this action.')));
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

        foreach ($disbursement_details as $disbursement_detail) {
            $loan_ids[] = $disbursement_detail['loan_id'];
        }
        $loan = Loans::find()->select(['*'])->where([ 'deleted' => 0])->andFilterWhere(['in','id', $loan_ids,])->groupBy(['branch_id'])->all();
        if(count($loan) != 1)
        {
            return $this->sendFailedResponse(400, "Branch ID Not Match");
        } else {
            $model->branch_id = $loan[0]['branch_id'];
            $model->area_id = $loan[0]['area_id'];
            $model->region_id = $loan[0]['region_id'];
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($flag = $model->save()) {
                    foreach ($disbursement_details as $disbursement_detail) {
                        $loan = Loans::find()->where(['id' => $disbursement_detail['loan_id'], 'deleted' => 0])->one();
                        if ($disbursement_detail['status'] == "collected") {
                            $loan->disbursement_id = $model->id;
                            $loan->date_disbursed = strtotime("now");
                        }
                        $loan->status = $disbursement_detail['status'];
                        if (!($flag = $loan->save())) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, $loan->getErrors());
                        } else {
                            if ($loan->status == "collected") {
                                if (!DisbursementHelper::GenerateSchedule($loan)) {
                                    $transaction->rollBack();
                                    return $this->sendFailedResponse(400, "Ledger Not Create");
                                }
                            }
                        }
                        $action_model = LoanActions::findOne(['parent_id' => $loan->id, 'action' => 'disbursement']);
                        $action_model->status = 1;
                        $action_model->expiry_date = strtotime('+10 days',strtotime( date('Y-m-d H:i:s')));
                        $action_model->save();

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
        $group = Groups::find()->where(['id'=>$request['group_id'], 'deleted'=>0])->one();
        if(!empty($group)){
            $group->status = 'rejected';
            $group->reject_reason = $request['rejected_reason'];
            if($group->save()){
                $loans = Loans::find()->where(['group_id'=>$request['group_id'], 'deleted'=>0])->all();
                foreach ($loans as $loan){
                    $loan->status = 'rejected';
                    $loan->reject_reason = $request['rejected_reason'];
                    $loan->save();
                }
                $applications = Applications::find()->where(['group_id'=>$request['group_id'], 'deleted'=>0])->all();
                foreach ($applications as $application){
                    $application->status = 'rejected';
                    $application->reject_reason = $request['rejected_reason'];
                    $application->save();
                }
                $response['message'] = "Group rejected successfully";
                return $this->sendSuccessResponse(200, $response);
            }else {
                return $this->sendFailedResponse(400, $group->getErrors());
            }
        }else {
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