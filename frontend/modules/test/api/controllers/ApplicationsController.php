<?php

namespace frontend\modules\test\api\controllers;


use common\components\DBHelper;
use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\LogsHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\OperationHelper;
use common\components\Helpers\PushHelper;
use common\components\Helpers\SmsHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\AppraisalsBusiness;
use common\models\Loans;
use common\models\Members;
use common\models\Operations;
use common\models\ProjectAppraisalsMapping;
use common\models\ProjectsDisabled;
use common\models\Projects;
use common\models\search\ApplicationsSearch;
use common\models\SocialAppraisal;
use common\models\Users;
use common\models\Visits;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;

use Yii;
use yii\helpers\ArrayHelper;


class ApplicationsController extends RestController
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
                        return print_r(json_encode($this->sendFailedResponse('401', 'You are not allowed to perform this action.')));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id, $this->rbac_type, UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'index' => ['GET'],
                        'applicationdetails' => ['GET'],
                        'create' => ['POST'],
                        'searchmember' => ['GET', 'POST'],
                        'rejectedlist' => ['GET'],
                        'syncapplications' => ['POST'],
                        'update' => ['PUT'],
                        'bulkupdate' => ['PUT'],
                        'verificationupdate' => ['PUT'],
                        'view' => ['GET'],
                        'verificationview' => ['GET'],
                        'delete' => ['DELETE'],
                        'verified' => ['GET'],
                        'verification' => ['GET'],
                        'syncrecommendationimages' => ['POST'],
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
        $searchModel = new ApplicationsSearch();
        $response_data = $searchModel->searchApi($params);
        if ($response_data['info']['totalCount'] > 0) {
            $response_data['data'] = ApiParser::parseApplications($response_data['data']);
            $data = [];
            $appraisal = [];
            //$data = $response_data['data'];
            foreach ($response_data['data'] as $app_data) {
                $appraisals = ProjectAppraisalsMapping::find()->where(['project_id' => $app_data['project_id']])->all();
                foreach ($appraisals as $a)
                {
                    $appr = ApplicationActions::find()->where(['parent_id' => $app_data['id'], 'action' => $a->appraisal->name])->one();
                    $appraisal[$a->appraisal->appraisal_table] = isset($appr) ? $appr->status : 0;
                }
                $app_data['appraisals'] =$appraisal;
                $data[] = $app_data;
            }
            $response = [
                'applications' => $data,
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

    public function actionApplicationdetails($id)
    {
        $model = $this->findModel($id);
        $app_data = ApiParser::parseApplicationDetails($model);
        $appraisals = ProjectAppraisalsMapping::find()->where(['project_id' => $model->project_id])->all();
        foreach ($appraisals as $a)
        {
            $appr = ApplicationActions::find()->where(['parent_id' => $id, 'action' => $a->appraisal->name])->one();
            $app_data[$a->appraisal->name.'_status'] = isset($appr) ? $appr->status : 0;
        }
        $response = [
            'application' => $app_data
        ];
        return $this->sendSuccessResponse(200, $response);
    }

    public function actionRejectedlist()
    {
        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $searchModel = new ApplicationsSearch();
        $params['status'] = 'rejected';
        $params['order'] = 'updated_by desc';
        $data = $searchModel->searchApi($params);
        /*print_r($response['data']);
        die();*/
        $response = [];
        $return = [];
        if ($data['info']['totalCount'] > 0) {
            foreach ($data['data'] as $application) {
                $data_application = ApiParser::parseMember($application->member);
                $data_application['application'] = ApiParser::parseApplication($application);
                $return[] = $data_application;
            }
            $response = [
                'members' => $return,
                'page' => $data['info']['page'],
                'size' => $data['info']['size'],
                'total_count' => $data['info']['totalCount'],
                'total_records' => $data['info']['totalRecords']

            ];
            //$response['data'] = ApiParser::parseApplications($response['data']);

            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionCreate()
    {
        $model = new Applications();
        $model->attributes = $this->request;
        if ($this->request['member_id'] <= 0) {
            return $this->sendFailedResponse(400, "Member not exist.");
        } else {
            $model->application_no = (string)rand(99, 9999999);
            $model->no_of_times = 0;
            $model->region_id = $model->member->region_id;
            $model->area_id = $model->member->area_id;
            $model->branch_id = $model->member->branch_id;
            $model->team_id = $model->member->team_id;
            $model->field_id = $model->member->field_id;
            $project = Projects::find()->select(['project_table'])->where(['id' => $model->project_id])->one();
            if (isset($project) || !empty($project)) {
                $model->project_table = $project['project_table'];
            }
            $transaction = Yii::$app->db->beginTransaction();

            try {
                if ($flag = $model->save()) {

                    $action_model = new ApplicationActions();
                    $action_model->parent_id = $model->id;
                    $action_model->user_id = $model->created_by;
                    $action_model->action = "social_appraisal";
                    $action_model->save();

                    $action_model = new ApplicationActions();
                    $action_model->parent_id = $model->id;
                    $action_model->user_id = $model->created_by;
                    $action_model->action = "business_appraisal";
                    $action_model->save();

                    if (isset($this->request['fee']) && $this->request['fee'] != 0) {
                        $operation_model = new Operations();
                        $operation_model->platform = 2;
                        $operation_model->branch_id = $model->branch_id;
                        $operation_model->team_id = $model->team_id;
                        $operation_model->field_id = $model->field_id;
                        $operation_model->project_id = $model->project_id;
                        $operation_model->region_id = $model->region_id;
                        $operation_model->area_id = $model->area_id;
                        $operation_model->application_id = $model->id;
                        $operation_model->credit = $model->fee;
                        $operation_model->receipt_no = (string)rand(99, 9999999);
                        $operation_model->operation_type_id = 1;
                        $operation_model->receive_date = $model->created_at;
                        $operation_model->deleted_at = 0;
                        if (!$flag = $operation_model->save()) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, $operation_model->errors);
                        }
                    }


                    if (isset($this->request['utility_bill'])) {
                        $base_code_utility_bill = $this->request['utility_bill'];
                        $utility_bill_image_name = 'bill_' . rand(111111, 999999) . '.png';
                        if (!(ImageHelper::imageUpload($model->id, 'applications', 'utility_bill', $utility_bill_image_name, $base_code_utility_bill))) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, "Utility Bill not Saved.");
                        }
                    }
                    if (isset($this->request['marriage_certificate'])) {
                        $base_code_marriage_certificate = $this->request['marriage_certificate'];
                        $marriage_certificate_image_name = 'marriage_certificate_' . rand(111111, 999999) . '.png';
                        if (!(ImageHelper::imageUpload($model->id, 'applications', 'marriage_certificate', $marriage_certificate_image_name, $base_code_marriage_certificate))) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, "Marriage Certificate not Saved.");
                        }
                    }
                    //print_r($projects);

                    $projects = Projects::find()->select(['project_table'])->where(['status' => 1])->andWhere(['<>', 'project_table', ''])->asArray()->all();

                    $projects_array = array();
                    foreach ($projects as $p) {
                        $projects_array[] = $p['project_table'];
                    }
                    if (in_array($model->project_table, $projects_array)) {

                        $project_table_name = str_replace('_', ' ', $model->project_table);
                        $project_table_name = ucwords($project_table_name);
                        $project_table_name = str_replace(' ', '', $project_table_name);

                        $class = 'common\models\\' . $project_table_name;
                        $project_detail_model = new $class;

                        $project_detail_model->attributes = $this->request;
                        $project_detail_model->application_id = $model->id;
                        if ($model->project_table == 'project_details_tevta') {
                            if (isset($this->request['tevta_certificate'])) {
                                $base_code_tevta_certificate = $this->request['tevta_certificate'];
                                $tevta_certificate_image_name = 'tevta_certificate' . rand(111111, 999999) . '.png';
                                if (!(ImageHelper::imageUpload($model->id, 'applications', 'tevta_certificate', $tevta_certificate_image_name, $base_code_tevta_certificate))) {
                                    $transaction->rollBack();
                                    return $this->sendFailedResponse(400, "Tevta Certificate not Saved.");
                                }
                            } else {
                                return $this->sendFailedResponse(400, "Tevta Certificate is required.");
                            }
                        }

                        if (!$flag = $project_detail_model->save()) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, $project_detail_model->errors);
                        }
                    }
                }

                if ($flag) {
                    $transaction->commit();
                    $data = ApiParser::parseApplication($model);
                    $project_details = isset($model->project_table) ? ApplicationHelper::getProjectDetail($model->id, $model->project_table) : [];
                    $data = array_merge($data, $project_details);
                    $data['utility_bill'] = ApiParser::parseImage(ApplicationHelper::getUtilityBill($model->id), $model->id);
                    $data['marriage_certificate'] = ApiParser::parseImage(ApplicationHelper::getMarraigeCertificate($model->id), $model->id);
                    return $this->sendSuccessResponse(201, $data);
                } else {
                    $transaction->rollBack();
                    return $this->sendFailedResponse(400, $model->errors);
                }

            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
    }

    public function actionRecommend()
    {
        if (isset($this->request['application_id']) && isset($this->request['recommended_amount'])) {
            $model = Applications::find()->where(['id' => $this->request['application_id'], 'deleted' => 0, 'status' => 'approved'])->one();
            if (!isset($model)) {
                return $this->sendFailedResponse(400, "Application not exist.");
            } else {
                $model->recommended_amount = $this->request['recommended_amount'];
                if ($model->save()) {
                    return $this->sendSuccessResponse(201, "Application Recommended.");
                } else {
                    return $this->sendFailedResponse(400, $model->errors);
                }
            }
        } else {
            return $this->sendFailedResponse(400, "Application Id, Recommended Amount are required fields.");
        }
    }

    public function actionSyncapplications()
    {
        $applications = $this->request;
        $errors = [];
        $success = [];
        $success_response = [];
        $data = [];
        $access_token = Yii::$app->getRequest()->getHeaders()->get('x-access-token');
        $user = Users::findIdentityByAccessToken($access_token);
        $branches = UsersHelper::getUserBranches($user);

        foreach ($applications as $application) {
            $update_flag = false;
            $error = '<ul>';
            if (!isset($application['member_id'])) {
                $error .= ApiParser::parseErrors("Member ID must be required.");
            } else {
                $member = Members::findOne(['deleted' => 0, 'status' => 'approved', 'id' => $application['member_id']]);
                $application_data = MemberHelper::getExistingApplicationFromCNIC($member->cnic);
                if ($application['member_id'] <= 0 || !isset($member)) {
                    $error .= ApiParser::parseErrors("Member not exist.");
                }
                if (isset($application_data) && $application_data->created_by == $user->id && in_array($application_data->branch_id,$branches)) {
                    $model = $application_data;
                    if (isset($model)) {
                        $project_table = $model->project_table;
                        $model->attributes = $application;
                        $project = Projects::find()->select(['project_table'])->where(['id' => $model->project_id])->one();
                        if (isset($project) || !empty($project)) {
                            $model->project_table = $project['project_table'];
                        }
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if ($flag = $model->save()) {
                                $operation_model_save=OperationHelper::saveOperations($model,'fee',$model->fee,0,time(),2);
                                if($operation_model_save !=1){
                                    $transaction->rollBack();
                                    $error .= ApiParser::parseErrors($operation_model_save);
                                }

                                $projects = Projects::find()->select(['project_table'])->where(['status' => 1])->andWhere(['<>', 'project_table', ''])->asArray()->all();
                                $projects_array = array();
                                foreach ($projects as $p) {
                                    $projects_array[] = $p['project_table'];
                                }
                                if (in_array($model->project_table, $projects_array)) {

                                    if (isset($model->project_table) && !empty($project_table) && $project_table != $model->project_table) {
                                        $project_table_name = str_replace('_', ' ', $project_table);
                                        $project_table_name = ucwords($project_table_name);
                                        $project_table_name = str_replace(' ', '', $project_table_name);

                                        $class = 'common\models\\' . $project_table_name;

                                        $project_model = $class::find()->where(['application_id' => $application['id'], 'deleted' => 0])->one();

                                        if (isset($project_model)) {
                                            $project_model->deleted = 1;
                                            $project_model->save();
                                        }

                                        $project_table_name = str_replace('_', ' ', $application['project_table']);
                                        $project_table_name = ucwords($project_table_name);
                                        $project_table_name = str_replace(' ', '', $project_table_name);

                                        $class = 'common\models\\' . $project_table_name;

                                        $project_detail_model = new $class;
                                        $project_detail_model->attributes = $application;
                                        $project_detail_model->application_id = $model->id;
                                        if (!$flag = $project_detail_model->save()) {
                                            $transaction->rollBack();
                                            $error .= ApiParser::parseErrors($project_detail_model->errors);
                                        }
                                    } else {
                                        $project_table_name = str_replace('_', ' ', $model->project_table);
                                        $project_table_name = ucwords($project_table_name);
                                        $project_table_name = str_replace(' ', '', $project_table_name);

                                        $class = 'common\models\\' . $project_table_name;
                                        $project_detail_model = $class::find()->where(['application_id' => $model->id, 'deleted' => 0])->one();
                                        if (isset($project_detail_model)) {
                                            $project_detail_model->attributes = $application;
                                            $project_detail_model->application_id = $model->id;
                                            if (!$flag = $project_detail_model->save()) {
                                                $transaction->rollBack();
                                                $error .= ApiParser::parseErrors($project_detail_model->errors);
                                            }
                                        }
                                    }
                                }
                            }

                            if ($flag) {
                                $transaction->commit();
                                $success[] = ['id' => $model->id, 'temp_id' => $application['temp_id'], 'application_no' => $model->application_no, 'cnic' => $model->member->cnic, 'status' => $model->member->full_name . "'s application has synced successfully.",
                                    "reject_type"=>"", "reject_reason"=>"","comments"=>""];
                                $success_response[] = $application['temp_id'];
                            } else {
                                $transaction->rollBack();
                                $error .= ApiParser::parseErrors($model->errors);
                            }

                        } catch (Exception $e) {
                            $transaction->rollBack();
                        }
                    } else {
                        $error .= ApiParser::parseErrors("Invalid Record requested");
                    }
                }
                else {
                    if (!isset($application['application_no']) || empty($application['application_no'])) {
                        $loan = MemberHelper::getActiveLoanFromCNIC($member->cnic);
                        if (isset($loan)) {
                            $error .= ApiParser::parseErrors('Active Loan Exist');
                        } else if ((isset($application_data)) && ($application_data->created_by != $user->id || !in_array($application_data->branch_id,$branches))) {
                            $error .= ApiParser::parseErrors('Application Already Exist against application no. '. $application_data->application_no.' which is created by '.(UsersHelper::getUserName($application_data->created_by)->fullname));
                        }
                        else {
                            $model = new Applications();
                            $model->attributes = $application;
                            $model->platform = 2;
                            $model->application_date = date('Y-m-d');
                            $model->get_application_no();
                            $model->set_application_hierarchy($user);
                            $model->set_application_project();
                            $model->no_of_times = 0;
                            $flag=true;
                            $application_model=ApplicationHelper::preConditionsApplication($model);
                            if(!empty($application_model->getErrors())){
                                //$error .= ApiParser::parseErrors($application_model->getErrors());
                                $flag = false;
                            }
                            $transaction = Yii::$app->db->beginTransaction();
                            try {
                                if($flag) {
                                    if ($flag = $model->save()) {

                                        $operation_model_save=OperationHelper::saveOperations($model,'fee',$model->fee,0,time(),2);
                                        if($operation_model_save !=1){
                                            $transaction->rollBack();
                                            $error .= ApiParser::parseErrors($operation_model_save);
                                        }

                                        $projects = Projects::find()->select(['project_table'])->where(['status' => 1])->andWhere(['<>', 'project_table', ''])->asArray()->all();
                                        $projects_array = array();
                                        foreach ($projects as $p) {
                                            $projects_array[] = $p['project_table'];
                                        }
                                        if (in_array($model->project_table, $projects_array)) {

                                            $project_table_name = str_replace('_', ' ', $model->project_table);
                                            $project_table_name = ucwords($project_table_name);
                                            $project_table_name = str_replace(' ', '', $project_table_name);

                                            $class = 'common\models\\' . $project_table_name;

                                            $project_detail_model = new $class;

                                            $project_detail_model->attributes = $application;
                                            $project_detail_model->application_id = $model->id;

                                            if (!$flag = $project_detail_model->save()) {
                                                $transaction->rollBack();
                                                Yii::$app->db->createCommand('ALTER TABLE applications auto_increment = 1')->execute();
                                                $error .= ApiParser::parseErrors($project_detail_model->errors);
                                            }
                                        }
                                    }
                                }

                                if ($flag) {
                                    ActionsHelper::insertActions('application',$model->project_id,$model->id,$model->created_by);
                                    //PushHelper::newApplicationNotification($model);
                                    /*$action_model_social_appraisal = new ApplicationActions();
                                    $action_model_social_appraisal->setValues($model->id, 'family_member_info', $model->created_by, 0);
                                    if (!$action_model_social_appraisal->save()) {
                                        $error .= ApiParser::parseErrors($model->getErrors());
                                    }
                                    $action_model_social_appraisal = new ApplicationActions();
                                    $action_model_social_appraisal->setValues($model->id, 'social_appraisal', $model->created_by, 0);
                                    if (!$action_model_social_appraisal->save()) {
                                        $error .= ApiParser::parseErrors($model->getErrors());
                                    }
                                    $action_model_business_appraisal = new ApplicationActions();
                                    $action_model_business_appraisal->setValues($model->id, 'business_appraisal', $model->created_by, 0);
                                    if (!$action_model_business_appraisal->save()) {
                                        $error .= ApiParser::parseErrors($model->getErrors());
                                    }

                                    $action_model_business_appraisal = new ApplicationActions();
                                    $action_model_business_appraisal->setValues($model->id, 'approved/rejected', $model->created_by, 0);
                                    if (!$action_model_business_appraisal->save()) {
                                        $error .= ApiParser::parseErrors($model->getErrors());
                                    }*/
                                    SmsHelper::SmsLogs('application', $model);
                                    $success[] = ['id' => $model->id, 'temp_id' => $application['temp_id'], 'application_no' => $model->application_no, 'cnic' => $model->member->cnic, 'status' => $model->member->full_name . "'s application has synced successfully.",
                                        "reject_type"=>"", "reject_reason"=>"","comments"=>""];
                                    $success_response[] = $application['temp_id'];
                                    $transaction->commit();
                                    /*$msg = SmsHelper::getApplicationText($model);
                                    $mobile = isset($model->member->membersMobile->phone) ? $model->member->membersMobile->phone : '';
                                    $sms = SmsHelper::SendUrdusms($mobile, $msg);
                                    if ($sms->corpsms[0]->type == 'Success') {
                                        SmsHelper::SmsLogs('application', $model);
                                        $success[] = ['id' => $model->id, 'temp_id' => $application['temp_id'], 'application_no' => $model->application_no, 'cnic' => $model->member->cnic, 'status' => $model->member->full_name . "'s application has synced successfully.",
                                        "reject_type"=>"", "reject_reason"=>"","comments"=>""];
                                        $success_response[] = $application['temp_id'];
                                        $transaction->commit();
                                    } else {
                                        $error .= ApiParser::parseErrors($sms->corpsms[0]->response);
                                        $transaction->rollBack();
                                        Yii::$app->db->createCommand('ALTER TABLE applications auto_increment = 1')->execute();
                                    }*/

                                } else {

                                    $transaction->rollBack();
                                    Yii::$app->db->createCommand('ALTER TABLE applications auto_increment = 1')->execute();
                                    $error .= ApiParser::parseErrors($model->errors);
                                }

                            } catch (Exception $e) {
                                $transaction->rollBack();
                            }
                        }
                    } else {

                        $model = Applications::findOne(['application_no' => $application['application_no'], 'deleted' => 0]);
                        if (isset($model)) {
                            $project_table = $model->project_table;
                            $model->attributes = $application;
                            $project = Projects::find()->select(['project_table'])->where(['id' => $model->project_id])->one();
                            if (isset($project) || !empty($project)) {
                                $model->project_table = $project['project_table'];
                            }
                            $transaction = Yii::$app->db->beginTransaction();
                            try {
                                if ($flag = $model->save()) {

                                    $operation_model_save=OperationHelper::saveOperations($model,'fee',$model->fee,0,time(),2);
                                    if($operation_model_save !=1){
                                        $transaction->rollBack();
                                        $error .= ApiParser::parseErrors($operation_model_save);
                                    }

                                    $projects = Projects::find()->select(['project_table'])->where(['status' => 1])->andWhere(['<>', 'project_table', ''])->asArray()->all();
                                    $projects_array = array();
                                    foreach ($projects as $p) {
                                        $projects_array[] = $p['project_table'];
                                    }
                                    if (in_array($model->project_table, $projects_array)) {

                                        if (isset($model->project_table) && !empty($project_table) && $project_table != $model->project_table) {
                                            $project_table_name = str_replace('_', ' ', $project_table);
                                            $project_table_name = ucwords($project_table_name);
                                            $project_table_name = str_replace(' ', '', $project_table_name);

                                            $class = 'common\models\\' . $project_table_name;

                                            $project_model = $class::find()->where(['application_id' => $application['id'], 'deleted' => 0])->one();

                                            if (isset($project_model)) {
                                                $project_model->deleted = 1;
                                                $project_model->save();
                                            }

                                            $project_table_name = str_replace('_', ' ', $application['project_table']);
                                            $project_table_name = ucwords($project_table_name);
                                            $project_table_name = str_replace(' ', '', $project_table_name);

                                            $class = 'common\models\\' . $project_table_name;

                                            $project_detail_model = new $class;
                                            $project_detail_model->attributes = $application;
                                            $project_detail_model->application_id = $model->id;
                                            if (!$flag = $project_detail_model->save()) {
                                                $transaction->rollBack();
                                                $error .= ApiParser::parseErrors($project_detail_model->errors);
                                            }
                                        } else {
                                            $project_table_name = str_replace('_', ' ', $model->project_table);
                                            $project_table_name = ucwords($project_table_name);
                                            $project_table_name = str_replace(' ', '', $project_table_name);

                                            $class = 'common\models\\' . $project_table_name;
                                            $project_detail_model = $class::find()->where(['application_id' => $model->id, 'deleted' => 0])->one();
                                            if (isset($project_detail_model)) {
                                                $project_detail_model->attributes = $application;
                                                $project_detail_model->application_id = $model->id;
                                                if (!$flag = $project_detail_model->save()) {
                                                    $transaction->rollBack();
                                                    $error .= ApiParser::parseErrors($project_detail_model->errors);
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($flag) {
                                    $transaction->commit();
                                    $success[] = ['id' => $model->id, 'temp_id' => $application['temp_id'], 'application_no' => $model->application_no, 'cnic' => $model->member->cnic, 'status' => $model->member->full_name . "'s application has synced successfully.",
                                        "reject_type"=>"", "reject_reason"=>"","comments"=>""];
                                    $success_response[] = $application['temp_id'];
                                } else {
                                    $transaction->rollBack();
                                    $error .= ApiParser::parseErrors($model->errors);
                                }

                            } catch (Exception $e) {
                                $transaction->rollBack();
                            }
                        } else {
                            $error .= ApiParser::parseErrors("Invalid Record requested");
                        }
                    }
                }
            }
            $error .= '</ul>';
            if (!in_array($application['temp_id'], $success_response)) {

                $errors[] = ['temp_id' => $application['temp_id'], 'error' => $error, 'cnic' => $member->cnic, 'status' => 'rejected'];
            }
        }
        if (empty($success) && !empty($errors)) {
            $data['response_status'] = 'error';
        } else if (!empty($success) && empty($errors)) {
            $data['response_status'] = 'success';
        } else {
            $data['response_status'] = 'warning';
        }
        $data['success'] = $success;
        $data['errors'] = $errors;
        return $this->sendSuccessResponse(201, $data);
    }

    public function actionUpdate()
    {
        $model = $this->findModel($this->request['applications']['id']);
        $project_table = $model->project_table;
        $model->activity_id = 0;
        //$status = $model->status;
        $model->attributes = $this->request['applications'];
        //$model->status = $status;
        $project = Projects::find()->select(['project_table'])->where(['id' => $model->project_id])->one();
        if (isset($project) || !empty($project)) {
            $model->project_table = $project['project_table'];
        }
        $errors = [];
        $success = [];
        $success_response = [];
        $data = [];
        $error = '<ul>';
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($flag = $model->save()) {
                /*if(isset($application['status']))
                {
                    $prev_action_model = ApplicationActions::findOne(['parent_id' => $model->id, 'action' => 'business_appraisal']);
                    $action_model = ApplicationActions::findOne(['parent_id' => $model->id, 'action' => 'approved/rejected']);
                    if(!isset($action_model))
                    {
                        $transaction->rollBack();
                        $error .= ApiParser::parseErrors("Business Appraisal must done before update status");
                    } else {
                        $action_model->status = 1;
                        $action_model->pre_action = $prev_action_model->id;
                        $action_model->expiry_date = strtotime('+10 days',strtotime( date('Y-m-d H:i:s')));
                        $action_model->save();

                        $action_model = new ApplicationActions();
                        $action_model->parent_id = $model->id;
                        $action_model->user_id = $model->created_by;
                        $action_model->action = "group_formation";
                        $action_model->save();
                    }
                }*/

                $operation_model_save=OperationHelper::saveOperations($model,'fee',$model->fee,0,time(),2);
                if($operation_model_save !=1){
                    $transaction->rollBack();
                    $error .= ApiParser::parseErrors($operation_model_save);
                }

                $projects = Projects::find()->select(['project_table'])->where(['status' => 1])->andWhere(['<>', 'project_table', ''])->asArray()->all();
                $projects_array = array();
                foreach ($projects as $p) {
                    $projects_array[] = $p['project_table'];
                }
                if (in_array($model->project_table, $projects_array)) {

                    if (isset($model->project_table) && !empty($project_table) && $project_table != $model->project_table) {

                        $project_table_name = str_replace('_', ' ', $project_table);
                        $project_table_name = ucwords($project_table_name);
                        $project_table_name = str_replace(' ', '', $project_table_name);

                        $class = 'common\models\\' . $project_table_name;

                        $project_model = $class::find()->where(['application_id' => $model->id, 'deleted' => 0])->one();

                        if (isset($project_model)) {
                            $project_model->deleted = 1;
                            $project_model->save();
                        }

                        $project_table_name = str_replace('_', ' ', $model->project_table);
                        $project_table_name = ucwords($project_table_name);
                        $project_table_name = str_replace(' ', '', $project_table_name);

                        $class = 'common\models\\' . $project_table_name;

                        $project_detail_model = new $class;
                        $project_detail_model->attributes = $this->request[$model->project_table];
                        $project_detail_model->application_id = $model->id;

                        if (!$flag = $project_detail_model->save()) {
                            $transaction->rollBack();
                            $error .= ApiParser::parseErrors($project_detail_model->errors);
                        }
                    } else {

                        $project_table_name = str_replace('_', ' ', $model->project_table);
                        $project_table_name = ucwords($project_table_name);
                        $project_table_name = str_replace(' ', '', $project_table_name);

                        $class = 'common\models\\' . $project_table_name;
                        $project_detail_model = $class::find()->where(['application_id' => $model->id, 'deleted' => 0])->one();
                        if (isset($project_detail_model)) {
                            $project_detail_model->attributes = $this->request[$model->project_table];
                            $project_detail_model->application_id = $model->id;
                            if (!$flag = $project_detail_model->save()) {
                                $transaction->rollBack();
                                $error .= ApiParser::parseErrors($project_detail_model->errors);
                            }
                        }
                    }
                }

                if (isset($this->request['applications']['documents']))
                {
                    $documents =(array) json_decode($this->request['applications']['documents'], true);
                    foreach ($documents as $doc)
                    {
                        $base_code = $doc['file_address'];
                        $image_name = $doc['file_type'].'_' . rand(111111, 999999) . '.png';
                        if (!(ImageHelper::imageUpload($model->id, 'applications', $doc['file_type'], $image_name, $base_code))) {
                            $error .= ApiParser::parseErrors($doc['file_type']." image not Saved");
                        }
                    }
                }

                /*if (isset($this->request['documents']['utility_bill'])) {
                    $base_code = $this->request['documents']['utility_bill'];
                    $image_name = 'utility_bill_' . rand(111111, 999999) . '.png';
                    if (!(ImageHelper::imageUpload($model->id, 'applications', 'utility_bill', $image_name, $base_code))) {
                        $error .= ApiParser::parseErrors("Utility Bill image not Saved");
                    }
                }

                if (isset($this->request['documents']['marraige_certificate'])) {
                    $base_code = $this->request['documents']['marraige_certificate'];
                    $image_name = 'marraige_certificate_' . rand(111111, 999999) . '.png';
                    if (!(ImageHelper::imageUpload($model->id, 'applications', 'marraige_certificate', $image_name, $base_code))) {
                        $error .= ApiParser::parseErrors("Marriage Ceritificate image not Saved");
                    }
                }*/
            }

            if ($flag) {
                $transaction->commit();
                $success[] = ['id' => $model->id, 'application_no' => $model->application_no, 'cnic' => $model->member->cnic, 'status' => $model->member->full_name . "'s application has synced successfully."];
                $success_response[] = $model->id;
            } else {
                $transaction->rollBack();
                $error .= ApiParser::parseErrors($model->errors);
            }

            $error .= '</ul>';
            if (!in_array($model->id, $success_response)) {

                $errors[] = ['error' => $error, 'cnic' => $model->member->cnic, 'status' => 'rejected'];
            }


            if (empty($success) && !empty($errors)) {
                $data['response_status'] = 'error';
            } else if (!empty($success) && empty($errors)) {
                $data['response_status'] = 'success';
            } else {
                $data['response_status'] = 'warning';
            }
            $data['success'] = $success;
            $data['errors'] = $errors;
            return $this->sendSuccessResponse(201, $data);

        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    public function actionVerificationupdate()
    {
        $model = $this->findModel($this->request['applications']['id']);
        $project_table = $model->project_table;
        $model->activity_id = 0;
        $model->attributes = $this->request['applications'];
        $project = Projects::find()->select(['project_table'])->where(['id' => $model->project_id])->one();
        if (isset($project) || !empty($project)) {
            $model->project_table = $project['project_table'];
        }
        $errors = [];
        $success = [];
        $success_response = [];
        $data = [];
        $error = '<ul>';
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($flag = $model->save()) {
                $operation_model_save=OperationHelper::saveOperations($model,'fee',$model->fee,0,time(),2);
                if($operation_model_save !=1){
                    $transaction->rollBack();
                    $error .= ApiParser::parseErrors($operation_model_save);
                }

                $projects = Projects::find()->select(['project_table'])->where(['status' => 1])->andWhere(['<>', 'project_table', ''])->asArray()->all();
                $projects_array = array();
                foreach ($projects as $p) {
                    $projects_array[] = $p['project_table'];
                }
                if (in_array($model->project_table, $projects_array)) {

                    if (isset($model->project_table) && !empty($project_table) && $project_table != $model->project_table) {
                        $project_table_name = str_replace('_', ' ', $project_table);
                        $project_table_name = ucwords($project_table_name);
                        $project_table_name = str_replace(' ', '', $project_table_name);

                        $class = 'common\models\\' . $project_table_name;

                        $project_model = $class::find()->where(['application_id' => $model->id, 'deleted' => 0])->one();

                        if (isset($project_model)) {
                            $project_model->deleted = 1;
                            $project_model->save();
                        }

                        $project_table_name = str_replace('_', ' ', $model->project_table);
                        $project_table_name = ucwords($project_table_name);
                        $project_table_name = str_replace(' ', '', $project_table_name);

                        $class = 'common\models\\' . $project_table_name;

                        $project_detail_model = new $class;
                        $project_detail_model->attributes = $this->request[$model->project_table];
                        $project_detail_model->application_id = $model->id;
                        if (!$flag = $project_detail_model->save()) {
                            $transaction->rollBack();
                            $error .= ApiParser::parseErrors($project_detail_model->errors);
                        }
                    } else {
                        $project_table_name = str_replace('_', ' ', $model->project_table);
                        $project_table_name = ucwords($project_table_name);
                        $project_table_name = str_replace(' ', '', $project_table_name);

                        $class = 'common\models\\' . $project_table_name;
                        $project_detail_model = $class::find()->where(['application_id' => $model->id, 'deleted' => 0])->one();
                        if (isset($project_detail_model)) {
                            $project_detail_model->attributes = $this->request[$model->project_table];
                            $project_detail_model->application_id = $model->id;
                            if (!$flag = $project_detail_model->save()) {
                                $transaction->rollBack();
                                $error .= ApiParser::parseErrors($project_detail_model->errors);
                            }
                        }
                    }
                }
                if (isset($this->request['documents']['utility_bill'])) {
                    $base_code = $this->request['documents']['utility_bill'];
                    $image_name = 'utility_bill_' . rand(111111, 999999) . '.png';
                    if (!(ImageHelper::imageUpload($model->id, 'applications', 'utility_bill', $image_name, $base_code))) {
                        $error .= ApiParser::parseErrors("Utility Bill image not Saved");
                    }
                }

                if (isset($this->request['documents']['marraige_certificate'])) {
                    $base_code = $this->request['documents']['marraige_certificate'];
                    $image_name = 'marraige_certificate_' . rand(111111, 999999) . '.png';
                    if (!(ImageHelper::imageUpload($model->id, 'applications', 'marraige_certificate', $image_name, $base_code))) {
                        $error .= ApiParser::parseErrors("Marriage Ceritificate image not Saved");
                    }
                }
            }

            if ($flag) {
                $transaction->commit();
                $success[] = ['id' => $model->id, 'application_no' => $model->application_no, 'cnic' => $model->member->cnic, 'status' => $model->member->full_name . "'s application has synced successfully."];
                $success_response[] = $model->id;
            } else {
                $transaction->rollBack();
                $error .= ApiParser::parseErrors($model->errors);
            }

            $error .= '</ul>';
            if (!in_array($model->id, $success_response)) {

                $errors[] = ['error' => $error, 'cnic' => $model->member->cnic, 'status' => 'rejected'];
            }


            if (empty($success) && !empty($errors)) {
                $data['response_status'] = 'error';
            } else if (!empty($success) && empty($errors)) {
                $data['response_status'] = 'success';
            } else {
                $data['response_status'] = 'warning';
            }
            $data['success'] = $success;
            $data['errors'] = $errors;
            return $this->sendSuccessResponse(201, $data);

        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    public function actionBulkupdate()
    {
        $applications = $this->request;
        $errors = [];
        $success = [];
        $response = [];
        $success_response = [];
        foreach ($applications as $application) {
            $error = '<ul>';
            $model = Applications::findOne(['id' => $application['id'], 'deleted' => 0]);
            if (isset($model)) {
                $project_table = $model->project_table;
                $model->attributes = $application;
                $project = Projects::find()->select(['project_table'])->where(['id' => $model->project_id])->one();
                if (isset($project) || !empty($project)) {
                    $model->project_table = $project['project_table'];
                }
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {

                        if (isset($application['status'])) {
                            $prev_action_model = ApplicationActions::findOne(['parent_id' => $model->id, 'action' => 'business_appraisal']);
                            $action_model = ApplicationActions::findOne(['parent_id' => $model->id, 'action' => 'approved/rejected']);
                            if (!isset($action_model)) {
                                $transaction->rollBack();
                                $error .= ApiParser::parseErrors("Business Appraisal must done before update status");
                            } else {
                                $action_model->status = 1;
                                $action_model->pre_action = $prev_action_model->id;
                                $action_model->save();

                                $action_model = new ApplicationActions();
                                $action_model->parent_id = $model->id;
                                $action_model->user_id = $model->created_by;
                                $action_model->action = "group_formation";
                                $action_model->save();
                            }
                        }

                        $operation_model_save=OperationHelper::saveOperations($model,'fee',$model->fee,0,time(),2);
                        if($operation_model_save !=1){
                            $transaction->rollBack();
                            $error .= ApiParser::parseErrors($operation_model_save);
                        }

                        $projects = Projects::find()->select(['project_table'])->where(['status' => 1])->andWhere(['<>', 'project_table', ''])->asArray()->all();
                        $projects_array = array();
                        foreach ($projects as $p) {
                            $projects_array[] = $p['project_table'];
                        }
                        if (in_array($model->project_table, $projects_array)) {

                            if (isset($model->project_table) && !empty($project_table) && $project_table != $model->project_table) {
                                $project_table_name = str_replace('_', ' ', $project_table);
                                $project_table_name = ucwords($project_table_name);
                                $project_table_name = str_replace(' ', '', $project_table_name);

                                $class = 'common\models\\' . $project_table_name;

                                $project_model = $class::find()->where(['application_id' => $application['id'], 'deleted' => 0])->one();

                                if (isset($project_model)) {
                                    $project_model->deleted = 1;
                                    $project_model->save();
                                }

                                $project_table_name = str_replace('_', ' ', $application['project_table']);
                                $project_table_name = ucwords($project_table_name);
                                $project_table_name = str_replace(' ', '', $project_table_name);

                                $class = 'common\models\\' . $project_table_name;

                                $project_detail_model = new $class;
                                $project_detail_model->attributes = $application;
                                $project_detail_model->application_id = $model->id;

                                if (!$flag = $project_detail_model->save()) {
                                    $transaction->rollBack();
                                    $error .= ApiParser::parseErrors($project_detail_model->errors);
                                }
                            } else {
                                $project_table_name = str_replace('_', ' ', $model->project_table);
                                $project_table_name = ucwords($project_table_name);
                                $project_table_name = str_replace(' ', '', $project_table_name);

                                $class = 'common\models\\' . $project_table_name;
                                $project_detail_model = $class::find()->where(['application_id' => $application['id'], 'deleted' => 0])->one();
                                if (isset($project_detail_model)) {
                                    $project_detail_model->attributes = $application;
                                    $project_detail_model->application_id = $model->id;

                                    if (!$flag = $project_detail_model->save()) {
                                        $transaction->rollBack();
                                        $error .= ApiParser::parseErrors($project_detail_model->errors);
                                    }
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        $success[] = ['id' => $model->id, 'application_no' => $model->application_no];
                        $success_response[] = $model->id;
                    } else {
                        $transaction->rollBack();
                        $error .= ApiParser::parseErrors($model->errors);
                    }

                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            } else {
                $error .= ApiParser::parseErrors("Invalid Record requested");
            }
            $error .= '</ul>';
            if (!in_array($application['id'], $success_response)) {
                $errors[] = ['id' => $application['id'], 'error' => $error];
            }
        }

        $response['success'] = $success;
        $response['errors'] = $errors;
        return $this->sendSuccessResponse(200, $response);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $response = ApplicationHelper::getApplicationdetailsTest($model);
        return $this->sendSuccessResponse(200,$response);
    }

    public function actionVerificationview($id)
    {
        $model = $this->findModel($id);
        $response = ApplicationHelper::getApplicationdetails($model);
        return $this->sendSuccessResponse(200,$response);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted = 1;
        $model->deleted_by = Yii::$app->user->getId();
        $model->deleted_at = strtotime(date('Y-m-d'));
        if ($model->save()) {
            $data = ApiParser::parseApplication($model);
            $project_details = isset($model->project_table) ? ApplicationHelper::getProjectDetail($model->id, $model->project_table) : [];
            $data = array_merge($data, $project_details);
            $data['utility_bill'] = ApiParser::parseImage(ApplicationHelper::getUtilityBill($model->id), $model->id);
            $data['marriage_certificate'] = ApiParser::parseImage(ApplicationHelper::getMarraigeCertificate($model->id), $model->id);
            return $this->sendSuccessResponse(200, $data);
        } else {
            return $this->sendFailedResponse(400, "Enable to delete record.");
        }
    }


    public function actionVerification()
    {
        $params = $_GET;
        $params['order'] = 'applications.created_at desc';
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $searchModel = new ApplicationsSearch();
        $data = $searchModel->searchApiVerificationNew($params);
        $response = [];
        $member = [];
        if (!empty($data['data'])) {
            $members_data = ApiParser::parseVerificationMembers($data['data']);
            foreach ($members_data as $app_data) {
                $application = Applications::findOne(['member_id' => $app_data['id'],'deleted' => 0]);
                $app_data = array_merge($app_data,ApplicationHelper::getAppraisalLocations($application));
                $member[] = $app_data;
            }
            $response = [
                'members' => $member,
                'page' => $data['info']['page'],
                'size' => $data['info']['size'],
                'total_count' => $data['info']['totalCount'],
                'total_records' => $data['info']['totalRecords']
            ];
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionVerified()
    {
        $params = $_GET;
        $params['order'] = 'applications.updated_at desc';
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $searchModel = new ApplicationsSearch();
        $data = $searchModel->searchApiVerified($params);
        $response = [];
        $members_data = [];
        if (!empty($data['data'])) {
            $members = ApiParser::parseVerificationMembers($data['data'],'approved');
            foreach ($members as $member)
            {
                $params['cnic'] = $member['cnic'];
                $record = $searchModel->searchApiMemberInfo($params);
                $member['application_record'] = ApiParser::parseApplicationMembers($record['data']);
                $members_data[] = $member;
            }
            $response = [
                'members' => $members_data,
                'page' => $data['info']['page'],
                'size' => $data['info']['size'],
                'total_count' => $data['info']['totalCount'],
                'total_records' => $data['info']['totalRecords']
            ];
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionSearchmember()
    {
        $params = $_GET;

        $loan = Applications::find()->select(['members.id', 'members.cnic'])
            ->joinWith('member')
            ->joinWith('loan')
            ->where(['cnic' => $params['search']['cnic'], 'loans.status' => "collected"])->one();

        if (!isset($loan) && empty($loan)) {
            $params['rbac_type'] = $this->rbac_type;
            $params['controller'] = Yii::$app->controller->id;
            $params['method'] = Yii::$app->controller->action->id;
            $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
            $searchModel = new ApplicationsSearch();
            $response_data = $searchModel->searchApiMemberInfo($params);
            if ($response_data['info']['totalCount'] > 0) {
                $response['data'] = ApiParser::parseApplicationMembers($response_data['data']);
                return $this->sendSuccessResponse(200, $response['data']);
            } else {
                return $this->sendFailedResponse(204, "Record not found");
            }
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionSyncrecommendationimages()
    {
        $flag = true;
        $request = $this->request;
        $model = new Visits();
        $model->attributes = $request;
        $model->save();
        $base64_decode_data = base64_decode($request['image']);
        $images_data = json_decode(gzdecode($base64_decode_data));
        foreach ($images_data as $data)
        {
            $image_data = [];
            $image_data['image_data'] = $data;
            $image_data['parent_id'] = $model->id;
            $image_data['parent_type'] = 'visits';
            $image_data['image_type'] = 'visit_'.rand(1,9);
            $image = ImageHelper::syncImageObject($image_data);
            if(!$image)
            {
                $flag = false;
            }
        }
        $response['message'] = 'Visit save successfully';
        if($flag) {
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "Visit not save.");
        }
    }

    public function actionVisits($id)
    {
        $response = ApplicationHelper::getVisitsByRole($id);
        if(isset($response)) {
            return $this->sendSuccessResponse(200, $response);
        } else {
            return $this->sendFailedResponse(204, "No Record Found.");
        }
    }

    protected function findModel($id)
    {
        if (($model = Applications::findOne(['id' => $id, 'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204, "Invalid Record requested");
        }
    }
}