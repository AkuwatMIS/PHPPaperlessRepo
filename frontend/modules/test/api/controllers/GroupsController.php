<?php

namespace frontend\modules\test\api\controllers;


use Codeception\Application;
use common\components\Helpers\ActionsHelper;
use common\components\Helpers\GroupHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\LoanHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\Branches;
use common\models\AppraisalsBusiness;
use common\models\GroupActions;
use common\models\Groups;
use common\models\Guarantors;
use common\models\Images;
use common\models\Loans;
use common\models\Members;
use common\models\search\ApplicationsSearch;
use common\models\search\GroupsSearch;
use common\models\search\LoansSearch;
use common\models\SocialAppraisal;
use phpDocumentor\Reflection\DocBlock\Description;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;

use Yii;


class GroupsController extends RestController
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
                        'list' => ['GET'],
                        'pendinggroups' => ['GET'],
                        'completedgroups' => ['GET'],
                        'searchmember' => ['GET', 'POST'],
                        'info' => ['GET'],
                        'create' => ['POST'],
                        'update' => ['PUT'],
                        'view' => ['GET'],
                        'delete' => ['DELETE'],
                        'deletegroupmember' => ['DELETE']
                    ],
                ],

            ];
    }

    public function actionPendinggroups()
    {
        $params = $_GET;
        $searchModel = new GroupsSearch();
        //$response = $searchModel->searchApi($params);
        $params['search']['status'] = "pending";

        $response = $searchModel->searchApiStatus($params);
        if($response['info']['totalCount'] > 0){
            $response['data'] = ApiParser::parseGroups($response['data']);
            return $this->sendSuccessResponse(200,$response['data'],$response['info']);
        }
        else{
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionCompletedgroups()
    {
        $params = $_GET;
        $searchModel = new GroupsSearch();
        //$response = $searchModel->searchApi($params);
        $params['search']['status'] = "completed";

        $response = $searchModel->searchApiStatus($params);
        if($response['info']['totalCount'] > 0){
            $response['data'] = ApiParser::parseGroups($response['data']);
            return $this->sendSuccessResponse(200,$response['data'],$response['info']);
        }
        else{
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionSearchmember()
    {
        $params = $_GET;
        $search = Yii::$app->getRequest()->getQueryParam('search');
        if(isset($search)){
            $params=$search;
        }
        $loan = Applications::find()->select(['members.id', 'members.cnic'])
            ->joinWith('member')
            ->joinWith('loan')
            ->where(['cnic' => $params['cnic'],'loans.status' => "collected"])->one();

        if(!isset($loan) && empty($loan)) {
            $applications = Applications::find()
                ->join('inner join', 'application_actions', 'application_actions.parent_id=applications.id')
                ->join('inner join', 'members', 'members.id=applications.member_id')
                ->leftJoin('loans', 'loans.application_id = applications.id')
                ->where(['is', 'loans.application_id', null])
                ->andWhere(['applications.status' => 'approved', 'application_actions.action' => 'approved/rejected', 'application_actions.status' => '1'])
                ->andWhere(['>=', 'application_actions.expiry_date', date('Y-m-d H:i:s')])
                ->andWhere(['members.cnic' => $params['cnic']])
                ->all();
            if(!empty($applications)) {
                $response['data'] = ApiParser::parseApplicationMembers($applications);
                return $this->sendSuccessResponse(200,$response['data']);
            } else {
                return $this->sendFailedResponse(204, "Record not found");
            }
        } else {
            return $this->sendFailedResponse(204, "Record not found");
        }

    }

    public function actionIndex()
    {
        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $searchModel = new GroupsSearch();
        $data = $searchModel->searchApi($params);
        $response = [];
        if($data['info']['totalCount'] > 0 || $data['info']['totalCount'] > 0){
            $groups = ApiParser::parseGroupsBasicInfo($data['data']);
            foreach ($groups as $group) {
                $group = array_merge($group, ['members'=> GroupHelper::getGroupMembers($group['id'])]);
                $group = array_merge($group, ['guarantors'=> GroupHelper::getGaurantors($group['id'])]);
                $group_records[] = $group;
            }
            $response = [
                'groups' => $group_records,
                'page' => $data['info']['page'],
                'size' => $data['info']['size'],
                'total_count' => $data['info']['totalCount'],
                'total_records' => $data['info']['totalRecords']
            ];
            return $this->sendSuccessResponse(200,$response);
        }
        else{
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionProcessedlist()
    {
        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $params['is_lock'] = 1;
        $searchModel = new GroupsSearch();
        //$response_data = $searchModel->searchApi($params);
        $response_data = $searchModel->searchApiProcessed($params);
        if($response_data['info']['totalCount'] > 0){
            $res_data['data'] = ApiParser::parseGroups($response_data['data']);
            $data =[];
            foreach ($res_data['data'] as $grp_data) {
                $data[] = array_merge($grp_data, ['members_count'=> GroupHelper::getGroupMembersCount($grp_data['id'])]);
            }

            $response = [
                'groups' => $data,
                'page' => $response_data['info']['page'],
                'size' => $response_data['info']['size'],
                'total_count' => $response_data['info']['totalCount'],
                'total_records' => $response_data['info']['totalRecords']
               // 'info' => $response_data['info']['info']
                //'total_count' => 5
            ];
            return $this->sendSuccessResponse(200,$response);
        }
        else{
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionPendinglist()
    {

        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $searchModel = new GroupsSearch();
        $response_data = $searchModel->searchApiPending($params);
        $group_records = [];
        if($response_data['info']['totalCount'] > 0){
            $groups = ApiParser::parseGroups($response_data['data']);
            foreach ($groups as $group) {
                $group['members_count'] = GroupHelper::getGroupMembersCount($group['id']);
                $group_records[] = $group;
            }

        $response = [
            'groups' => $group_records,
            'page' => $response_data['info']['page'],
            'size' => $response_data['info']['size'],
            'total_count' => $response_data['info']['totalCount'],
            'total_records' => $response_data['info']['totalRecords'],
            'info' => $response_data['info']['info']
            //'total_count' => 5
        ];
        /*print_r($response_data['data']);
        die();*/
        //$params['status'] = "approved";
        /*$applications = Applications::find()
            ->leftJoin('loans','loans.application_id = applications.id')
            ->leftJoin('groups','groups.id = applications.group_id')
            ->where(['is', 'loans.application_id', null])
            ->andWhere(['applications.status'=>'approved'])
            ->andWhere(['<>','applications.group_id','0'])

            //->all();

        if(!empty($applications)){
            $groups = GroupHelper::getGroupsListFromApplications($applications);
            $response_data['info']['totalCount'] = count($groups);
            $response_data['data'] = ApiParser::parseGroups($groups);
            $data =[];
            foreach ($response_data['data'] as $grp_data) {
                $grp_data['members_count'] = GroupHelper::getGroupMembersCount($grp_data['id']);
                $data[] = $grp_data;
            }
            $response = [
                'groups' => $data,
                'page' => $response_data['info']['page'],
                'size' => $response_data['info']['size'],
                'total_count' => $response_data['info']['totalCount']
                //'total_count' => 5
            ];*/
            return $this->sendSuccessResponse(200,$response);
        }
        else{
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionPendinglisthousing()
    {

        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $searchModel = new GroupsSearch();
        $response_data = $searchModel->searchApiPendingHousing($params);
        $group_records = [];
        if($response_data['info']['totalCount'] > 0){
            $groups = ApiParser::parseGroups($response_data['data']);
            foreach ($groups as $group) {
                $group['members_count'] = GroupHelper::getGroupMembersCount($group['id']);
                $group_records[] = $group;
            }

            $response = [
                'groups' => $group_records,
                'page' => $response_data['info']['page'],
                'size' => $response_data['info']['size'],
                'total_count' => $response_data['info']['totalCount'],
                'total_records' => $response_data['info']['totalRecords'],
            ];
            return $this->sendSuccessResponse(200,$response);
        }
        else{
            return $this->sendFailedResponse(204, "Record not found");
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
                    $action_model->expiry_date = strtotime('+10 days', strtotime(date('Y-m-d H:i:s')));
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

    public function actionRecommendedlist()
    {

        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $searchModel = new GroupsSearch();
        $response_data = $searchModel->searchApiPendingHousing($params);
        $group_records = [];
        if($response_data['info']['totalCount'] > 0){
            $groups = ApiParser::parseGroups($response_data['data']);
            foreach ($groups as $group) {
                $group['members_count'] = GroupHelper::getGroupMembersCount($group['id']);
                $group_records[] = $group;
            }

            $response = [
                'groups' => $group_records,
                'page' => $response_data['info']['page'],
                'size' => $response_data['info']['size'],
                'total_count' => $response_data['info']['totalCount'],
                'total_records' => $response_data['info']['totalRecords'],
            ];
            return $this->sendSuccessResponse(200,$response);
        }
        else{
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionPending()
    {
        $params = $_GET;
        $params['rbac_type'] = $this->rbac_type;
        $params['controller'] = Yii::$app->controller->id;
        $params['method'] = Yii::$app->controller->action->id;
        $params['user_id'] = UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $params['is_lock'] = 1;
        $searchModel = new GroupsSearch();
        $response_data = $searchModel->searchApiPendingDisbursement($params);
        /*print_r($response);
        die();*/
        if($response_data['info']['totalCount'] > 0){
            $data =[];
            foreach ($response_data['data'] as $app_data) {
                $data1 = ApiParser::parseGroup($app_data);
                $data1 = array_merge($data1, ['members'=> GroupHelper::getGroupMembers($app_data['id'])]);
                $data[] = array_merge($data1, ['members_count'=> GroupHelper::getGroupMembersCount($app_data['id'])]);
            }
            $response = [
                'groups' => $data,
                'page' => $response_data['info']['page'],
                'size' => $response_data['info']['size'],
                'total_count' => $response_data['info']['totalCount'],
                'total_records' => $response_data['info']['totalRecords']
            ];
            return $this->sendSuccessResponse(200,$response);
        }
        else{
            return $this->sendFailedResponse(204, "Record not found");
        }
    }

    public function actionInfo($id)
    {
        $model = $this->findModel($id);
        $response['members'] = GroupHelper::getGroupMembers($model->id);
        return $this->sendSuccessResponse(200,$response);
    }

    public function actionCreate()
    {
        $application_ids = $this->request['application_id'];
        $serial_no = 0;
        if(count($application_ids) != 1 && (count($application_ids) < 3 || count($application_ids) > 6)){
            return $this->sendFailedResponse(400, "Group Size is not valid");
        } else {
            foreach ($application_ids as $application_id){
                $application = Applications::find()->where(['id' => $application_id, 'deleted' => 0, 'status' => 'approved'])->one();
                if(!isset($application))
                {
                    return $this->sendFailedResponse(400, "Application Ids not Approved.");
                } else {
                    $branch_ids[] = $application->branch_id;
                }
            }
            if(count(array_unique($branch_ids)) === 1){
                $branch = Branches::find()->select(['code'])->where(['id' => $branch_ids[0]])->one();
                $model = new Groups();
                //$model->grp_type = $this->request['grp_type'];
                if(count($application_ids) == 1)
                {
                    $model->grp_type = 'IND' ;
                } else {
                    $model->grp_type = 'GRP';
                }

                $group = Groups::find()->select(['br_serial'])->where(['branch_id' => $application->branch_id, 'grp_type' => $model->grp_type])->orderBy('br_serial' .' DESC')->one();
                if(isset($group))
                {
                    $serial_no = $group->br_serial;
                }

                if(isset($this->request['group_name']))
                {
                    $model->group_name = $this->request['group_name'];
                }
                $model->grp_no = $model->grp_type . '-' . $branch->code . '-'. str_pad($serial_no +1, 5, '0', STR_PAD_LEFT);
                $model->region_id = $application->region_id;
                $model->area_id = $application->area_id;
                $model->branch_id = $application->branch_id;
                $model->team_id = $application->team_id;
                $model->field_id = $application->field_id;
                $model->br_serial = $serial_no + 1;
                $model->group_size = count($application_ids);

                $transaction = Yii::$app->db->beginTransaction();
                /*$model->save();
                print_r($model->getErrors());
                die();*/
                try {
                    if ($flag = $model->save()) {

                        if($model->grp_type == "IND")
                        {
                            if(isset($this->request['guarantors'])) {
                                foreach ($this->request['guarantors'] as $guarantor) {
                                    $loan = Applications::find()->select(['members.id', 'members.cnic'])
                                        ->joinWith('member')
                                        ->joinWith('loan')
                                        ->where(['cnic' => $guarantor['cnic'],'loans.status' => "collected"])->one();
                                    /*$member = Members::find()->select(['id', 'cnic'])->where(['cnic' => $guarantor['cnic']])->one();
                                    $application = Applications::find()->where(['member_id' => $member->id])->one();
                                    $loan = Loans::find()->where(['application_id' => $application->id, 'status' => "collected"])->one();*/

                                    if (!isset($loan)) {
                                        $guarantor_model = new Guarantors();
                                        $guarantor_model->attributes = $guarantor;
                                        $guarantor_model->phone = '92'.ltrim($guarantor_model->phone,'0');
                                        $guarantor_model->platform = 2;
                                        $guarantor_model->group_id = $model->id;

                                        if (!($flag = $guarantor_model->save())) {
                                            $transaction->rollBack();
                                            return $this->sendFailedResponse(400, $guarantor_model->getErrors());
                                        } else {
                                            if (isset($guarantor['cnic_front'])) {
                                                $base_code_cnic_front = $guarantor['cnic_front'];
                                                $cnic_front_image_name = 'cnic_front' . rand(111111, 999999) . '.png';
                                                if (!(ImageHelper::imageUpload($guarantor_model->id, 'guarantors', 'cnic_front', $cnic_front_image_name, $base_code_cnic_front))) {
                                                    $transaction->rollBack();
                                                    return $this->sendFailedResponse(400, "CNIC Front Image Not Saved.");
                                                }
                                            }

                                            if (isset($guarantor['cnic_back'])) {
                                                $base_code_cnic_back =$guarantor['cnic_back'];
                                                $cnic_back_image_name = 'cnic_back_' . rand(111111, 999999) . '.png';
                                                if (!(ImageHelper::imageUpload($guarantor_model->id, 'guarantors', 'cnic_back', $cnic_back_image_name, $base_code_cnic_back))) {
                                                    $transaction->rollBack();
                                                    return $this->sendFailedResponse(400, "CNIC Back Image Not Saved.");
                                                }
                                            }
                                        }
                                    } else {
                                        $transaction->rollBack();
                                        return $this->sendFailedResponse(400, 'Guarantor have active loan So Group not generated.');
                                    }
                                }
                            } else {
                                $transaction->rollBack();
                                return $this->sendFailedResponse(400, 'Group not generate without guarantors');
                            }

                        }

                    }

                    if ($flag) {
                        foreach ($application_ids as $application_id)
                        {
                            $application_model = Applications::findOne(['id' => $application_id]);
                            $application_model->group_id = $model->id;
                            $application_model->is_lock = 1;
                            $application_model->save();
                            $ba_model = AppraisalsBusiness::findOne(['application_id' => $application_id]);
                            $ba_model->is_lock = 1;
                            $ba_model->save();
                            $prev_action_model = ApplicationActions::findOne(['parent_id' => $application_model->id, 'action' => 'approved/rejected']);
                            $action_model = ApplicationActions::findOne(['parent_id' => $application_model->id, 'action' => 'group_formation']);
                            if(!isset($action_model))
                            {
                                $transaction->rollBack();
                                return $this->sendFailedResponse(400,"Approval of application must be required before group formation");
                            } else {
                                $action_model->status = 1;
                                $action_model->pre_action = $prev_action_model->id;
                                $action_model->expiry_date = strtotime('+10 days',strtotime( date('Y-m-d H:i:s')));
                                $action_model->save();

                            }
                        }
                        ActionsHelper::insertActions('group',0,$model->id,$model->created_by);
                        $action_model = new GroupActions();
                        $action_model->parent_id = $model->id;
                        $action_model->user_id = $model->created_by;
                        $action_model->action = "lac";
                        $action_model->save();

                        $action_model = new GroupActions();
                        $action_model->parent_id = $model->id;
                        $action_model->user_id = $model->created_by;
                        $action_model->action = "fund_request";
                        $action_model->save();

                        $transaction->commit();
                        $response = ApiParser::parseGroup($model);
                        //$response['message'] = "Group against ". $model->grp_no ." generate successfully";
                        $response['members'] = GroupHelper::getGroupMembers($model->id);
                        $response['guarantors'] = GroupHelper::getGaurantors($model->id);

                        return $this->sendSuccessResponse(201, $response);
                    } else {
                        $transaction->rollBack();
                        return $this->sendFailedResponse(400,$model->errors);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            } else {
                return $this->sendFailedResponse(400, "Group members should belong to same branch");
            }
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model->is_locked == 0) {
            $application_ids = $this->request['application_id'];

            $applications = Applications::find()->where(['group_id' => $id, 'deleted' => 0])->all();
            foreach ($applications as $application) {
                if(isset($application->loan))
                {
                    if($application->loan->sanction_no == null && !in_array($application->id, $application_ids))
                    {
                        $loan = Loans::findOne(['id' => $application->loan->id]);
                        $loan->deleted = 1;
                        $loan->save();
                    }
                }
                $application->group_id = 0;
                $application->save();
                $application_action = ApplicationActions::find()->where(['parent_id' => $application->id])->andWhere(['action' => 'group_formation'])->one();
                $application_action->status = 0;
                $application_action->expiry_date = 0;
                $application_action->save();
            }

            if(count($application_ids) != 1 && (count($application_ids) < 3 || count($application_ids) > 6)){
                return $this->sendFailedResponse(400, "Group Size is not valid");
            } else {
                foreach ($application_ids as $application_id){
                    $application = Applications::find()->where(['id' => $application_id, 'deleted' => 0, 'status' => 'approved'])->one();
                    if(!isset($application))
                    {
                        return $this->sendFailedResponse(400, "Application Ids not Approved.");
                    } else {
                        $branch_ids[] = $application->branch_id;
                    }
                }
                if(count(array_unique($branch_ids)) === 1){
                    if(isset($this->request['group_name']))
                    {
                        $model->group_name = $this->request['group_name'];
                    }

                    $model->group_size = count($application_ids);
                    $model->status = 'pending';
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save()) {

                            if($model->grp_type == "IND")
                            {
                                if(isset($this->request['guarantors'])) {
                                    $guarantors = Guarantors::find()->where(['group_id' => $model->id,'deleted' => 0])->all();
                                    if(isset($guarantors))
                                    {
                                        foreach ($guarantors as $g)
                                        {
                                            $g->deleted = 1;
                                            $g->save();
                                        }
                                    }
                                    foreach ($this->request['guarantors'] as $guarantor) {
                                        $loan = Applications::find()->select(['members.id', 'members.cnic'])
                                            ->joinWith('member')
                                            ->joinWith('loan')
                                            ->where(['cnic' => $guarantor['cnic'],'loans.status' => "collected"])->one();

                                        if (!isset($loan)) {
                                            $guarantor_model = new Guarantors();
                                            $guarantor_model->attributes = $guarantor;
                                            $guarantor_model->group_id =  $model->id;

                                            if (!($flag = $guarantor_model->save())) {
                                                $transaction->rollBack();
                                                return $this->sendFailedResponse(400, $guarantor_model->getErrors());
                                            } else {
                                                if (isset($guarantor['cnic_front'])) {
                                                    $base_code_cnic_front = $guarantor['cnic_front'];
                                                    $cnic_front_image_name = 'cnic_front' . rand(111111, 999999) . '.png';
                                                    if (!(ImageHelper::imageUpload($guarantor_model->id, 'guarantors', 'cnic_front', $cnic_front_image_name, $base_code_cnic_front))) {
                                                        $transaction->rollBack();
                                                        return $this->sendFailedResponse(400, "CNIC Front Image Not Saved.");
                                                    }
                                                }

                                                if (isset($guarantor['cnic_back'])) {
                                                    $base_code_cnic_back =$guarantor['cnic_back'];
                                                    $cnic_back_image_name = 'cnic_back_' . rand(111111, 999999) . '.png';
                                                    if (!(ImageHelper::imageUpload($guarantor_model->id, 'guarantors', 'cnic_back', $cnic_back_image_name, $base_code_cnic_back))) {
                                                        $transaction->rollBack();
                                                        return $this->sendFailedResponse(400, "CNIC Back Image Not Saved.");
                                                    }
                                                }
                                            }
                                        } else {
                                            $transaction->rollBack();
                                            return $this->sendFailedResponse(400, 'Guarantor have active loan So Group not generated.');
                                        }
                                    }
                                } else {
                                    $transaction->rollBack();
                                    return $this->sendFailedResponse(400, 'Group not generate without guarantors');
                                }
                            }
                        }

                        if ($flag) {
                            foreach ($application_ids as $application_id)
                            {
                                $application_model = Applications::findOne(['id' => $application_id]);
                                $application_model->group_id = $model->id;
                                $application_model->save();
                                $prev_action_model = ApplicationActions::findOne(['parent_id' => $application_model->id, 'action' => 'approved/rejected']);
                                $action_model = ApplicationActions::findOne(['parent_id' => $application_model->id, 'action' => 'group_formation']);
                                if(!isset($action_model))
                                {
                                    $transaction->rollBack();
                                    return $this->sendFailedResponse(400,"Approval of application must be required before group formation");
                                } else {
                                    $action_model->status = 1;
                                    $action_model->pre_action = $prev_action_model->id;
                                    $action_model->save();

                                    $group_action = GroupActions::findOne(['parent_id' => $model->id]);
                                    if(!isset($group_action)) {
                                        $action_model = new GroupActions();
                                        $action_model->parent_id = $model->id;
                                        $action_model->user_id = $model->created_by;
                                        $action_model->action = "lac";
                                        $action_model->save();

                                        $action_model = new GroupActions();
                                        $action_model->parent_id = $model->id;
                                        $action_model->user_id = $model->created_by;
                                        $action_model->action = "fund_request";
                                        $action_model->save();
                                    }

                                }
                            }
                            $transaction->commit();
                            $response = ApiParser::parseGroup($model);
                            $response['members'] = GroupHelper::getGroupMembers($model->id);
                            $response['guarantors'] = GroupHelper::getGaurantors($model->id);

                            return $this->sendSuccessResponse(200, $response);
                        } else {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400,$model->errors);
                        }
                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                } else {
                    return $this->sendFailedResponse(400, "Group members should belong to same branch");
                }
            }

        } else {
            return $this->sendFailedResponse(400, "Group is locked.");
        }
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $response = ApiParser::parseGroup($model);
        $response['members'] = GroupHelper::getGroupMembers($id);
        $response['guarantors'] = GroupHelper::getGaurantors($id);

        return $this->sendSuccessResponse(200,$response);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->is_locked == 0) {
            $model->deleted = 1;
            if ($model->save()) {

                $group_members = GroupHelper::getGroupMembers($id);
                $group_gurantors = GroupHelper::getGaurantors($id);
                $applications = Applications::find()->where(['group_id' => $model->id])->all();
                foreach ($applications as $application) {
                    $application->group_id = 0;
                    $application->save();
                    $application_action = ApplicationActions::find()->where(['parent_id' => $application->id])->andWhere(['action' => 'group_formation'])->one();
                    $application_action->status = 0;
                    $application_action->expiry_date = 0;
                    $application_action->save();
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
            return $this->sendFailedResponse(400,"Group is locked.");
        }
    }

    public function actionDeletegroupmember($id)
    {
        $model = $this->findModel($id);
        if($model->is_locked == 0) {
            $application_ids = $this->request['application_id'];
            foreach ($application_ids as $application_id)
            {
                $application = Applications::find()->where(['id' => $application_id, 'deleted' => 0])->one();
                $application->group_id = 0;
                $application->save();
            }
            $response = ApiParser::parseGroup($model);
            $response['members'] = GroupHelper::getGroupMembers($id);
            $response['guarantors'] = GroupHelper::getGaurantors($id);
            return $this->sendSuccessResponse(200,$response);
        } else {
            return $this->sendFailedResponse(400,"Group is locked.");
        }
    }

    protected function findModel($id)
    {
        if (($model = Groups::findOne(['id' => $id,'deleted' =>0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }
}