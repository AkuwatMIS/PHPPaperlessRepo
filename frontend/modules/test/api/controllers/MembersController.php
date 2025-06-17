<?php

namespace frontend\modules\test\api\controllers;


use common\components\Helpers\CodeHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\LogsHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\UsersHelper;
use common\components\Parsers\ApiParser;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\Images;
use common\models\Loans;
use common\models\Members;
use common\models\MembersAddress;
use common\models\MembersEmail;
use common\models\MembersPhone;
use common\models\search\LoansSearch;
use common\models\search\MembersSearch;
use common\models\Users;
use frontend\modules\test\api\models\Member;
use yii\db\Exception;
use yii\filters\AccessControl;
use frontend\modules\test\api\models\Employee;
use frontend\modules\test\api\behaviours\Verbcheck;
use frontend\modules\test\api\behaviours\Apiauth;

use Yii;


class MembersController extends RestController
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
                        'syncmembers' => ['POST'],
                        'update' => ['PUT'],
                        'bulkupdate' => ['PUT'],
                        'view' => ['GET'],
                        'details' => ['GET'],
                        'delete' => ['DELETE'],
                        'memberverification' => ['GET']
                    ],
                ],

            ];
    }

    public function actionIndex()
    {
        $params = $_GET;
        $searchModel = new MembersSearch();
        $response = $searchModel->searchApi($params);
        if(!empty($response['data'])){
            $response['data'] = ApiParser::parseMembers($response['data']);
            $data = [];
            foreach ($response['data'] as  $member_data)
            {
                $member_data = array_merge($member_data,MemberHelper::getMemberImages($member_data['id']));
                $member_data['logs'] = ApiParser::parseLogs(LogsHelper::getLogs("members",$member_data['id']));
                $data[] = $member_data;
            }
            return $this->sendSuccessResponse(200,$data, $response['info']);
        }else{
            return $this->sendFailedResponse(204, "Record not found");
        }

    }

    public function actionMemberverification($cnic)
    {
        $loan = MemberHelper::getActiveLoanFromCNIC($cnic);

        if (isset($loan) && !empty($loan)) {
            return $this->sendFailedResponse(400, 'Active Loan Exist');
        } else {
            return $this->sendSuccessResponse(200, "Record not found");
        }
    }

    public function actionSyncfamilymember(){

        $member = $this->request;
        /*print_r($member);
        die();*/
        $model = Members::findOne(['id' => $member['id'], 'deleted' => 0]);
        $model->attributes = $member;

        if(isset($model)) {
            $model->attributes = $member;
            $members=MemberHelper::preConditionsMember($model);
            if (!empty($members->getErrors())) {
                return $this->sendFailedResponse(401, $members->getErrors());
            }
            /*$loan = MemberHelper::getActiveLoanFromCNIC($model->family_member_cnic);
            if (isset($loan)) {
                return $this->sendFailedResponse(401, 'Active Loan Exist Against Sanction No. ' .$loan->sanction_no);
            }*/
            else {


                if (isset($this->request['family_member_cnic_front']) && isset($this->request['family_member_cnic_back'])) {
                    $base_code1 = $this->request['family_member_cnic_front'];
                    $base_code2 = $this->request['family_member_cnic_back'];
                    $image_name1 = 'family_member_cnic_front_' . rand(111111, 999999) . '.png';
                    $image_name2 = 'family_member_cnic_back_' . rand(111111, 999999) . '.png';
                    if (!(ImageHelper::imageUpload($model->id, 'members', 'family_member_cnic_front', $image_name1, $base_code1))) {
                        return $this->sendFailedResponse(400, "Family Member CNIC Front not Saved");
                    }
                    if (!(ImageHelper::imageUpload($model->id, 'members', 'family_member_cnic_back', $image_name2, $base_code2))) {
                        return $this->sendFailedResponse(400, "Family Member CNIC Back not Saved");
                    }
                }

                $application = Applications::find()->leftJoin('loans', 'loans.application_id = applications.id')
                    ->where(['member_id' => $member['id']])
                    ->andWhere(['<>','applications.status', 'rejected'])
                    ->andWhere(['is', 'loans.application_id', null])->one();
                $application->is_biometric = 1;
                if (!$application->save()) {
                    return $this->sendFailedResponse(401, $model->getErrors());
                }

                if (!$model->save() && !$application->save()) {
                    return $this->sendFailedResponse(401, $model->getErrors());
                }

                $action_model = ApplicationActions::findOne(['parent_id' => $application->id, 'action' => 'family_member_info']);

                $action_model->status = 1;
                $action_model->expiry_date = strtotime('+3 months', strtotime(date('Y-m-d H:i:s')));
                $action_model->save();

                return $this->sendSuccessResponse(200, array('message' => 'Family Member Details Saved Successfully'));
            }
        }
    }

    public function actionSyncmembers()
    {
        $members = $this->request;
        $errors = [];
        $success = [];
        $success_response = [];
        $data = [];
        foreach ($members as $member) {
            $error = '<ul>';
            $model = Members::findOne(['cnic' => $member['cnic'], 'deleted' => 0]);
            $headers = Yii::$app->getRequest()->getHeaders();
            $access_token = $headers->get('x-access-token');
            $user = Users::findIdentityByAccessToken($access_token);
            if(isset($model)) {
                $model->attributes = $member;
                if (!empty($member)) {
                    /*if($model->created_by != $user->id) {
                        $model->region_id = isset($user->region->obj_id) ? $user->region->obj_id : 0;
                        $model->area_id = isset($user->area->obj_id) ? $user->area->obj_id : 0;
                        $model->branch_id = isset($user->branch->obj_id) ? $user->branch->obj_id : 0;
                        $model->team_id = isset($user->team->obj_id) ? $user->team->obj_id : 0;
                        $model->field_id = isset($user->field->obj_id) ? $user->field->obj_id : 0;
                    }*/
                    $members=MemberHelper::preConditionsMember($model);
                    if (!empty($members->getErrors())) {
                        $error .= ApiParser::parseErrors($members->getErrors());
                    }
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save()) {

                            if (isset($member['mobile'])) {
                                $memberPhone = new MembersPhone();
                                $memberPhone->member_id = $model->id;
                                $memberPhone->is_current = 1;
                                $memberPhone->phone_type = 'mobile';
                                $memberPhone->phone = '92'.ltrim($member['mobile'],'0');
                                $phone_save=MemberHelper::saveMemberPhone($memberPhone);
                                if(!$phone_save){
                                    $error .= ApiParser::parseErrors($phone_save);
                                }
                            }

                            if (isset($member['email'])) {
                                $memberEmail = new MembersEmail();
                                $memberEmail->email = $member['email'];
                                $memberEmail->member_id = $model->id;
                                $email_save=MemberHelper::saveMemberEmail($memberEmail);
                                if(!$email_save){
                                    $error .= ApiParser::parseErrors($email_save);
                                }
                            }
                            if (isset($member['home_address'])) {
                                $memberAddress = new MembersAddress();
                                $memberAddress->member_id = $model->id;
                                $memberAddress->is_current = 1;
                                $memberAddress->address_type = "home";
                                $memberAddress->address = $member['home_address'];
                                $address_save=MemberHelper::saveMemberAddress($memberAddress);
                                if(!$address_save){
                                    $error .= ApiParser::parseErrors($address_save);
                                }
                            }

                            if (isset($member['business_address'])) {
                                $memberAddress = new MembersAddress();
                                $memberAddress->member_id = $model->id;
                                $memberAddress->is_current = 1;
                                $memberAddress->address_type = "business";
                                $memberAddress->address = $member['business_address'];
                                $address_save=MemberHelper::saveMemberAddress($memberAddress);
                                if(!$address_save){
                                    $error .= ApiParser::parseErrors($address_save);
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();
                            $success[] = ['id' => $model->id, 'temp_id' => $member['temp_id'], 'status' => $model->full_name ."'s record has synced successfully" ,'cnic' => $model->cnic];
                            $success_response[] = $member['temp_id'];
                        } else {
                            $transaction->rollBack();
                            $error .= ApiParser::parseErrors($model->errors);
                        }

                    } catch (Exception $e) {
                        $transaction->rollBack();
                    }
                } else {
                    $error .= ApiParser::parseErrors($model->errors);
                }


            } else {
                if (!isset($member['mobile']) || empty($member['mobile'])) {
                    $error .= ApiParser::parseErrors("Mobile cannot be blank");
                } else if (!isset($member['business_address']) || empty($member['business_address'])) {
                    $error .= ApiParser::parseErrors("Business Address cannot be blank");
                } else if (!isset($member['home_address']) || empty($member['home_address'])) {
                    $error .= ApiParser::parseErrors("Home Address cannot be blank");
                } else {
                    $model = new Members();
                    $model->attributes = $member;
                    $model->platform = 2;

                    if (!isset($user->region->obj_id) || !isset($user->area->obj_id) || !isset($user->branch->obj_id) || !isset($user->team->obj_id) || !isset($user->field->obj_id)) {
                        $error .= ApiParser::parseErrors("User Structure Mapping not set");
                    } else {
                        $model->region_id = isset($user->region->obj_id) ? $user->region->obj_id  :0;
                        $model->area_id = isset($user->area->obj_id) ? $user->area->obj_id :0;
                        $model->branch_id = isset($user->branch->obj_id) ? $user->branch->obj_id :0;
                        $model->team_id = isset($user->team->obj_id) ? $user->team->obj_id :0;
                        $model->field_id = isset($user->field->obj_id) ? $user->field->obj_id :0;
                        $members=MemberHelper::preConditionsMember($model);
                        if (!empty($members->getErrors())) {
                            $error .= ApiParser::parseErrors($members->getErrors());
                        }
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            if ($flag = $model->save()) {
                                $memberPhone = new MembersPhone();
                                $memberPhone->member_id = $model->id;
                                $memberPhone->is_current = 1;
                                $memberPhone->phone_type = 'mobile';
                                $memberPhone->phone = '92'.ltrim($member['mobile'],'0');
                                $phone_save=MemberHelper::saveMemberPhone($memberPhone);
                                if(!$phone_save) {
                                    $error .= ApiParser::parseErrors($phone_save);
                                }

                                if (isset($member['email']) && !empty($member['email'])) {
                                    $memberEmail = new MembersEmail();
                                    $memberEmail->email = $member['email'];
                                    $memberEmail->member_id = $model->id;
                                    $email_save=MemberHelper::saveMemberEmail($memberEmail);
                                    if(!$email_save){
                                        $transaction->rollBack();
                                        $error .= ApiParser::parseErrors($email_save);
                                    }
                                }

                                $memberAddress = new MembersAddress();
                                $memberAddress->member_id = $model->id;
                                $memberAddress->is_current = 1;
                                $memberAddress->address_type = "home";
                                $memberAddress->address = $member['home_address'];
                                $address_save=MemberHelper::saveMemberAddress($memberAddress);
                                if(!$address_save){
                                    $transaction->rollBack();
                                    $error .= ApiParser::parseErrors($address_save);
                                }

                                $memberAddress = new MembersAddress();
                                $memberAddress->member_id = $model->id;
                                $memberAddress->is_current = 1;
                                $memberAddress->address_type = "business";
                                $memberAddress->address = $member['business_address'];
                                $address_save=MemberHelper::saveMemberAddress($memberAddress);
                                if(!$address_save){
                                    $transaction->rollBack();
                                    $error .= ApiParser::parseErrors($address_save);
                                }
                            }

                            if ($flag) {
                                $transaction->commit();
                                $success[] = ['id' => $model->id, 'temp_id' => $member['temp_id'], 'status' => $model->full_name."'s record has synced successfully", 'cnic' =>$model->cnic];
                                $success_response[] = $member['temp_id'];
                            } else {
                                $transaction->rollBack();
                                Yii::$app->db->createCommand('ALTER TABLE members auto_increment = 1')->execute();
                                $error .= ApiParser::parseErrors($model->errors);
                            }

                        } catch (Exception $e) {
                            $transaction->rollBack();
                        }
                    }
                }
            }
            $error .= '</ul>';
            if (!in_array($member['temp_id'], $success_response)) {
                $errors[] = ['temp_id' => $member['temp_id'], 'cnic' => $member['cnic'], 'error' => $error];
            }
        }
        if(empty($success) && !empty($errors)){
            $data['response_status'] = 'error';
        }else if(!empty($success) && empty($errors)){
            $data['response_status'] = 'success';
        }else{
            $data['response_status'] = 'warning';
        }

        $data['success'] = $success;
        $data['errors'] = $errors;
        return $this->sendSuccessResponse(201, $data);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = $this->request;
        if (!empty($this->request)) {
            if (isset($this->request['dob'])) {
                //$model->dob = strtotime($model->dob);
            }
            if (isset($this->request['profile_pic'])) {
                $base_code_profile = $this->request['profile_pic'];
                $profile_image_name = 'profile_' . rand(111111, 999999) . '.png';
                $model->profile_pic = $profile_image_name;
            }

            if (isset($this->request['cnic_front'])) {
                $base_code_cnic_front = $this->request['cnic_front'];
                $cnic_front_image_name = 'cnic_front_' . rand(111111, 999999) . '.png';
            }

            if (isset($this->request['cnic_back'])) {
                $base_code_cnic_back = $this->request['cnic_back'];
                $cnic_back_image_name = 'cnic_back_' . rand(111111, 999999) . '.png';
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($flag = $model->save()) {
                    if (isset($this->request['profile_pic'])) {
                        if (!(ImageHelper::imageUpload($model->id, 'members', 'profile', $profile_image_name, $base_code_profile))) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, "Profile Picture not update.");
                        }
                    }
                    if (isset($this->request['cnic_front'])) {
                        if (!(ImageHelper::imageUpload($model->id, 'members', 'cnic_front', $cnic_front_image_name, $base_code_cnic_front))) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, "CNIC Front not saved.");
                        }
                    }
                    if (isset($this->request['cnic_back'])) {
                        if (!(ImageHelper::imageUpload($model->id, 'members', 'cnic_back', $cnic_back_image_name, $base_code_cnic_back))) {
                            $transaction->rollBack();
                            return $this->sendFailedResponse(400, "CNIC Back not saved.");
                        }
                    }

                    if (isset($this->request['phone'])) {

                        $phone_models = MembersPhone::find()->where(['member_id' => $id, 'phone_type' => 'phone'])->all();
                        foreach ($phone_models as $phone_model) {
                            $phone_model->is_current = 0;
                            $phone_model->save();
                        }
                        $phone_model = new MembersPhone();
                        $phone_model->phone_type = 'phone';
                        $phone_model->phone = $this->request['phone'];
                        $phone_model->member_id = $model->id;
                        if (!($flag = $phone_model->save())) {
                            return $this->sendFailedResponse(400, $phone_model->errors);
                        }
                    }

                    if (isset($this->request['mobile'])) {

                        $phone_models = MembersPhone::find()->where(['member_id' => $id, 'phone_type' => 'mobile'])->all();
                        foreach ($phone_models as $phone_model) {
                            $phone_model->is_current = 0;
                            $phone_model->save();
                        }
                        $phone_model = new MembersPhone();
                        $phone_model->phone_type = 'mobile';
                        $phone_model->phone = $this->request['mobile'];
                        $phone_model->member_id = $model->id;
                        if (!($flag = $phone_model->save())) {
                            return $this->sendFailedResponse(400, $phone_model->errors);
                        }
                    }

                    if (isset($this->request['email'])) {
                        if ($model->membersEmail->email != $this->request['email']) {
                            $email_models = MembersEmail::find()->where(['member_id' => $id])->all();

                            foreach ($email_models as $email_model) {
                                $email_model->is_current = 0;
                                $email_model->save();
                            }

                            $email_model = new MembersEmail();
                            $email_model->email = $this->request['email'];
                            $email_model->member_id = $model->id;
                            //print_r($email_model->validate());
                            //$email_model->save();

                            if (!($flag = $email_model->save())) {
                                return $this->sendFailedResponse(400, $email_model->errors);
                            }
                        }
                    }
                    if (isset($this->request['home_address'])) {

                        $address_models = MembersAddress::find()->where(['member_id' => $id, 'address_type' => "home"])->all();
                        foreach ($address_models as $address_model) {
                            $address_model->is_current = 0;
                            $address_model->save();
                        }


                        $address_model = new MembersAddress();
                        $address_model->address_type = "home";
                        $address_model->address = $this->request['home_address'];
                        $address_model->member_id = $model->id;
                        if (!($flag = $address_model->save())) {
                            return $this->sendFailedResponse(400, $address_model->errors);
                        }
                    }

                    if (isset($this->request['business_address'])) {

                        $address_models = MembersAddress::find()->where(['member_id' => $id, 'address_type' => "business"])->all();
                        foreach ($address_models as $address_model) {
                            $address_model->is_current = 0;
                            $address_model->save();
                        }


                        $address_model = new MembersAddress();
                        $address_model->address_type = "business";
                        $address_model->address = $this->request['business_address'];
                        $address_model->member_id = $model->id;
                        if (!($flag = $address_model->save())) {
                            return $this->sendFailedResponse(400, $address_model->errors);
                        }
                    }
                }

                if ($flag) {
                    $transaction->commit();
                    $response = ApiParser::parseMember($model);
                    $response['profile_pic'] = ApiParser::parseImage(MemberHelper::getProfileImage($model->id), $model->id);
                    $response['cnic_front'] = ApiParser::parseImage(MemberHelper::getFCNIC($model->id), $model->id);
                    $response['cnic_back'] = ApiParser::parseImage(MemberHelper::getBCNIC($model->id), $model->id);
                    return $this->sendSuccessResponse(200, $response);
                } else {
                    $transaction->rollBack();
                    return $this->sendFailedResponse(400, $model->errors);
                }

            } catch (Exception $e) {
                $transaction->rollBack();
            }
        } else {
            return $this->sendFailedResponse(400, "Empty request not allowed.");
        }

    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $response = ApiParser::parseMember($model);
        $response['logs'] = ApiParser::parseLogs(LogsHelper::getLogs("members",$model->id));
        return $this->sendSuccessResponse(200,$response);
    }

    public function actionDetails($key, $value)
    {
        $params['cnic'] = isset($value) ? $value : '';
        if($key == "application_id")
        {
            $member = Members::findOne(['cnic' => $value,'deleted' => 0]);
            if(!isset($member))
            {
                return $this->sendFailedResponse(204, "Invalid Record requested");
            }
            else {
                $application = Applications::findOne(['member_id' => $member->id, 'deleted' => 0]);
                if (!isset($application)) {
                    return $this->sendFailedResponse(204, "Invalid Record requested");
                } else {
                    $application = ApiParser::parseApplication($application);
                    $response['application'] = CodeHelper::getKeyValue($application);
                    return $this->sendSuccessResponse(200, $response);
                }
            }
        }

        if($key == "loan_id")
        {
            $searchModel = new LoansSearch();
            $search_member = $searchModel->searchGlobal($params);
            if($search_member['info']['totalCount'] > 0) {
                $response['message'] = "Get Members Details";
                $response['members'] = ApiParser::parseLoansSearchResult($search_member['data']);
                return $this->sendSuccessResponse(200, $response);
            } else {
                return $this->sendFailedResponse(204, "Record not found");
            }

        }

    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleted = 1;
        $model->deleted_by = Yii::$app->user->getId();
        $model->deleted_at = strtotime(date('Y-m-d'));
        if ($model->save()) {
            $response = ApiParser::parseMember($model);
            $response['profile_pic'] = ApiParser::parseImage(MemberHelper::getProfileImage($model->id), $model->id);
            $response['cnic_front'] = ApiParser::parseImage(MemberHelper::getFCNIC($model->id), $model->id);
            $response['cnic_back'] = ApiParser::parseImage(MemberHelper::getBCNIC($model->id), $model->id);
            $response['logs'] = ApiParser::parseLogs(LogsHelper::getLogs("members",$model->id));
            return $this->sendSuccessResponse(200,$response);
        } else {
            return $this->sendFailedResponse(204,"Enable to delete record.");
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