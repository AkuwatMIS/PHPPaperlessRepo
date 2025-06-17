<?php

namespace frontend\modules\api\controllers;

use common\components\Helpers\GroupHelper;
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


class LoansController extends RestController
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
                        'ledger' => ['GET'],
                        'search' => ['GET'],
                        'disbursed' => ['GET'],
                        'memberattendance' => ['PUT'],
                        'delete' => ['DELETE'],
                        'approvedtakaful' => ['GET'],
                        'pendingtakaful' => ['GET']
                    ],
                ],

            ];
    }

    public function actionIndex()
    {
        $params = $_GET;
        $searchModel = new LoansSearch();
        $response = $searchModel->searchApi($params);
        if ($response['info']['totalCount'] > 0) {
            $response['data'] = ApiParser::parseLoans($response['data']);
            $data = [];
            foreach ($response['data'] as $loan_data) {
                $loan_data['logs'] = ApiParser::parseLogs(LogsHelper::getLogs("loans", $loan_data['id']));
                $data[] = $loan_data;
            }
            return $this->sendSuccessResponse(200, $data, $response['info']);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionSearch($key, $value)
    {
        $cnic = $sanction_no = $group_no = '';
        if ($key == 'cnic') {
            $cnic = $value;
        } else if ($key == 'sanction_no') {
            $sanction_no = $value;
        } else if ($key == 'group_no') {
            $group_no = $value;
        }
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));

        $params['sanction_no'] = isset($sanction_no) ? $sanction_no : '';
        $params['cnic'] = isset($cnic) ? $cnic : '';
        $params['grp_no'] = isset($group_no) ? $group_no : '';

        $searchModel = new LoansSearch();
        $search_member = $searchModel->searchGlobal($params);
        if ($search_member['info']['totalCount'] > 0) {
            $response['message'] = "Get Members Details";
            $response['members'] = ApiParser::parseLoansSearchResult($search_member['data']);
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionMemberattendance()
    {
        $model = $this->findModel($this->request['loan_id']);
        $model->attendance_status = $this->request['attendance_status'];
        if ($model->save()) {
            $response['message'] = 'Attendance Successfully Done.';
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionProcessed()
    {
        $params = $_GET;
        $params['status'] = 'processed';
        $params['order'] = 'updated_at desc';
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));

        $searchModel = new LoansSearch();
        $response = $searchModel->searchApiProcessed($params);
        if ($response['info']['totalCount'] > 0) {
            $response['data'] = ApiParser::parseLoans($response['data']);
            $data = [];
            foreach ($response['data'] as $loan_data) {
                $loan_info = LoanHelper::getInfoByLoan($loan_data['id']);
                $data['members'][] = $loan_info;
            }
            return $this->sendSuccessResponse(200, $data, $response['info']);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionPendingtakaful()
    {
        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));

        $searchModel = new LoansSearch();
        $response_data = $searchModel->searchForTakaful($params);
        if ($response_data['info']['totalCount'] > 0) {
            $response = [
                'loans' =>  ApiParser::parseTakafulInfoList($response_data['data']),
                'page' => $response_data['info']['page'],
                'size' => $response_data['info']['size'],
                'total_count' => $response_data['info']['totalCount'],
                'total_records' => $response_data['info']['totalRecords']
            ];
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionApprovedtakaful()
    {
        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));

        $searchModel = new LoansSearch();
        $response_data = $searchModel->searchForApprovedTakaful($params);
        if ($response_data['info']['totalCount'] > 0) {
            $response = [
                'loans' =>  ApiParser::parseTakafulInfoList($response_data['data']),
                'page' => $response_data['info']['page'],
                'size' => $response_data['info']['size'],
                'total_count' => $response_data['info']['totalCount'],
                'total_records' => $response_data['info']['totalRecords']
            ];
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionTakafulinfo()
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

    public function actionDisbursed()
    {
        $params = $_GET;
        $params['status'] = 'collected';
        $params['order'] = 'updated_at desc';
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));

        $searchModel = new LoansSearch();
        $response = $searchModel->searchApiDisbursed($params);
        if ($response['info']['totalCount'] > 0) {
            $response['data'] = ApiParser::parseLoans($response['data']);
            $data = [];
            foreach ($response['data'] as $loan_data) {
                $loan_info = LoanHelper::getDisbursedInfoByLoan($loan_data['id']);
                $data['members'][] = $loan_info;
            }
            return $this->sendSuccessResponse(200, $data, $response['info']);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionCreateprocessed()
    {
        $disbursement_details = $this->request['disbursement_details'];
        $save = true;
        foreach ($disbursement_details as $disbursement_detail) {
            $model = $this->findModel($disbursement_detail['loan_id']);
            $model->status = $disbursement_detail['status'];
            if (!$model->save()) {
                $save = false;
            }
        }
        if ($save) {
            $response['message'] = 'Record Processed Successfully.';
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(400, $model->errors);
        }
    }

    public function actionApproveloan()
    {
        if (isset($this->request['application_id']) && isset($this->request['loan_amount'])) {
            $model = new Loans();
            $model->attributes = $this->request;
            $model->platform = 2;

            $application = Applications::find()->where(['id' => $model->application_id, 'deleted' => 0, 'status' => 'approved'])->one();
            $loan = Loans::find()->where(['application_id' => $model->application_id, 'deleted' => 0])->andWhere(['!=','status' , 'collected'])->one();
            if (!isset($application)) {
                return $this->sendFailedResponse(400, "Application not exist.");
            } else if (isset($loan)) {
                return $this->sendFailedResponse(400, "Loan already exist.");
            } else {
                $model->validateLoanAmount($application);
                if (!empty($model->getErrors())) {
                    return $this->sendFailedResponse(400, $model->getErrors());
                }
                if (isset($application->project_table) && !empty($application->project_table)) {
                    $model->project_table = $application->project_table;
                }
                $model->status = 'pending';
                $model->balance = $model->loan_amount;
                //$model->status = $application->status;
                $model->project_id = $application->project_id;
                $model->activity_id = $application->activity_id;
                $model->product_id = $application->product_id;
                $model->group_id = $application->group_id;
                $model->region_id = $application->region_id;
                $model->area_id = $application->area_id;
                $model->branch_id = $application->branch_id;
                $model->team_id = isset($application->team_id) ? $application->team_id : '1';
                $model->field_id = $application->field_id;
                $model->setLoaninfo();
                if ($model->save()) {

                    $tranch_model = new LoanTranches();
                    $tranch_model->platform = 2;
                    $tranch_model->tranch_no = 1;
                    $tranch_model->loan_id = $model->id;
                    $tranch_model->tranch_amount = $model->loan_amount;
                    $tranch_model->status = 1;
                    if(!$tranch_model->save()) {
                        return $this->sendFailedResponse(400, $tranch_model->getErrors());
                    }

                    $action_model = new LoanActions();
                    $action_model->parent_id = $model->id;
                    $action_model->user_id = $model->created_by;
                    $action_model->action = "loan_approved";
                    $action_model->status = 1;
                    $action_model->expiry_date = strtotime('+3 months', strtotime(date('Y-m-d H:i:s')));
                    $action_model->save();

                    $action_model = new LoanActions();
                    $action_model->parent_id = $model->id;
                    $action_model->user_id = $model->created_by;
                    $action_model->action = "lac";
                    $action_model->save();

                    $response = ApiParser::parseLoan($model);
                    return $this->sendSuccessResponse(201, $response);
                } else {
                    return $this->sendFailedResponse(400, $model->errors);
                }
            }
        } else {
            return $this->sendFailedResponse(400, "Application Id, Loan Amount, Inst Amount, Inst Months, Inst Type are required fields.");
        }
    }

    public function actionCreateloan()
    {
        $loan_ids = $this->request['loans'];
        $group_id = 0;
        foreach ($loan_ids as $loan_id)
        {
            $model = Loans::find()->where(['id'=>$loan_id])->one();
            $serial_no = 0;
            $group_id = $model->group_id;
            $funding_line = isset($model->project->funding_line) ? $model->project->funding_line : '';
            $branch_code = isset($model->branch->code) ? $model->branch->code : '';
            $loan = Loans::find()->select(['br_serial'])->where(['branch_id' => $model->branch_id])->orderBy('br_serial' . ' DESC')->one();
            if (isset($loan)) {
                $serial_no = $loan->br_serial;
            }
            $model->sanction_no = $branch_code . '-' . $funding_line . '-' . str_pad($serial_no + 1, 5, '0', STR_PAD_LEFT);
            $model->br_serial = $serial_no + 1;
            if ($flag = $model->save()) {

                $action_model = LoanActions::findOne(['parent_id' => $model->id, 'action' => 'lac']);
                $action_model->status = 1;
                $action_model->expiry_date = strtotime('+3 months', strtotime(date('Y-m-d H:i:s')));
                $action_model->save();

                $action_model = new LoanActions();
                $action_model->parent_id = $model->id;
                $action_model->user_id = $model->created_by;
                $action_model->action = "cheque_printing";
                $action_model->save();

                $action_model = new LoanActions();
                $action_model->parent_id = $model->id;
                $action_model->user_id = $model->created_by;
                $action_model->action = "takaful";
                $action_model->save();

                $action_model = new LoanActions();
                $action_model->parent_id = $model->id;
                $action_model->user_id = $model->created_by;
                $action_model->action = "disbursement";
                $action_model->save();

                $count_application = Applications::find()->where(['group_id'=>$model->group_id])->count();
                $count_loans = Loans::find()->where(['group_id'=>$model->group_id])->count();
                if($count_application == $count_loans){
                    $group = Groups::findOne(['id' => $model->group_id]);
                    $group->is_locked = 1;
                    $group->status = 'approved';
                    $group->save();
                }
            }
        }
        if($flag) {
            $action_model = GroupActions::findOne(['parent_id' => $group_id, 'action' => 'lac']);
            $action_model->status = 1;
            $action_model->expiry_date = strtotime('+3 months', strtotime(date('Y-m-d H:i:s')));
            $action_model->save();
            return $this->sendSuccessResponse(201, array('message'=>'Sanction No applied successfully'));
        } else{
            return $this->sendFailedResponse(400, 'Sanction No not applied.');
        }
    }

    public function actionRejected()
    {
        if (isset($this->request['application_id']) && isset($this->request['reject_reason'])) {

            $application = Applications::find()->where(['id' => $this->request['application_id'], 'deleted' => 0, 'status' => 'approved'])->one();
            if (!isset($application)) {
                return $this->sendFailedResponse(400, "Application not exist.");
            } else {
                $application->status = 'rejected';
                $application->reject_reason = $this->request['reject_reason'];
                $application->comments = isset($this->request['comments']) ? $this->request['comments'] : '';
                if ($application->save()) {
                    $id = $application->group_id;
                    $model = $this->findModel($id);
                    $model->deleted = 1;
                    if ($model->save()) {

                        $group_members = GroupHelper::getGroupMembers($id);
                        $group_gurantors = GroupHelper::getGaurantors($id);
                        $applications = Applications::find()->where(['group_id' => $model->id])->all();
                        foreach ($applications as $application) {
                            $application->group_id = 0;
                            $application->save();
                        }

                        if ($model->grp_type == "IND") {
                            $guarantors = Guarantors::find()->where(['group_id' => $model->id])->all();
                            foreach ($guarantors as $guarantor) {
                                $guarantor->deleted = 1;
                                $guarantor->save();
                            }
                        }
                        $response = ApiParser::parseGroup($model);
                        $response['members'] = $group_members;
                        $response['guarantors'] = $group_gurantors;
                        return $this->sendSuccessResponse(200, $response);
                    } else {
                        return $this->sendFailedResponse(400, "Enable to delete record.");
                    }
                } else {
                    return $this->sendFailedResponse(400, "Application not save successfully.");
                }
            }
        } else {
            return $this->sendFailedResponse(400, "Application Id is required fields.");
        }
    }


    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (isset($this->request['loan_amount'])) {
            $model->loan_amount = $this->request['loan_amount'];
        }
        if (isset($this->request['inst_amnt'])) {
            $model->inst_amnt = $this->request['inst_amnt'];
        }
        if (isset($this->request['inst_type'])) {
            $model->inst_type = $this->request['inst_type'];
        }
        if (isset($this->request['inst_months'])) {
            $model->inst_months = $this->request['inst_months'];
        }
        if ($model->save()) {
            $response = ApiParser::parseLoan($model);
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(400, $model->errors);
        }
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $response = ApiParser::parseLoan($model);
        return $this->sendSuccessResponse(200, $response);
    }

    public function actionLedger($loan_id)
    {
        $model = $this->findModel($loan_id);
        if (!empty($model)) {
            $response['data']['message'] = "Get Borrower Ledger Detail";
            $response['data']['member'] = ApiParser::parseLedger($model);
            $response['info'] = '';
            return $this->sendSuccessResponse(200, $response['data'], $response['info']);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }

    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted = 1;
        $model->deleted_by = Yii::$app->user->getId();
        $model->deleted_at = strtotime(date('Y-m-d'));
        if ($model->save()) {
            $response = ApiParser::parseLoan($model);
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "Enable to delete record.");
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