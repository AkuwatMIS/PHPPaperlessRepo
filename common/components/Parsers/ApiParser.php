<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Parsers;


use common\components\Helpers\ActionsHelper;
use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\AppraisalsHelper;
use common\components\Helpers\CodeHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\ListHelper;
use common\components\Helpers\LoanHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\RecoveriesHelper;
use common\components\Helpers\StructureHelper;
use common\models\ApplicationDetails;
use common\models\Applications;
use common\models\AppraisalsBusiness;
use common\models\AppraisalsEmergency;
use common\models\AppraisalsHousing;
use common\models\Branches;
use common\models\EmergencyLoans;
use common\models\Images;
use common\models\Loans;
use common\models\Members;
use common\models\Provinces;
use common\models\Recoveries;
use common\models\SocialAppraisal;
use common\models\VigaLoans;
use common\models\Visits;
use yii\helpers\Url;

class ApiParser
{

    public static function housingAppraisal($id){

        $location =  AppraisalsHousing::find()->select(['latitude','longitude'])->where(['application_id'=>$id])->one();

        if($location->longitude == 0 || $location->latitude == 0) {
            $location = Visits::find()->select(['latitude', 'longitude'])->where(['parent_type' => 'application' ,'parent_id' => $id])->one();
        }
        return $location;
    }
    public static function memberLocation($id){

        $location =  SocialAppraisal::find()->select(['latitude','longitude'])->where(['application_id'=>$id])->one();
        if(empty($location)) {
            $location = AppraisalsBusiness::find()->select(['latitude', 'longitude'])->where(['application_id' => $id])->one();
        }
        if(empty($location)) {
            $location = AppraisalsEmergency::find()->select(['latitude', 'longitude'])->where(['application_id' => $id])->one();
        }
        if(empty($location)) {
            $location = Visits::find()->select(['latitude', 'longitude'])->where(['parent_type' => 'application' ,'parent_id' => $id])->one();
        }
        return $location;

    }

    public static function parseMembers($members)
    {
        if (!empty($members)) {
            $array = [];
            foreach ($members as $member) {

                $loan_status = Applications::find()->select(['loans.status'])->leftJoin('loans','loans.application_id = applications.id')->where(['member_id'=> $member->id])->orderBy('applications.id desc')->one();

                $hp = self::housingAppraisal($member->applications[0]['id']);
                if(empty($hp)){
                    $hp = self::memberLocation($member->applications[0]['id']);
                }
                $array[] = array(
                    'id' => $member->id,
                    'full_name' => isset($member->full_name) ? $member->full_name : "",
                    'parentage' => isset($member->parentage) ? $member->parentage : "",
                    'parentage_type' => isset($member->parentage_type) ? $member->parentage_type : "",
                    'cnic' => isset($member->cnic) ? $member->cnic : "",
                    'gender' => isset($member->gender) ? $member->gender : "",
                    'dob' => isset($member->dob) && $member->dob != 0 ? date('d-F-Y', $member->dob) : "",
                    'education' => isset($member->education) ? $member->education : "",
                    'marital_status' => isset($member->marital_status) ? $member->marital_status : "",
                    'family_no' => isset($member->family_no) ? $member->family_no : "",
                    'family_member_name' => isset($member->family_member_name) ? $member->family_member_name : "",
                    'family_member_cnic' => isset($member->family_member_cnic) ? $member->family_member_cnic : "",
                    'religion' => isset($member->religion) ? $member->religion : "",
                    'is_disable' => isset($member->is_disable) ? $member->is_disable : 0,
                    'disability_type' => isset($member->disability_type) ? $member->disability_type : "",
                    /*'profile_pic' =>  isset($member->profile_pic) ? $member->profile_pic : "",*/
                    'mobile' => isset($member->membersMobile->phone) ? '0' . ltrim($member->membersMobile->phone, '92') : "",
                    //'mobile' => isset($member->membersMobile->phone) ? $member->membersMobile->phone : "",
                    'phone' => isset($member->membersPtcl->phone) ? $member->membersPtcl->phone : "",
                    'email' => isset($member->membersEmail->email) ? $member->membersEmail->email : "",
                    'business_address' => isset($member->businessAddress->address) ? $member->businessAddress->address : "",
                    'home_address' => isset($member->homeAddress->address) ? $member->homeAddress->address : "",
                    'longitude' =>(!empty($hp)) ? $hp->longitude : "",
                    'latitude' =>(!empty($hp)) ? $hp->latitude : "",
                    'status' => isset($member->status) ? $member->status : "",
                    'left_index' => isset($member->left_index) ? $member->left_index : "",
                    'right_index' => isset($member->right_index) ? $member->right_index : "",
                    'left_thumb' => isset($member->left_thumb) ? $member->left_thumb : "",
                    'right_thumb' => isset($member->right_thumb) ? $member->right_thumb : "",
                    'cnic_issue_date' => isset($member->info->cnic_issue_date) ? date('d-F-Y',strtotime($member->info->cnic_issue_date)) : "1-Jan-1970",
                    'cnic_expiry_date' => isset($member->info->cnic_expiry_date) ? date('d-F-Y',strtotime($member->info->cnic_expiry_date))  : "1-Jan-1970",
                    'mother_name' => isset($member->info->mother_name) ? $member->info->mother_name : "",
                    'account_no' => isset($member->memberAccount->account_no) ? $member->memberAccount->account_no : "",
                    'bank_name' => isset($member->memberAccount->bank_name) ? $member->memberAccount->bank_name : "",
                    'title' =>isset($member->memberAccount->title) ? $member->memberAccount->title : "",
                    'loan_status' =>isset($loan_status->status) ? $loan_status->status : "",
                );
                /* if($data['profile_pic'] != null) {
                     $image = Images::findOne(['parent_id' => $member->id, 'parent_type' => 'members']);
                     $data['profile_pic'] = ApiParser::parseImage($image, $member->id);
                 }
                 $array[] = $data ;*/
            }
            return $array;
        }
        return $members;
    }

    public static function parseVerificationMember($application, $status = 'pending')
    {
        $member = isset($application->member) ? $application->member : '';
        if (!empty($member)) {
            $array = array(
                'id' => $member->id,
                'application_id' => $application->id,
                'full_name' => isset($member->full_name) ? $member->full_name : "",
                'cnic' => isset($member->cnic) ? $member->cnic : "",
                'mobile' => isset($member->membersMobile->phone) ? $member->membersMobile->phone : "",
                'verification_status' => $status,
                'req_amount' => round($application->req_amount),
                'parentage' => $application->member->parentage,
                'parentage_type' => $application->member->parentage_type,
                'gender' => $application->member->gender,
                'application_no' => $application->application_no,
            );
            return $array;
        }
        return $member;
    }

    public static function parseVerificationMembers($applications, $status = 'pending')
    {
        $array = array();
        if (!empty($applications)) {
            foreach ($applications as $key => $application) {
                $member = isset($application->member) ? $application->member : '';
                $array[$key] = array(
                    'id' => $member->id,
                    'application_id' => $application->id,
                    'full_name' => isset($member->full_name) ? $member->full_name : "",
                    'cnic' => isset($member->cnic) ? $member->cnic : "",
                    'mobile' => isset($member->membersMobile->phone) ? $member->membersMobile->phone : "",
                    'verification_status' => $status,
                    'req_amount' => round($application->req_amount),
                    'parentage' => $application->member->parentage,
                    'parentage_type' => $application->member->parentage_type,
                    'gender' => $application->member->gender,
                    'application_no' => $application->application_no,
                );
                $array[$key] = array_merge($array[$key], MemberHelper::getMemberProfileImage($member->id));
            }
            return $array;
        }
        return $array;
    }

    public static function parseTakafulInfo($loans)
    {
        $array = array();
        if (!empty($loans)) {
            foreach ($loans as $loan) {
                $array = array(
                    'id' => $loan->id,
                    'full_name' => isset($loan->application->member->full_name) ? $loan->application->member->full_name : "",
                    'mobile' => isset($loan->application->member->membersMobile->phone) ? $loan->application->member->membersMobile->phone : "",
                    'parentage' => isset($loan->application->member->parentage) ? $loan->application->member->parentage : "",
                    'cnic' => isset($loan->application->member->cnic) ? $loan->application->member->cnic : "",
                    'profile_pic' => self::parseImage(MemberHelper::getProfileImage($loan->application->member->id), $loan->application->member->id),
                    'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : "",
                    'loan_amount' => isset($loan->loan_amount) ? $loan->loan_amount : "",
                    //'date_disbursed' => isset($loan->date_disbursed) ? date('Y-m-d', $loan->date_disbursed) : 0,
                    'balance' => isset($loan->balance) ? $loan->balance : "",
                );
            }
            return $array;
        }
        return $loans;
    }

    public static function parseTakafulInfoList($loans)
    {
        $array = array();
        if (!empty($loans)) {
            foreach ($loans as $loan) {
                $array[] = array(
                    'id' => $loan->id,
                    'full_name' => isset($loan->application->member->full_name) ? $loan->application->member->full_name : "",
                    'mobile' => isset($loan->application->member->membersMobile->phone) ? $loan->application->member->membersMobile->phone : "",
                    'parentage' => isset($loan->application->member->parentage) ? $loan->application->member->parentage : "",
                    'cnic' => isset($loan->application->member->cnic) ? $loan->application->member->cnic : "",
                    'profile_pic' => self::parseImage(MemberHelper::getProfileImage($loan->application->member->id), $loan->application->member->id),
                    'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : "",
                    'loan_amount' => isset($loan->loan_amount) ? $loan->loan_amount : "",
                    //'date_disbursed' => isset($loan->date_disbursed) ? date('Y-m-d', $loan->date_disbursed) : 0,
                    'balance' => isset($loan->balance) ? $loan->balance : "",
                );
            }
            return $array;
        }
        return $loans;
    }

    public static function parseMember($member)
    {
        if (!empty($member)) {
            $array = array(
                'id' => $member->id,
                'full_name' => isset($member->full_name) ? $member->full_name : "",
                'parentage' => isset($member->parentage) ? $member->parentage : "",
                'parentage_type' => isset($member->parentage_type) ? $member->parentage_type : "",
                'cnic' => isset($member->cnic) ? $member->cnic : "",
                'gender' => isset($member->gender) ? $member->gender : "",
                'dob' => isset($member->dob) && $member->dob != 0 ? date('d-F-Y', $member->dob) : "",
                'education' => isset($member->education) ? $member->education : "",
                'marital_status' => isset($member->marital_status) ? $member->marital_status : "",
                'family_no' => isset($member->family_no) ? $member->family_no : "",
                'family_member_name' => isset($member->family_member_name) ? $member->family_member_name : "",
                'family_member_cnic' => isset($member->family_member_cnic) ? $member->family_member_cnic : "",
                'religion' => isset($member->religion) ? $member->religion : "",
                'is_disable' => isset($member->is_disable) ? $member->is_disable : 0,
                'profile_pic' => self::parseImage(MemberHelper::getProfileImage($member->id), $member->id),
                'disability_type' => isset($member->disability_type) ? $member->disability_type : "",
                'mobile' => isset($member->membersMobile->phone) ? $member->membersMobile->phone : "",
                'phone' => isset($member->membersPtcl->phone) ? $member->membersPtcl->phone : "",
                'email' => isset($member->membersEmail->email) ? $member->membersEmail->email : "",
                'business_address' => isset($member->businessAddress->address) ? $member->businessAddress->address : "",
                'home_address' => isset($member->homeAddress->address) ? $member->homeAddress->address : "",
                'status' => isset($member->status) ? $member->status : "",
                'left_index' => isset($member->left_index) ? $member->left_index : "",
                'right_index' => isset($member->right_index) ? $member->right_index : "",
                'left_thumb' => isset($member->left_thumb) ? $member->left_thumb : "",
                'right_thumb' => isset($member->right_thumb) ? $member->right_thumb : "",
                'family_member_cnic_front' => self::parseImage(MemberHelper::getFamilyMemberCNICFrontImage($member->id), $member->id),
                'family_member_cnic_back' => self::parseImage(MemberHelper::getFamilyMemberCNICBackImage($member->id), $member->id),
            );

            return $array;
        }
        return $member;
    }

    public static function parseMemberAll($member)
    {
        if (!empty($member)) {
            $array = array(
                'id' => $member->id,
                'full_name' => isset($member->full_name) ? $member->full_name : "",
                'parentage' => isset($member->parentage) ? $member->parentage : "",
                'parentage_type' => isset($member->parentage_type) ? $member->parentage_type : "",
                'cnic' => isset($member->cnic) ? $member->cnic : "",
                'gender' => isset($member->gender) ? $member->gender : "",
                'dob' => isset($member->dob) && $member->dob != 0 ? date('d-F-Y', $member->dob) : "",
                'education' => isset($member->education) ? $member->education : "",
                'marital_status' => isset($member->marital_status) ? $member->marital_status : "",
                'family_no' => isset($member->family_no) ? $member->family_no : "",
                'family_member_name' => isset($member->family_member_name) ? $member->family_member_name : "",
                'family_member_cnic' => isset($member->family_member_cnic) ? $member->family_member_cnic : "",
                'religion' => isset($member->religion) ? $member->religion : "",
                'is_disable' => isset($member->is_disable) ? $member->is_disable : 0,
                'profile_pic' => self::parseImage(MemberHelper::getProfileImage($member->id), $member->id),
                'disability_type' => isset($member->disability_type) ? $member->disability_type : "",
                'mobile' => isset($member->membersMobile->phone) ? $member->membersMobile->phone : "",
                'phone' => isset($member->membersPtcl->phone) ? $member->membersPtcl->phone : "",
                'email' => isset($member->membersEmail->email) ? $member->membersEmail->email : "",
                'business_address' => isset($member->businessAddress->address) ? $member->businessAddress->address : "",
                'home_address' => isset($member->homeAddress->address) ? $member->homeAddress->address : "",
                'status' => isset($member->status) ? $member->status : "",
            );

            return $array;
        }
        return $member;
    }

    public static function parseMemberBasic($member)
    {
        if (!empty($member)) {
            $array = array(
                'id' => $member->id,
                'full_name' => isset($member->full_name) ? $member->full_name : "",
                'parentage' => isset($member->parentage) ? $member->parentage : "",
                'parentage_type' => isset($member->parentage_type) ? $member->parentage_type : "",
                'cnic' => isset($member->cnic) ? $member->cnic : "",
                'gender' => isset($member->gender) ? $member->gender : "",
                'profile_pic' => self::parseImage(MemberHelper::getProfileImage($member->id), $member->id),
            );

            return $array;
        }
        return $member;
    }

    public static function parseMemberPushNotification($member)
    {
        if (!empty($member)) {
            $array = array(
                'id' => $member->id,
                'cnic' => isset($member->cnic) ? $member->cnic : "",
            );

            return $array;
        }
        return $member;
    }

    public static function parseApplicationInfo($application)
    {
        $array = [];
        if (!empty($application)) {
            $array = array(
                'id' => $application->id,
                'full_name' => isset($application->member->full_name) ? $application->member->full_name : "",
                'cnic' => isset($application->member->cnic) ? $application->member->cnic : "",
                'profile_pic' => self::parseImage(MemberHelper::getProfileImage($application->member->id), $application->member->id),
                'application_no' => isset($application->application_no) ? $application->application_no : "",
                'req_amount' => isset($application->req_amount) ? round($application->req_amount) : 0,
                'recommended_amount' => isset($application->recommended_amount) ? round($application->recommended_amount) : 0,
                'client_contribution'=>isset($application->client_contribution)?$application->client_contribution:0,
                'project_id' => isset($application->project_id) ? $application->project_id : 0,
            );

            return $array;
        }
        return $array;
    }

    public static function parseMemberBasicInfo($member)
    {
        if (!empty($member)) {
            $array = array(
                'id' => $member->id,
                'full_name' => isset($member->full_name) ? $member->full_name : "",
                'parentage' => isset($member->parentage) ? $member->parentage : "",
                'parentage_type' => isset($member->parentage_type) ? $member->parentage_type : "",
                'cnic' => isset($member->cnic) ? $member->cnic : "",
                'gender' => isset($member->gender) ? $member->gender : "",
                'profile_pic' => self::parseImage(MemberHelper::getProfileImage($member->id), $member->id),
                'left_index' => isset($member->left_index) ? $member->left_index : '',
                'right_index' => isset($member->right_index) ? $member->right_index : '',
                'left_thumb' => isset($member->left_thumb) ? $member->left_thumb : '',
                'right_thumb' => isset($member->right_thumb) ? $member->right_thumb : '',
            );

            return $array;
        }
        return $member;
    }

    public static function parseGuarantor($gaurantor)
    {
        if (!empty($gaurantor)) {
            $array = array(
                'id' => $gaurantor->id,
                'group_id' => isset($gaurantor->group_id) ? $gaurantor->group_id : 0,
                'name' => isset($gaurantor->name) ? $gaurantor->name : "",
                'parentage' => isset($gaurantor->parentage) ? $gaurantor->parentage : "",
                'cnic' => isset($gaurantor->cnic) ? $gaurantor->cnic : "",
                'phone' => isset($gaurantor->phone) ? '0' . ltrim($gaurantor->phone, '92') : "",
                'address' => isset($gaurantor->address) ? $gaurantor->address : "",
                'left_thumb' => isset($gaurantor->left_thumb) ? $gaurantor->left_thumb : "",
            );

            return $array;
        }
        return $gaurantor;
    }

    public static function parseDevice($device)
    {
        $array = array();
        if (!empty($device)) {
            $array = [
                'id' => $device->id,
                'uu_id' => $device->uu_id,
                'imei_no' => $device->imei_no,
                'os_version' => $device->os_version,
                'device_model' => $device->device_model,
            ];
        }
        return $array;
    }

    public static function parseImageData($data)
    {
        $array = array();
        if (!empty($data)) {
            $array = [
                'temp_id' => $data->temp_id,
                'parent_id' => $data->parent_id,
                'parent_type' => $data->parent_type,
                'image_type' => $data->image_type,
            ];
        }
        return $array;
    }

    public static function parseLogs($logs)
    {
        if (!empty($logs)) {
            $array = [];
            foreach ($logs as $log) {
                $array[] = array(
                    //'id' => $log->id,
                    'field' => isset($log->field) ? $log->field : "",
                    'old_value' => isset($log->old_value) ? $log->old_value : "",
                    'new_value' => isset($log->new_value) ? $log->new_value : "",
                    'stamp' => isset($log->stamp) ? date('d-F-Y', $log->stamp) : "",
                    'user_id' => isset($log->user_id) ? $log->user->username : "",
                );
            }
            return $array;
        }
        return $logs;
    }

    public static function parseLog($log)
    {
        if (!empty($log)) {

            $array = array(
                //'id' => $log->id,
                //'field' => isset($log->field) ? $log->field : "",
                'old_value' => isset($log->old_value) ? $log->old_value : "",
                'new_value' => isset($log->new_value) ? $log->new_value : "",
                'changed_date' => isset($log->stamp) ? date('d-M-Y - h:i A', $log->stamp) : "",
                'changed_by' => isset($log->user_id) ? $log->user->username : "",
            );
            return $array;
        }
        return $log;
    }

    public static function parseFundRequests($fund_requests)
    {
        if (!empty($fund_requests)) {
            $array = [];
            foreach ($fund_requests as $fund_request) {

                $array[] = array(
                    'id' => $fund_request->id,
                    'requested_amount' => isset($fund_request->requested_amount) ? $fund_request->requested_amount : 0,
                    'approved_amount' => $fund_request->approved_amount,
                    'total_loans' => isset($fund_request->total_loans) ? $fund_request->total_loans : 0,
                    'region_id' => isset($fund_request->region_id) ? $fund_request->region_id : 0,
                    'area_id' => isset($fund_request->area_id) ? $fund_request->area_id : 0,
                    'branch_id' => isset($fund_request->branch_id) ? $fund_request->branch_id : 0,
                    'status' => isset($fund_request->status) ? $fund_request->status : 0,
                    'approved_by' => isset($fund_request->approved_by) ? $fund_request->approved_by : 0,
                    'approved_on' => $fund_request->approved_on != 0 ? date('d-F-Y', $fund_request->approved_on) : 0,
                );
            }
            return $array;
        }
        return $fund_requests;
    }

    public static function parseFundRequest($fund_request)
    {
        if (!empty($fund_request)) {
            $array = array(
                'id' => $fund_request->id,
                'requested_amount' => isset($fund_request->requested_amount) ? $fund_request->requested_amount : 0,
                'approved_amount' => $fund_request->approved_amount,
                'total_loans' => isset($fund_request->total_loans) ? $fund_request->total_loans : 0,
                'region_id' => isset($fund_request->region_id) ? $fund_request->region_id : 0,
                'area_id' => isset($fund_request->area_id) ? $fund_request->area_id : 0,
                'branch_id' => isset($fund_request->branch_id) ? $fund_request->branch_id : 0,
                'status' => isset($fund_request->status) ? $fund_request->status : 0,
                'approved_by' => isset($fund_request->approved_by) ? $fund_request->approved_by : 0,
                'approved_on' => $fund_request->approved_on != 0 ? date('d-F-Y', $fund_request->approved_on) : 0,
            );

            return $array;
        }
        return $fund_request;
    }

    public static function parseFundRequestDetails($fund_request_details)
    {
        if (!empty($fund_request_details)) {
            $array = [];
            foreach ($fund_request_details as $fund_request_detail) {

                $array[] = array(
                    'id' => $fund_request_detail->id,
                    'branch_id' => isset($fund_request_detail->branch_id) ? $fund_request_detail->branch_id : 0,
                    'project_id' => isset($fund_request_detail->project_id) ? $fund_request_detail->project_id : 0,
                    'project_name' => $fund_request_detail->project->name,
                    'cheque_no' => isset($fund_request_detail->cheque_no) ? $fund_request_detail->cheque_no : "",
                    'fund_request_id' => isset($fund_request_detail->fund_request_id) ? $fund_request_detail->fund_request_id : 0,
                    'total_loans' => isset($fund_request_detail->total_loans) ? $fund_request_detail->total_loans : 0,
                    'total_requested_amount' => isset($fund_request_detail->total_requested_amount) ? $fund_request_detail->total_requested_amount : 0,
                    'total_approved_amount' => $fund_request_detail->total_approved_amount,
                );
            }
            return $array;
        }
        return $fund_request_details;
    }

    public static function parseDisbursements($disbursements)
    {
        if (!empty($disbursements)) {
            $array = [];
            foreach ($disbursements as $disbursement) {
                $array[] = array(
                    'id' => $disbursement->id,
                    'date_disbursed' => isset($disbursement->date_disbursed) ? date('d-F-Y', $disbursement->date_disbursed) : 0,
                    'venue' => isset($disbursement->venue) ? $disbursement->venue : "",
                );

            }
            return $array;
        }
        return $disbursements;
    }

    public static function parseDisbursement($disbursement)
    {
        if (!empty($disbursement)) {
            $array = array(
                'id' => $disbursement->id,
                'date_disbursed' => isset($disbursement->date_disbursed) ? date('d-F-Y', $disbursement->date_disbursed) : 0,
                'venue' => isset($disbursement->venue) ? $disbursement->venue : "",
            );

            return $array;
        }
        return $disbursement;
    }

    public static function parseGroups($groups)
    {
        if (!empty($groups)) {
            $array = [];
            foreach ($groups as $group) {
                $array[] = array(
                    'id' => $group->id,
                    'grp_no' => isset($group->grp_no) ? $group->grp_no : "",
                    'group_name' => isset($group->group_name) ? $group->group_name : "",
                    'grp_type' => isset($group->grp_type) ? $group->grp_type : "",
                    'region_id' => isset($group->region_id) ? $group->region_id : 0,
                    'area_id' => isset($group->area_id) ? $group->area_id : 0,
                    'branch_id' => isset($group->branch_id) ? $group->branch_id : 0,
                    'team_id' => isset($group->team_id) ? $group->team_id : 0,
                    'field_id' => isset($group->status) ? $group->field_id : 0,
                    'status' => isset($group->status) ? $group->status : 0,
                    'is_locked' => $group->is_locked,
                    'created_by' => isset($group->user->username) ? $group->user->username : '',
                    'reject_reason' => isset($group->reject_reason) ? $group->reject_reason : "",
                );
            }
            return $array;
        }
        return $groups;
    }

    public static function parseGroupsBasicInfo($groups)
    {
        if (!empty($groups)) {
            $array = [];
            foreach ($groups as $group) {
                $array[] = array(
                    'id' => $group->id,
                    'grp_no' => isset($group->grp_no) ? $group->grp_no : "",
                    'group_name' => isset($group->group_name) ? $group->group_name : "",
                    'is_locked' => $group->is_locked,
                );
            }
            return $array;
        }
        return $groups;
    }

    public static function parseGroup($group)
    {
        if (!empty($group)) {
            $array = array(
                'id' => $group->id,
                'grp_no' => isset($group->grp_no) ? $group->grp_no : "",
                'group_name' => isset($group->group_name) ? $group->group_name : "",
                'grp_type' => isset($group->grp_type) ? $group->grp_type : "",
                'region_id' => isset($group->region_id) ? $group->region_id : 0,
                'area_id' => isset($group->area_id) ? $group->area_id : 0,
                'branch_id' => isset($group->branch_id) ? $group->branch_id : 0,
                'team_id' => isset($group->team_id) ? $group->team_id : 0,
                'field_id' => isset($group->status) ? $group->field_id : 0,
                'status' => isset($group->status) ? $group->status : 0,
                'is_locked' => $group->is_locked,
                //'created_by' => isset($group->user->username) ? $group->user->username : '',
                'reject_reason' => isset($group->reject_reason) ? $group->reject_reason : "",

            );

            return $array;
        }
        return $group;
    }

    public static function parseUser($user)
    {
        if (!empty($user)) {
            $array = array(
                'id' => $user->id,
                'username' => isset($user->username) ? $user->username : "",
                'fullname' => isset($user->fullname) ? $user->fullname : "",
                'father_name' => isset($user->father_name) ? $user->father_name : "",
                'email' => isset($user->email) ? $user->email : "",
                'cnic' => isset($user->cnic) ? $user->cnic : "",
                'alternate_email' => isset($user->alternate_email) ? $user->alternate_email : "",
                'address' => isset($user->address) ? $user->address : "",
                //'mobile' => isset($user->mobile) ? $user->mobile : "",
                'mobile' => isset($user->mobile) ? '0' . ltrim($user->mobile, '92') : "",
                'joining_date' => isset($user->joining_date) ? date('Y-m-d', $user->joining_date) : "",
                'emp_code' => isset($user->emp_code) ? $user->emp_code : "",
                'team_name' => isset($user->team_name) ? $user->team_name : "",
                //'profile_pic' => "",
                'left_thumb_impression' => isset($user->left_thumb_impression) ? $user->left_thumb_impression : "",
                'right_thumb_impression' => isset($user->right_thumb_impression) ? $user->right_thumb_impression : "",
                'term_and_condition' => $user->term_and_condition,
                'status' => isset($user->status) ? $user->status : 0,
                'area' => isset($user->area->userArea->name) ? $user->area->userArea->name : '',
                'branch' => isset($user->branch->userBranch->name) ? $user->branch->userBranch->name : '',
            );

            return $array;
        }
        return $user;
    }

    public static function parseImage($image, $id)
    {
        if (!empty($image)) {
            $user_image = (!empty($image->image_name)) ? ($image->image_name) : "";
            //$pic_url = $image->parent_type . "/" . $id . "/" . $user_image;
            $pic_url=ImageHelper::getAttachmentApiPath(). '?type='. $image->parent_type . "&id=" . $id . "&file_name=" . $user_image .'&download=true';
            return $pic_url;
        } else {
            return "";
        }
    }
    public static function parseImageWeb($image, $id)
    {
        if (!empty($image)) {
            $user_image = (!empty($image->image_name)) ? ($image->image_name) : "";
            //$pic_url = $image->parent_type . "/" . $id . "/" . $user_image;
            $pic_url=ImageHelper::getAttachmentApiPath(). '?type='. $image->parent_type . "&id=" . $id . "&file_name=" . $user_image .'&download=false';
            return $pic_url;
        } else {
            return "";
        }
    }

    public static function parseVisit($visit,$download,$id)
    {
        $array = array(
            'visit_id' => $visit->id,
            'is_shifted' => ApplicationDetails::getShifted($id),
            'visited_by' => isset($visit->user->role) ?$visit->user->fullname.' ('.$visit->user->role->itemName->description.')' : "",
            'visited_date' => date('Y-m-d',$visit->created_at),
            'notes' => $visit->comments,
            'percentage' => $visit->percent,
            'is_tranche' => $visit->is_tranche,
            'estimated_figures' => $visit->estimated_figures,
            'estimated_completion_time' => $visit->estimated_completion_time,
            'estimated_start_date' => ($visit->estimated_start_date > 0) ? date('Y-m-d', $visit->estimated_start_date): "",
            'images' => ImageHelper::getVisitImages($visit->id,$download)
        );

        return $array;
    }

    public static function parseArea($area)
    {
        if (!empty($area)) {
            $array = array(
                'id' => $area->id,
                'region' => isset($area->region->name) ? $area->region->name : '',
                'name' => $area->name,
                'code' => $area->code,
            );
            return $array;
        }
        return $area;
    }

    public static function parseBranch($branch)
    {
        if (!empty($branch)) {
            $array = array(
                'id' => $branch->id,
                'region' => isset($branch->region->name) ? $branch->region->name : '',
                'area' => isset($branch->area->name) ? $branch->area->name : '',
                'name' => $branch->name,
                'code' => $branch->code,
            );
            return $array;
        }
        return $branch;
    }

    public static function parseTeam($team)
    {
        if (!empty($team)) {
            $array = array(
                'id' => $team->id,
                'name' => $team->name,
            );
            return $array;
        }
        return $team;
    }

    public static function parseField($field)
    {
        if (!empty($field)) {
            $array = array(
                'id' => $field->id,
                'name' => $field->name,
            );
            return $array;
        }
        return $field;
    }

    public static function parseProjects($projects)
    {
        if (!empty($projects)) {
            foreach ($projects as $project) {
                $array[] = array(
                    'index' => $project->project->id,
                    'name' => $project->project->name,
                );
            }
            return self::remove_duplicateKeys("index", $array);
        }
        return $projects;
    }

    public static function remove_duplicateKeys($key, $data)
    {

        $_data = array();

        foreach ($data as $v) {
            if (isset($_data[$v[$key]])) {
                // found duplicate
                continue;
            }
            // remember unique item
            $_data[$v[$key]] = $v;
        }
        // if you need a zero-based array
        // otherwise work with $_data
        $data = array_values($_data);
        return $data;
    }

    public static function parseBranches($branches)
    {
        if (!empty($branches)) {
            foreach ($branches as $branch) {
                $array[] = array(
                    'id' => $branch->id,
                    'region' => isset($branch->region->name) ? $branch->region->name : '',
                    'area' => isset($branch->area->name) ? $branch->area->name : '',
                    'name' => $branch->name,
                    'code' => $branch->code,
                );
            }
            return $array;
        }
        return $branches;
    }

    public static function parseApplications($applications)
    {
        if (!empty($applications)) {
            foreach ($applications as $application) {
                $array[] = array(
                    'id' => $application->id,
                    'name' => $application->member->full_name,
                    'cnic' => $application->member->cnic,
                    'parentage' => $application->member->parentage,
                    'parentage_type' => $application->member->parentage_type,
                    'gender' => $application->member->gender,
                    'marital_status' => $application->member->marital_status,
                    'home_address' => isset($application->member->homeAddress->address) ? $application->member->homeAddress->address : "",
                    'business_address' => isset($application->member->businessAddress->address) ? $application->member->businessAddress->address : "",
                    'mobile' => isset($application->member->membersMobile->phone) ? $application->member->membersMobile->phone : 0,
                    'profile_pic' => self::parseImage(MemberHelper::getProfileImage($application->member->id), $application->member->id),
                    //'profile_pic' =>   base64_encode($application->member->id),
                    'application_no' => isset($application->application_no) ? $application->application_no : '',
                    'project' => isset($application->project->name) ? $application->project->name : '',
                    'project_id' => isset($application->project_id) ? $application->project_id : 0,
                    'activity' => isset($application->activity->name) ? $application->activity->name : '',
                    'activity_id' => isset($application->activity_id) ? $application->activity_id : 0,
                    'product' => isset($application->product->name) ? $application->product->name : '',
                    'req_amount' => isset($application->req_amount) ? number_format($application->req_amount) : 0,
                    'is_lock' => isset($application->is_lock) ? $application->is_lock : 0,
                    'status' => isset($application->status) ? $application->status : 0,
                    'reject_reason' => isset($application->reject_reason) ? $application->reject_reason : '',
                    'comments' => isset($application->comments) ? $application->comments : '',
                    'details' => self::parseApplicationByKeyValueBasic($application),
                    'family_member' => self::parseFamilyMember($application),

                    /* 'member_id' => isset($application->member_id) ? $application->member_id : 0,
                     'fee' => isset($application->fee) ? $application->fee : "",
                     'application_no' => isset($application->application_no) ? $application->application_no : "",
                     'project_id' => isset($application->project_id) ? $application->project_id : 0,
                     'project_table' => isset($application->project_table) ? $application->project_table : "",
                     'activity_id' => isset($application->activity_id) ? $application->activity_id : 0,
                     'product_id' => isset($application->product_id) ? $application->product_id : 0,
                     'group_id' => isset($application->group_id) ? $application->group_id : 0,
                     /*'region_id' => isset($application->region_id) ? $application->region_id : 0,
                     'area_id' => isset($application->area_id) ? $application->area_id : 0,
                     'branch_id' => isset($application->branch_id) ? $application->branch_id : 0,
                     'team_id' => isset($application->team_id) ? $application->team_id : 0,
                     'field_id' => isset($application->field_id) ? $application->field_id : 0,
                     'req_amount' => isset($application->req_amount) ? $application->req_amount : 0,
                     'no_of_times' => isset($application->no_of_times) ? $application->no_of_times : "",
                     'bzns_cond' => isset($application->bzns_cond) ? $application->bzns_cond : "",
                     'who_will_work' => isset($application->who_will_work) ? $application->who_will_work : "",
                     'name_of_other' => isset($application->name_of_other) ? $application->name_of_other : "",
                     'other_cnic' => isset($application->other_cnic) ? $application->other_cnic : "",
                     'is_urban' => isset($application->is_urban) ? $application->is_urban : 1,
                     'status' => isset($application->status) ? $application->status : 0,*/
                );
            }
            return $array;
        }
        return $applications;
    }

    public static function parseFamilyMember($application)
    {
        if (!empty($application)) {
            $array = array(
                'id' => isset($application->member->id) ? $application->member->id : '',
                'name' => isset($application->member->family_member_name) ? $application->member->family_member_name : '',
                'cnic' => isset($application->member->family_member_cnic) ? $application->member->family_member_cnic : '',
                'family_member_cnic_front' => self::parseImage(MemberHelper::getFamilyMemberCNICFrontImage($application->member->id), $application->member->id),
                'family_member_cnic_back' => self::parseImage(MemberHelper::getFamilyMemberCNICBackImage($application->member->id), $application->member->id),
                'is_biometric' => isset($application->is_biometric) ? $application->is_biometric : 0,
            );
            return $array;
        }
        return $application;
    }

    public static function parseLoansTranches($loans)
    {
        if (!empty($loans)) {
            foreach ($loans as $loan) {
                $array[] = array(
                    'id' => $loan->id,
                    'application_id' => $loan->application->id,
                    'full_name' => $loan->application->member->full_name,
                    'profile_pic' =>  self::parseImage(MemberHelper::getProfileImage($loan->application->member->id), $loan->application->member->id),
                    'cnic' => $loan->application->member->cnic,
                    'parentage' => $loan->application->member->parentage,
                    'parentage_type' => $loan->application->member->parentage_type,
                    'project_id' => isset($loan->project_id) ? $loan->project_id : 0,
                    'loan_amount' => isset($loan->loan_amount) ? $loan->loan_amount : 0,
                    'details' => self::parseLoanTranches($loan->tranches),
                    'percent' => LoanHelper::getCompletionPercentage($loan->application_id)
                );
            }
            return $array;
        }
        return $loans;
    }

    public static function parseApplicationDetails($application)
    {
        if (!empty($application)) {

            $array = array(
                'id' => $application->id,
                'name' => $application->member->full_name,
                'cnic' => $application->member->cnic,
                'parentage' => $application->member->parentage,
                'parentage_type' => $application->member->parentage_type,
                'gender' => $application->member->gender,
                'home_address' => isset($application->member->homeAddress->address) ? $application->member->homeAddress->address : "",
                'business_address' => isset($application->member->businessAddress->address) ? $application->member->businessAddress->address : "",
                'mobile' => isset($application->member->membersMobile->phone) ? $application->member->membersMobile->phone : 0,
                'profile_pic' => self::parseImage(MemberHelper::getProfileImage($application->member->id), $application->member->id),
                'application_no' => isset($application->application_no) ? $application->application_no : '',
                'project' => isset($application->project->name) ? $application->project->name : '',
                'activity' => isset($application->activity->name) ? $application->activity->name : '',
                'activity_id' => isset($application->activity_id) ? $application->activity_id : 0,
                'product' => isset($application->product->name) ? $application->product->name : '',
                'req_amount' => isset($application->req_amount) ? number_format($application->req_amount) : 0,
                'is_lock' => isset($application->is_lock) ? $application->is_lock : 0,
                'status' => isset($application->status) ? $application->status : 0,
                'reject_reason' => isset($application->reject_reason) ? $application->reject_reason : '',
                'comments' => isset($application->comments) ? $application->comments : '',
                'details' => self::parseApplicationByKeyValueBasic($application),
                'family_member' => self::parseFamilyMember($application),
            );
            return $array;
        }
        return $application;
    }


    public static function parseApplicationMembers($applications)
    {
        if (!empty($applications)) {
            foreach ($applications as $application) {
                $array = array(
                    'application_id' => $application->id,
                    'full_name' => $application->member->full_name,
                    'cnic' => $application->member->cnic,
                    'parentage' => $application->member->parentage,
                    'parentage_type' => $application->member->parentage_type,
                    'gender' => $application->member->gender,
                    'application_no' => isset($application->application_no) ? $application->application_no : "",
                    'project_id' => isset($application->project_id) ? $application->project_id : 0,
                    'left_index' => isset($application->member->left_index) ? $application->member->left_index : '',
                    'right_index' => isset($application->member->right_index) ? $application->member->right_index : '',
                    'left_thumb' => isset($application->member->left_thumb) ? $application->member->left_thumb : '',
                    'right_thumb' => isset($application->member->right_thumb) ? $application->member->right_thumb : '',
                );
            }
            return $array;
        }
        return $applications;
    }

    public static function parseApplicationBasic($application)
    {
        $array = array(
            'application_id' => $application->id,
            'full_name' => $application->member->full_name,
            'cnic' => $application->member->cnic,
            'parentage' => $application->member->parentage,
            'parentage_type' => $application->member->parentage_type,
            'gender' => $application->member->gender,
            'application_no' => isset($application->application_no) ? $application->application_no : "",
            'project_id' => isset($application->project_id) ? $application->project_id : 0,
            'left_index' => isset($application->member->left_index) ? $application->member->left_index : '',
            'right_index' => isset($application->member->left_index) ? $application->member->left_index : '',
            'left_thumb' => isset($application->member->left_index) ? $application->member->left_index : '',
            'right_thumb' => isset($application->member->left_index) ? $application->member->left_index : '',
        );

        return $array;
    }

    public static function parseApplication($application)
    {
        if (!empty($application)) {
            $array = array(
                'id' => (string)$application->id,
                'member_id' => isset($application->member_id) ? $application->member_id : 0,
                'fee' => isset($application->fee) ? $application->fee : null,
                'application_no' => isset($application->application_no) ? $application->application_no : null,
                'project_id' => isset($application->project_id) ? (string)$application->project_id : null,
                'project_table' => isset($application->project_table) ? $application->project_table : null,
                'activity_id' => isset($application->activity_id) && $application->activity_id != 0 ? (string)$application->activity_id : null,
                'product_id' => isset($application->product_id) ? (string)$application->product_id : null,
                'group_id' => isset($application->group_id) ? (string)$application->group_id : null,
                'req_amount' => isset($application->req_amount) ? number_format($application->req_amount) : null,
                'no_of_times' => isset($application->no_of_times) ? (string)$application->no_of_times : null,
                'bzns_cond' => isset($application->bzns_cond) ? $application->bzns_cond : null,
                'who_will_work' => isset($application->who_will_work) ? $application->who_will_work : null,
                'name_of_other' => isset($application->name_of_other) ? $application->name_of_other : null,
                'other_cnic' => isset($application->other_cnic) ? $application->other_cnic : null,
                'is_urban' => isset($application->is_urban) ? (string)$application->is_urban : 1,
                'is_lock' => isset($application->is_lock) ? (string)$application->is_lock : null,
                'status' => isset($application->status) ? $application->status : null,
            );
            return $array;
        }
        return $application;
    }

    public static function parseApplicationPushNotification($application)
    {
        if (!empty($application)) {
            $array = array(
                'id' => (string)$application->id,
                'member_id' => isset($application->member_id) ? $application->member_id : 0,
                'application_no' => isset($application->application_no) ? $application->application_no : null,
            );
            return $array;
        }
        return $application;
    }

    public static function parseApplicationBasicInfo($application)
    {
        if (!empty($application)) {
            $array = array(
                'id' => $application->id,
                'member_id' => isset($application->member_id) ? $application->member_id : 0,
                'application_no' => isset($application->application_no) ? $application->application_no : "",
                'req_amount' => isset($application->req_amount) ? round($application->req_amount) : 0,
                'recommended_amount' => isset($application->recommended_amount) ? round($application->recommended_amount) : 0,
                'client_contribution'=>isset($application->client_contribution)?$application->client_contribution:0,
                'project_id' => isset($application->project_id) ? $application->project_id : 0,
                'completion_percentage' => isset($application->lastVisit->percent) ? $application->lastVisit->percent : 0,
                'is_shifted' => isset($application->lastVisit->is_shifted) ? $application->lastVisit->is_shifted : 0,
            );
            return $array;
        }
        return $application;
    }

    public static function parseProjectDetail($keys, $project)
    {
        if (!empty($project)) {
            $array = [];
            foreach ($keys as $key) {
                $array[$key] = isset($project->$key) ? $project->$key : "";
            }
            return $array;
        }
        return $project;
    }

    public static function parseProjectDetailArray($keys, $project)
    {
        if (!empty($project)) {
            $array = [];
            foreach ($keys as $key) {
                $array[$key] = isset($project[$key]) ? $project[$key] : "";
            }
            return $array;
        }
        return $project;
    }


    public static function parseSocialAppraisals($social_appraisals)
    {
        if (!empty($social_appraisals)) {
            foreach ($social_appraisals as $social_appraisal) {
                $array[] = array(
                    'id' => $social_appraisal->id,
                    'application_id' => isset($social_appraisal->application_id) ? $social_appraisal->application_id : 0,
                    'poverty_index' => isset($social_appraisal->poverty_index) ? $social_appraisal->poverty_index : "",
                    'house_ownership' => isset($social_appraisal->house_ownership) ? $social_appraisal->house_ownership : "",
                    'house_rent_amount' => isset($social_appraisal->house_rent_amount) ? $social_appraisal->house_rent_amount : 0,
                    'land_size' => isset($social_appraisal->land_size) ? $social_appraisal->land_size : 0,
                    'total_family_members' => isset($social_appraisal->total_family_members) ? $social_appraisal->total_family_members : 0,
                    'no_of_earning_hands' => isset($social_appraisal->no_of_earning_hands) ? $social_appraisal->no_of_earning_hands : 0,
                    'ladies' => isset($social_appraisal->ladies) ? $social_appraisal->ladies : 0,
                    'gents' => isset($social_appraisal->gents) ? $social_appraisal->gents : 0,
                    'source_of_income' => isset($social_appraisal->source_of_income) ? $social_appraisal->source_of_income : "",
                    'total_household_income' => isset($social_appraisal->total_household_income) ? $social_appraisal->total_household_income : 0,
                    'utility_bills' => isset($social_appraisal->utility_bills) ? $social_appraisal->utility_bills : 0,
                    'educational_expenses' => isset($social_appraisal->educational_expenses) ? $social_appraisal->educational_expenses : 0,
                    'medical_expenses' => isset($social_appraisal->medical_expenses) ? $social_appraisal->medical_expenses : 0,
                    'kitchen_expenses' => isset($social_appraisal->kitchen_expenses) ? $social_appraisal->kitchen_expenses : 0,
                    'monthly_savings' => isset($social_appraisal->monthly_savings) ? $social_appraisal->monthly_savings : "",
                    'amount' => isset($social_appraisal->amount) ? $social_appraisal->amount : 0,
                    'date_of_maturity' => isset($social_appraisal->date_of_maturity) && $social_appraisal->date_of_maturity != 0 ? date('d-F-Y', $social_appraisal->date_of_maturity) : "",
                    'other_expenses' => isset($social_appraisal->other_expenses) ? $social_appraisal->other_expenses : 0,
                    'total_expenses' => isset($social_appraisal->total_expenses) ? $social_appraisal->total_expenses : 0,
                    'other_loan' => isset($social_appraisal->other_loan) ? $social_appraisal->other_loan : "",
                    'loan_amount' => isset($social_appraisal->loan_amount) ? $social_appraisal->loan_amount : 0,
                    'economic_dealings' => isset($social_appraisal->economic_dealings) ? $social_appraisal->economic_dealings : "",
                    'social_behaviour' => isset($social_appraisal->social_behaviour) ? $social_appraisal->social_behaviour : "",
                    'latitude' => isset($social_appraisal->latitude) ? $social_appraisal->latitude : 0,
                    'longitude' => isset($social_appraisal->longitude) ? $social_appraisal->longitude : 0,
                    'status' => isset($social_appraisal->status) ? $social_appraisal->status : "",
                );
            }
            return $array;
        }
        return $social_appraisals;
    }


    public static function parseSocialAppraisalWithKeys($social_appraisal)
    {
        $array = new SocialAppraisal();
        if (!empty($social_appraisal)) {
            $array = array(
                'id' => $social_appraisal->id,
                'application_id' => isset($social_appraisal->application_id) ? $social_appraisal->application_id : 0,
                'poverty_index' => isset($social_appraisal->poverty_index) ? $social_appraisal->poverty_index : "",
                'house_ownership' => isset($social_appraisal->house_ownership) ? $social_appraisal->house_ownership : "",
                'house_rent_amount' => isset($social_appraisal->house_rent_amount) ? $social_appraisal->house_rent_amount : 0,
                'land_size' => isset($social_appraisal->land_size) ? $social_appraisal->land_size : 0,
                'total_family_members' => isset($social_appraisal->total_family_members) ? $social_appraisal->total_family_members : 0,
                'no_of_earning_hands' => isset($social_appraisal->no_of_earning_hands) ? $social_appraisal->no_of_earning_hands : 0,
                'ladies' => isset($social_appraisal->ladies) ? $social_appraisal->ladies : 0,
                'gents' => isset($social_appraisal->gents) ? $social_appraisal->gents : 0,
                'source_of_income' => isset($social_appraisal->source_of_income) ? $social_appraisal->source_of_income : "",
                'total_household_income' => isset($social_appraisal->total_household_income) ? $social_appraisal->total_household_income : 0,
                'utility_bills' => isset($social_appraisal->utility_bills) ? $social_appraisal->utility_bills : 0,
                'educational_expenses' => isset($social_appraisal->educational_expenses) ? $social_appraisal->educational_expenses : 0,
                'medical_expenses' => isset($social_appraisal->medical_expenses) ? $social_appraisal->medical_expenses : 0,
                'kitchen_expenses' => isset($social_appraisal->kitchen_expenses) ? $social_appraisal->kitchen_expenses : 0,
                'monthly_savings' => isset($social_appraisal->monthly_savings) ? $social_appraisal->monthly_savings : "",
                'amount' => isset($social_appraisal->amount) ? $social_appraisal->amount : 0,
                'date_of_maturity' => isset($social_appraisal->date_of_maturity) && $social_appraisal->date_of_maturity != 0 ? date('d-F-Y', $social_appraisal->date_of_maturity) : "",
                'other_expenses' => isset($social_appraisal->other_expenses) ? $social_appraisal->other_expenses : 0,
                'total_expenses' => isset($social_appraisal->total_expenses) ? $social_appraisal->total_expenses : 0,
                'other_loan' => isset($social_appraisal->other_loan) ? $social_appraisal->other_loan : "",
                'loan_amount' => isset($social_appraisal->loan_amount) ? $social_appraisal->loan_amount : 0,
                'economic_dealings' => isset($social_appraisal->economic_dealings) ? $social_appraisal->economic_dealings : "",
                'social_behaviour' => isset($social_appraisal->social_behaviour) ? $social_appraisal->social_behaviour : "",
                'latitude' => isset($social_appraisal->latitude) ? $social_appraisal->latitude : 0,
                'longitude' => isset($social_appraisal->longitude) ? $social_appraisal->longitude : 0,
                'status' => isset($social_appraisal->status) ? $social_appraisal->status : "",

            );
            $array = array_merge($array, ['social_appraisal_details' => CodeHelper::getKeyValue($array)]);
            return $array;
        }
        return $array;
    }

    public static function parseBusinessAppraisals($business_appraisals)
    {
        if (!empty($business_appraisals)) {
            foreach ($business_appraisals as $business_appraisal) {
                $array[] = array(
                    'id' => $business_appraisal->id,
                    'application_id' => isset($business_appraisal->application_id) ? $business_appraisal->application_id : 0,
                    'business_type' => isset($business_appraisal->business_type) ? $business_appraisal->business_type : 0,
                    'business' => isset($business_appraisal->business) ? $business_appraisal->business : "",
                    'place_of_business' => isset($business_appraisal->place_of_business) ? $business_appraisal->place_of_business : "",
                    'latitude' => isset($business_appraisal->latitude) ? $business_appraisal->latitude : 0,
                    'longitude' => isset($business_appraisal->longitude) ? $business_appraisal->longitude : 0,
                    'status' => isset($business_appraisal->status) ? $business_appraisal->status : '',
                );
            }
            return $array;
        }
        return $business_appraisals;
    }

    public static function parseBusinessAppraisalComplete($business_appraisal)
    {
        $array = new AppraisalsBusiness();
        if (!empty($business_appraisal)) {
            $array = array(
                'id' => $business_appraisal->id,
                'application_id' => isset($business_appraisal->application_id) ? $business_appraisal->application_id : 0,
                'business_type' => isset($business_appraisal->business_type) ? $business_appraisal->business_type : 0,
                'business' => isset($business_appraisal->business) ? $business_appraisal->business : "",
                'place_of_business' => isset($business_appraisal->place_of_business) ? $business_appraisal->place_of_business : "",
                'latitude' => isset($business_appraisal->latitude) ? $business_appraisal->latitude : 0,
                'longitude' => isset($business_appraisal->longitude) ? $business_appraisal->longitude : 0,
                'status' => isset($business_appraisal->status) ? $business_appraisal->status : '',
                //'ba_details' => AppraisalsHelper::getDetails($business_appraisal->id),
                'ba_fixed_business_assets' => AppraisalsHelper::getFixedBusinessAssets($business_appraisal->id),
                'ba_runninng_capital' => AppraisalsHelper::getRunningCapital($business_appraisal->id),
                'ba_business_expenses' => AppraisalsHelper::getBusinessExpenses($business_appraisal->id),
                'ba_new_required_assets' => AppraisalsHelper::getNewRequiredAssets($business_appraisal->id)
            );
            return $array;
        }
        return $array;
    }

    public static function parseLoansSearchResult($loans)
    {
        if (!empty($loans)) {
            foreach ($loans as $loan) {
                $array[] = array(
                    'id' => $loan->application->member->id,
                    'full_name' => isset($loan->application->member->full_name) ? $loan->application->member->full_name : "",
                    'parentage' => isset($loan->application->member->parentage) ? $loan->application->member->parentage : "",
                    'cnic' => isset($loan->application->member->cnic) ? $loan->application->member->cnic : "",
                    'grp_no' => isset($loan->group->grp_no) ? $loan->group->grp_no : "",
                    'branch' => isset($loan->branch->name) ? $loan->branch->name : "",
                    'project' => isset($loan->project->name) ? $loan->project->name : "",
                    'loan' => self::getLoanInfo($loan),
                );
            }
            return $array;
        }
        return $loans;
    }

    private static function getLoanInfo($loan)
    {
        if (!empty($loan)) {
            $array = array(
                'id' => isset($loan->id) ? $loan->id : 0,
                'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : '',
                'loan_amount' => isset($loan->loan_amount) ? $loan->loan_amount : 0,
                'date_disbursed' => ($loan->date_disbursed != 0) ? date('Y-m-d', $loan->date_disbursed) : 0,
                'status' => isset($loan->status) ? $loan->status : '',
            );
            return $array;
        } else {
            $array = array(
                'id' => 0
            );
            return $array;
        }
    }

    public static function parseBaDetails($ba_details)
    {
        if (!empty($ba_details)) {
            $array = array(
                'id' => $ba_details->id,
                'ba_id' => $ba_details->ba_id,
                'application_id' => isset($ba_details->application_id) ? $ba_details->application_id : 0,
                'business_income' => isset($ba_details->business_income) ? $ba_details->business_income : 0,
                'job_income' => isset($ba_details->job_income) ? $ba_details->job_income : 0,
                'house_rent_income' => isset($ba_details->house_rent_income) ? $ba_details->house_rent_income : 0,
                'other_income' => isset($ba_details->other_income) ? $ba_details->other_income : 0,
                'total_income' => isset($ba_details->total_income) ? $ba_details->total_income : 0,
                'expected_increase_in_income' => isset($ba_details->expected_increase_in_income) ? $ba_details->expected_increase_in_income : 0,
                'description' => isset($ba_details->description) ? $ba_details->description : '',
            );
            return $array;
        }
        return $ba_details;
    }

    public static function parseAssets($ba_assets)
    {
        if (!empty($ba_assets)) {
            $array = [];
            foreach ($ba_assets as $ba_asset) {

                $array[] = array(
                    'id' => $ba_asset->id,
                    'ba_id' => $ba_asset->ba_id,
                    'application_id' => $ba_asset->application_id,
                    'assets_list' => isset($ba_asset->assets_list) ? $ba_asset->assets_list : "",
                    'total_amount' => isset($ba_asset->total_amount) ? $ba_asset->total_amount : 0,
                    'type' => isset($ba_asset->type) ? $ba_asset->type : '',
                );
            }
            return $array;
        }
        return $ba_assets;
    }

    public static function parseFixedBusinessAssets($fixed_business_assets)
    {
        if (!empty($fixed_business_assets)) {
            $array = [];
            foreach ($fixed_business_assets as $fixed_business_asset) {

                $array[] = array(
                    'id' => $fixed_business_asset->id,
                    'ba_id' => $fixed_business_asset->ba_id,
                    'application_id' => $fixed_business_asset->application_id,
                    'assets' => isset($fixed_business_asset->assets) ? $fixed_business_asset->assets : "",
                    'quantity' => isset($fixed_business_asset->quantity) ? $fixed_business_asset->quantity : 0,
                    'existing_price' => isset($fixed_business_asset->existing_price) ? $fixed_business_asset->existing_price : 0,
                );
            }
            return $array;
        }
        return $fixed_business_assets;
    }

    public static function parseRunningCapital($ba_running_capital)
    {
        if (!empty($ba_running_capital)) {
            $array = [];
            foreach ($ba_running_capital as $running_capital) {

                $array[] = array(
                    'id' => $running_capital->id,
                    'ba_id' => $running_capital->ba_id,
                    'application_id' => $running_capital->application_id,
                    'assets' => isset($running_capital->assets) ? $running_capital->assets : "",
                    'quantity' => isset($running_capital->quantity) ? $running_capital->quantity : 0,
                    'purchasing_price' => isset($running_capital->purchasing_price) ? $running_capital->purchasing_price : 0,
                );
            }
            return $array;
        }
        return $ba_running_capital;
    }

    public static function parseNewRequiredAssets($new_required_assets)
    {
        if (!empty($new_required_assets)) {
            $array = [];
            foreach ($new_required_assets as $new_required_asset) {

                $array[] = array(
                    'id' => $new_required_asset->id,
                    'ba_id' => $new_required_asset->ba_id,
                    'application_id' => $new_required_asset->application_id,
                    'assets' => isset($new_required_asset->assets) ? $new_required_asset->assets : "",
                    'quantity' => isset($new_required_asset->quantity) ? $new_required_asset->quantity : 0,
                    'purchasing_price' => isset($new_required_asset->purchasing_price) ? $new_required_asset->purchasing_price : 0,
                );
            }
            return $array;
        }
        return $new_required_assets;
    }

    public static function parseBusinessExpenses($business_expenses)
    {
        if (!empty($business_expenses)) {
            $array = [];
            foreach ($business_expenses as $business_expense) {

                $array[] = array(
                    'id' => $business_expense->id,
                    'ba_id' => $business_expense->ba_id,
                    'application_id' => $business_expense->application_id,
                    'expense_title' => isset($business_expense->expense_title) ? $business_expense->expense_title : "",
                    'quantity' => isset($business_expense->quantity) ? $business_expense->quantity : 0,
                    'expenses_value' => isset($business_expense->expenses_value) ? $business_expense->expenses_value : 0,
                );
            }
            return $array;
        }
        return $business_expenses;
    }

    public static function parseLoans($loans)
    {
        if (!empty($loans)) {
            foreach ($loans as $loan) {
                $array[] = array(
                    'id' => $loan->id,
                    'application_id' => isset($loan->application_id) ? $loan->application_id : 0,
                    'fund_request_id' => isset($loan->fund_request_id) ? $loan->fund_request_id : 0,
                    'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : "",
                    'loan_amount' => isset($loan->loan_amount) ? $loan->loan_amount : 0,
                    'inst_amnt' => isset($loan->inst_amnt) ? $loan->inst_amnt : 0,
                    'inst_type' => isset($loan->inst_type) ? $loan->inst_type : "",
                    'inst_months' => isset($loan->inst_months) ? $loan->inst_months : 0,
                    'project_id' => isset($loan->project_id) ? $loan->project_id : 0,
                    'project_table' => isset($loan->project_table) ? $loan->project_table : "",
                    'date_approved' => isset($loan->date_approved) && $loan->date_approved != 0 ? date('Y-m-d', $loan->date_approved) : 0,
                    'date_disbursed' => isset($loan->date_disbursed) && $loan->date_disbursed != 0 ? date('Y-m-d', $loan->date_disbursed) : 0,
                    'cheque_dt' => isset($loan->cheque_dt) && $loan->cheque_dt != 0 ? date('Y-m-d', $loan->cheque_dt) : 0,
                    'cheque_no' => isset($loan->cheque_no) ? $loan->cheque_no : "",
                    'disbursement_id' => isset($loan->disbursement_id) ? $loan->disbursement_id : 0,
                    'activity_id' => isset($loan->activity_id) ? $loan->activity_id : 0,
                    'product_id' => isset($loan->product_id) ? $loan->product_id : 0,
                    'group_id' => isset($loan->group_id) ? $loan->group_id : 0,
                    'region_id' => isset($loan->region_id) ? $loan->region_id : 0,
                    'area_id' => isset($loan->area_id) ? $loan->area_id : 0,
                    'branch_id' => isset($loan->branch_id) ? $loan->branch_id : 0,
                    'team_id' => isset($loan->team_id) ? $loan->team_id : 0,
                    'field_id' => isset($loan->field_id) ? $loan->field_id : 0,
                    'loan_expiry' => isset($loan->loan_expiry) && $loan->loan_expiry != 0 ? date('Y-m-d', $loan->loan_expiry) : 0,
                    'loan_completed_date' => isset($loan->loan_completed_date) && $loan->loan_expiry != 0 ? date('Y-m-d', $loan->loan_completed_date) : 0,
                    'old_sanc_no' => isset($loan->old_sanc_no) ? $loan->old_sanc_no : "",
                    'remarks' => isset($loan->remarks) ? $loan->remarks : "",
                    'reject_reason' => isset($loan->reject_reason) ? $loan->reject_reason : "",
                    'attendance_status' => isset($loan->activeTranch) && !empty($loan->activeTranch)? $loan->activeTranch->attendance_status : $loan->attendance_status,
                    'due' => isset($loan->due) ? $loan->due : 0,
                    'overdue' => isset($loan->overdue) ? $loan->overdue : 0,
                    'balance' => isset($loan->balance) ? $loan->balance : 0,
                    'status' => isset($loan->status) ? $loan->status : '',
                );
            }
            return $array;
        }
        return $loans;
    }

    public static function parseLoan($loan)
    {
        if (!empty($loan)) {
            $array = array(
                'id' => $loan->id,
                'application_id' => isset($loan->application_id) ? $loan->application_id : 0,
                'fund_request_id' => isset($loan->fund_request_id) ? $loan->fund_request_id : 0,
                'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : "",
                'loan_amount' => isset($loan->loan_amount) ? $loan->loan_amount : 0,
                'inst_amnt' => isset($loan->inst_amnt) ? $loan->inst_amnt : 0,
                'inst_type' => isset($loan->inst_type) ? $loan->inst_type : "",
                'inst_months' => isset($loan->inst_months) ? $loan->inst_months : 0,
                'project_id' => isset($loan->project_id) ? $loan->project_id : 0,
                'project_table' => isset($loan->project_table) ? $loan->project_table : "",
                'date_approved' => isset($loan->date_approved) && $loan->date_approved != 0 ? date('Y-m-d', $loan->date_approved) : 0,
                'date_disbursed' => isset($loan->disbursement) && $loan->disbursement->date_disbursed != 0 ? date('Y-m-d', $loan->disbursement->date_disbursed) : 0,
                'cheque_dt' => isset($loan->cheque_dt) && $loan->cheque_dt != 0 ? date('Y-m-d', $loan->cheque_dt) : 0,
                'cheque_no' => isset($loan->cheque_no) ? $loan->cheque_no : "",
                'disbursement_id' => isset($loan->disbursement_id) ? $loan->disbursement_id : 0,
                'activity_id' => isset($loan->activity_id) ? $loan->activity_id : 0,
                'product_id' => isset($loan->product_id) ? $loan->product_id : 0,
                'group_id' => isset($loan->group_id) ? $loan->group_id : 0,
                'region_id' => isset($loan->region_id) ? $loan->region_id : 0,
                'area_id' => isset($loan->area_id) ? $loan->area_id : 0,
                'branch_id' => isset($loan->branch_id) ? $loan->branch_id : 0,
                'team_id' => isset($loan->team_id) ? $loan->team_id : 0,
                'field_id' => isset($loan->field_id) ? $loan->field_id : 0,
                'loan_expiry' => isset($loan->loan_expiry) && $loan->loan_expiry != 0 ? date('Y-m-d', $loan->loan_expiry) : 0,
                'loan_completed_date' => isset($loan->loan_completed_date) && $loan->loan_expiry != 0 ? date('Y-m-d', $loan->loan_completed_date) : 0,
                'old_sanc_no' => isset($loan->old_sanc_no) ? $loan->old_sanc_no : "",
                'remarks' => isset($loan->remarks) ? $loan->remarks : "",
                'reject_reason' => isset($loan->reject_reason) ? $loan->reject_reason : "",
                'attendance_status' => isset($loan->activeTranch) && !empty($loan->activeTranch)? $loan->activeTranch->attendance_status : $loan->attendance_status,
                'due' => isset($loan->due) ? $loan->due : 0,
                'overdue' => isset($loan->overdue) ? $loan->overdue : 0,
                'balance' => isset($loan->balance) ? $loan->balance : 0,
                'status' => isset($loan->status) ? $loan->status : '',
                'venue' => isset($loan->disbursment) ? $loan->disbursment->venue : '',
            );
            return $array;
        }
        return $loan;
    }

    public static function parseLoanBasic($loan)
    {
        if (!empty($loan)) {
            $array = array(
                'id' => $loan->id,
                'application_id' => isset($loan->application_id) ? $loan->application_id : 0,
                'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : "",
            );
            return $array;
        }
        return $loan;
    }

    public static function parseLoanTranches($tranches)
    {
        $array = [];
        if (!empty($tranches)) {
            foreach ($tranches as $tranche) {
                $array[] = array(
                    'id' => $tranche->id,
                    'tranch_amount' => $tranche->tranch_amount,
                    'tranch_no' => $tranche->tranch_no,
                    'status' => $tranche->status,
                    'tranch_date' => ($tranche->tranch_date != 0) ? date('d M Y',$tranche->tranch_date) : '',
                    'start_date' => ($tranche->start_date != 0) ? date('d M Y',$tranche->start_date) : '',
                );
            }
            return $array;
        } else {
            return $array;
        }
    }

    public static function parseLoanTrancheForRecomendation($tranche)
    {
        if (!empty($tranche)) {

                $array = array(
                    'id' => $tranche->id,
                    'tranch_amount' => $tranche->tranch_amount,
                    'tranch_no' => $tranche->tranch_no,
                    'status' => $tranche->status,
                    'tranch_date' => ($tranche->tranch_date != 0) ? date('d M Y',$tranche->tranch_date) : '',
                    'start_date' => ($tranche->start_date != 0) ? date('d M Y',$tranche->start_date) : '',
                );

                return $array;
            }

        else {
            return $tranche;
        }

    }

    public static function parseLoanBasicInfo($loan)
    {
        if (!empty($loan)) {
            $array = array(
                'id' => $loan->id,
                'application_id' => isset($loan->application_id) ? $loan->application_id : 0,
                'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : "",
                'loan_amount' => isset($loan->loan_amount) ? $loan->loan_amount : 0,
                'status' => isset($loan->status) ? $loan->status : '',
                'attendance_status' => isset($loan->activeTranch) && !empty($loan->activeTranch)? $loan->activeTranch->attendance_status : $loan->attendance_status,
                'details' => self::parseLoanTranches($loan->tranches)
            );
            if(in_array($loan->project_id,StructureHelper::trancheProjects()))
            {
                $array = array_merge($array,['percent' => LoanHelper::getCompletionPercentage($loan->application_id)]);
            }

            return $array;
        }
        return $loan;
    }

    public static function parseLedger($loan)
    {
        if (!empty($loan)) {
            $array = array(
                'id' => isset($loan->application->member->id) ? $loan->application->member->id : 0,
                'full_name' => isset($loan->application->member->full_name) ? $loan->application->member->full_name : '',
                'parentage' => isset($loan->application->member->parentage) ? $loan->application->member->parentage : '',
                'cnic' => isset($loan->application->member->cnic) ? $loan->application->member->cnic : '',
                'grp_no' => isset($loan->application->group->grp_no) ? $loan->application->group->grp_no : '',
                'branch' => isset($loan->branch->name) ? $loan->branch->name : '',
                'project' => isset($loan->project->name) ? $loan->project->name : '',
                'label_wise_borrower_detail' => [
                    /*[
                        'key' => 'Id',
                        'value' => isset($loan->application->member->id) ? $loan->application->member->id : 0,
                    ],*/
                    [
                        'key' => 'Full Name',
                        'value' => isset($loan->application->member->full_name) ? $loan->application->member->full_name : '',
                    ],
                    [
                        'key' => 'Parentage',
                        'value' => isset($loan->application->member->parentage) ? $loan->application->member->parentage : '',
                    ],
                    [
                        'key' => 'CNIC',
                        'value' => isset($loan->application->member->cnic) ? $loan->application->member->cnic : '',
                    ],
                    /*[
                        'key' => 'Type',
                        'value' => isset($loan->application->group->grptype) ? $loan->application->group->grptype : '',
                    ],*/
                    [
                        'key' => 'Group No',
                        'value' => isset($loan->application->group->grp_no) ? $loan->application->group->grp_no : '',
                    ],
                    [
                        'key' => 'Branch',
                        'value' => isset($loan->branch->name) ? $loan->branch->name : '',
                    ],
                    [
                        'key' => 'Project',
                        'value' => isset($loan->project->name) ? $loan->project->name : '',
                    ],
                    /*[
                        'key' => 'Mobile',
                        'value' => isset($borrower->mobile) ? $borrower->mobile : '',
                    ],
                    [
                        'key' => 'Loan Purpose',
                        'value' => isset($borrower->activity->name) ? $borrower->activity->name : '',
                    ]*/
                ],
                'loan' => self::parseLoanLedger($loan),
                'ledger' => self::parseLedgerDetail($loan)

            );
            return $array;
        }
        return $loan;
    }

    public static function parseLedgerDetail($loan)
    {
        $schedules = $loan->schedules;
        if (!empty($schedules)) {

            $recv = array();
            $id = 1;
            foreach ($schedules as $s) {
                //print_r($s);
                //die();
                $balance = $loan->loan_amount;
                if (!empty($s->recoveries)) {
                    foreach ($s->recoveries as $r) {
                        $balance = $balance - (isset($r->credit) ? $r->credit : 0);
                        $recv[] = array(
                            'id' => $id,
                            'due_date' => isset($s->due_date) ? date('d M Y', $s->due_date) : '',
                            'credit' => isset($r->amount) ? ($r->amount)+($r->charges_amount) : '0',
                            'recv_date' => isset($r->receive_date) ? date('d M Y', $r->receive_date) : '',
                            'receipt_no' => isset($r->receipt_no) ? $r->receipt_no : '',
                            'schdl_amnt' => isset($s->schdl_amnt) ? $s->schdl_amnt : '0',
                            'schdl_credit' => isset($s->credit) ? $s->credit : '0',
                            'advance' => isset($s->advance_log) ? $s->advance_log : '0',
                            'overdue' => isset($s->overdue_log) ? $s->overdue_log : '0',
                            'due' => isset($s->due_amnt) ? $s->due_amnt : '0',
                            'mdp' => isset($r->mdp) ? $r->mdp : '',
                            'is_late' => false,
                            'label_wise_ledger' =>
                                [
                                    [
                                        'key' => 'Installment No',
                                        'value' => $id
                                    ],
                                    [
                                        'key' => 'Due Date',
                                        'value' => isset($s->due_date) ? date('d M Y', $s->due_date) : '',
                                    ],
                                    [
                                        'key' => 'Due Amount',
                                        'value' => isset($s->schdl_amnt) ? number_format($s->schdl_amnt) : '0',
                                    ],
                                    [
                                        'key' => 'Receipt No',
                                        'value' => isset($r->receipt_no) ? $r->receipt_no : '',
                                    ],
                                    [
                                        'key' => 'Received Date',
                                        'value' => isset($r->receive_date) ? date('d M Y', $r->receive_date) : '',
                                    ],
                                    [
                                        'key' => 'Received Amount',
                                        'value' => isset($r->amount) ? number_format(($r->amount)+($r->charges_amount)) : '0',
                                    ]
                                ]
                        );
                        $id++;
                    }
                } else {
                    $recv[] = array(
                        'id' => $id,
                        'due_date' => isset($s->due_date) ? date('d M Y', $s->due_date) : '',
                        'credit' => '0',
                        'recv_date' => '',
                        'receipt_no' => '',
                        'schdl_credit' => isset($s->credit) ? $s->credit : '',
                        'schdl_amnt' => isset($s->schdl_amnt) ? $s->schdl_amnt : '',
                        'advance' => isset($s->advance_log) ? $s->advance_log : '',
                        'overdue' => isset($s->overdue_log) ? $s->overdue_log : '0',
                        'due' => isset($s->due_amnt) ? $s->due_amnt : '0',
                        'mdp' => '0',
                        'is_late' => false,
                        'label_wise_ledger' =>
                            [
                                [
                                    'key' => 'Installment No',
                                    'value' => $id
                                ],
                                [
                                    'key' => 'Due Date',
                                    'value' => isset($s->due_date) ? date('d M Y', $s->due_date) : '',
                                ],
                                [
                                    'key' => 'Due Amount',
                                    'value' => isset($s->schdl_amnt) ? number_format($s->schdl_amnt) : '0',
                                ],
                                [
                                    'key' => 'Receipt No',
                                    'value' => '',
                                ],
                                [
                                    'key' => 'Received Date',
                                    'value' => '',
                                ],
                                [
                                    'key' => 'Received Amount',
                                    'value' => '0',
                                ]
                            ]
                    );
                    $id++;
                }
            }
            return $recv;
        }
        return $schedules;
    }

    public static function parseLoanLedger($loan)
    {
        if (!empty($loan)) {
            $array = array(
                'id' => isset($loan->id) ? $loan->id : 0,
                'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : '',
                'loan_amount' => isset($loan->loan_amount) ? $loan->loan_amount : '',
                'date_disbursed' => isset($loan->date_disbursed) ? date('Y-m-d', $loan->date_disbursed) : 0,
                'status' => isset($loan->status) ? $loan->status : '',
                'label_wise_loan_detail' => [
                    /*[
                        'key' => 'Sanction No',
                        'value' => isset($loan->sanction_no) ? $loan->sanction_no : '',
                    ],*/
                    [
                        'key' => 'Loan Amount',
                        'value' => isset($loan->loan_amount) ? $loan->loan_amount : '',
                    ],
                    /*[
                        'key' => 'Disbursement Date',
                        'value' => isset($loan->date_disbursed) ? date('Y-m-d', $loan->date_disbursed) : 0,
                    ],
                    [
                        'key' => 'Loan Status',
                        'value' => isset($loan->status) ? $loan->status : '',
                    ],*/
                    [
                        'key' => 'Received Amount',
                        'value' => RecoveriesHelper::getReceiveAmount($loan),
                    ],
                    [
                        'key' => 'Outstanding Balance',
                        'value' => isset($loan->balance) ? $loan->balance : '',
                    ]
                ]

            );
            return $array;
        }
        return $loan;
    }

    public static function parseRecovery($model)
    {
        $info = array();
        if (!empty($model)) {
            return [
                [
                    'key' => 'Receipt No',
                    'value' => isset($model->receipt_no) ? $model->receipt_no : ''
                ],
                [
                    'key' => 'Sanction No',
                    'value' => isset($model->sanction_no) ? $model->sanction_no : '',
                ],
                [
                    'key' => 'Member Name',
                    'value' => isset($model->application->member->full_name) ? $model->application->member->full_name : '',
                ],
                [
                    'key' => 'CNIC',
                    'value' => isset($model->application->member->cnic) ? $model->application->member->cnic : '',
                ],
                [
                    'key' => 'Received Date',
                    'value' => isset($model->receive_date) ? date('d-M-Y', $model->receive_date) : '',
                ],
                [
                    'key' => 'Recovery Amount',
                    'value' => isset($model->amount) ? 'Rs.' . number_format($model->amount) . '/-' : '',
                ],
                [
                    'key' => 'Loan Amount',
                    'value' => isset($model->loan->loan_amount) ? 'Rs.' . number_format($model->loan->loan_amount) . '/-' : '',
                ],
                [
                    'key' => 'Outstanding Balance',
                    'value' => isset($model->loan->balance) ? 'Rs.' . number_format($model->loan->balance) . '/-' : '',
                ],
                /*'receipt_no' => isset($model->receipt_no) ? $model->receipt_no : '',
                'sanction_no' => isset($model->sanction_no) ? $model->sanction_no : '',
                'name' => isset($model->borrower->name) ? $model->borrower->name : '',
                'cnic' => isset($model->borrower->cnic) ? $model->borrower->cnic : '',
                'balance' => isset($model->loan->balance) ? $model->loan->balance : '',
                'recv_date' => isset($model->recv_date) ? $model->recv_date : '',
                'recovery_amount' => isset($model->credit) ? $model->credit : '',
                'mdp' => isset($model->mdp) ? $model->mdp : '',*/
            ];
            //return $info;
        }
        return $info;
    }

    public static function parseLoanRecovery($loans)
    {
        /*print_r($loans);
        die();*/
        if (!empty($loans)) {
            foreach ($loans as $loan) {
                $array['loan'][] = array(
                    'id' => $loan->id,
                    'application_id' => isset($loan->application_id) ? $loan->application_id : 0,
                    'name' => isset($loan->application->member->full_name) ? $loan->application->member->full_name : 0,
                    'mobile' => isset($loan->application->member->membersMobile->phone) ? $loan->application->member->membersMobile->phone : 0,
                    'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : "",
                    'loan_amount' => isset($loan->loan_amount) ? $loan->loan_amount : 0,
                    'status' => isset($loan->status) ? $loan->status : '',
                    'date_disbursed' => isset($loan->date_disbursed) && $loan->date_disbursed != 0 ? date('Y-m-d', $loan->date_disbursed) : 0,
                    'balance' => isset($loan->balance) ? $loan->balance : 0,
                    'due' => isset($loan->due) ? $loan->due : 0,
                    'overdue' => isset($loan->overdue) ? $loan->overdue : 0,
                    'member' => self::parseLoanRecoveryMember($loan->application->member, $loan),
                    'paid-installments' => self::parsePaidInstallments($loan),

                );
            }

            return $array;
        }
        return $loans;
    }

    public static function parseLoanRecoveryMember($member, $loan)
    {
        if (!empty($member)) {
            $array =
                [
                    [
                        'key' => 'Name',
                        'value' => isset($member->full_name) ? $member->full_name : '',
                    ],
                    [
                        'key' => 'Parentage',
                        'value' => isset($member->parentage) ? $member->parentage : '',
                    ],
                    [
                        'key' => 'CNIC',
                        'value' => isset($member->cnic) ? $member->cnic : '',
                    ],
                    [
                        'key' => 'MOBILE',
                        'value' => isset($member->membersMobile->phone) ? $member->membersMobile->phone : '',
                    ],
                    [
                        'key' => 'Balance',
                        'value' => isset($loan->balance) ? $loan->balance : '0',
                    ],
                    [
                        'key' => 'Loan Amount',
                        'value' => isset($loan->loan_amount) ? $loan->loan_amount : '0',
                    ],
                    [
                        'key' => 'Due Amount',
                        'value' => RecoveriesHelper::getDueAmount($loan)
                    ]
                ];

            return $array;
        }
        return $member;
    }

    public static function parsePaidInstallments($loan)
    {
        $schedules = [];
        if (!empty($loan->schedules)) {
            $schedules = $loan->schedules;
            $recv = array();
            $id = 1;
            foreach ($schedules as $s) {
                //print_r($s);
                //die();
                $balance = $loan->loan_amount;
                if (!empty($s->recoveries)) {
                    foreach ($s->recoveries as $r) {
                        $balance = $balance - (isset($r->credit) ? $r->credit : 0);
                        $recv[] =
                            [
                                'installment_no' => $id,
                                'due_date' => isset($s->due_date) ? date('d M Y', strtotime($s->due_date)) : '',
                                'due_amount' => isset($s->due_amnt) ? number_format($s->due_amnt) : '0',
                                'receipt_no' => isset($r->receipt_no) ? $r->receipt_no : '',
                                'recv_date' => isset($r->recv_date) ? date('d M Y', $r->recv_date) : date('d M Y', 0),
                                'recv_amount' => isset($r->credit) ? number_format($r->credit) : '0',
                            ];
                        $id++;
                    }
                } else {
                    $recv[] = [
                        'installment_no' => $id,
                        'due_date' => isset($s->due_date) ? date('d M Y', strtotime($s->due_date)) : '',
                        'due_amount' => isset($s->due_amnt) ? number_format($s->due_amnt) : '0',
                        'receipt_no' => '',
                        'recv_date' => '',
                        'recv_amount' => '0',
                    ];

                    $id++;
                }
            }
            return $recv;
        }
        return $schedules;
    }

    public static function parseErrors($errors)
    {
        $error_list = '';
        if (is_array($errors)) {
            foreach ($errors as $error) {
                $error_list .= '<li>' . $error[0] . '</li>';
            }
        } else {
            $error_list .= '<li>' . $errors . '</li>';
        }

        return $error_list;
    }
    public static function parseMemberByKeyValue($member)
    {
        $array = new Members();
        if (!empty($member)) {
            $array = array(
                [
                    'key' => 'Full Name',
                    'value' => isset($member->full_name) ? $member->full_name : '',
                ],
                [
                    'key' => 'Parentage',
                    'value' => isset($member->parentage) ? $member->parentage : '',
                ],
                [
                    'key' => 'Parentage Type',
                    'value' => isset($member->parentage_type) ? $member->parentage_type : '',
                ],
                [
                    'key' => 'CNIC',
                    'value' => isset($member->cnic) ? $member->cnic : '',
                ],
                [
                    'key' => 'Gender',
                    'value' => isset($member->gender) ? ListHelper::getListValue('gender', $member->gender) : '',
                ],
                [
                    'key' => 'DOB',
                    'value' => isset($member->dob) ? date('d-F-Y', $member->dob) : '',
                ],
                [
                    'key' => 'Mobile',
                    'value' => isset($member->membersMobile->phone) ? $member->membersMobile->phone : "",
                ],
                [
                    'key' => 'Home Address',
                    'value' => isset($member->homeAddress->address) ? $member->homeAddress->address : "",
                ],
                [
                    'key' => 'Business Address',
                    'value' => isset($member->businessAddress->address) ? $member->businessAddress->address : "",
                ],
                [
                    'key' => 'Email Address',
                    'value' => isset($member->membersEmail->email) ? $member->membersEmail->email : "",
                ],
                [
                    'key' => 'Education',
                    'value' => isset($member->education) ? ListHelper::getListValue('education', $member->education) : '',
                ],
                [
                    'key' => 'Marital Status',
                    'value' => isset($member->marital_status) ? ListHelper::getListValue('marital_status', $member->marital_status) : '',
                ],
                [
                    'key' => 'Family Member Name',
                    'value' => isset($member->family_member_name) ? $member->family_member_name : '',
                ],
                [
                    'key' => 'Family Member CNIC',
                    'value' => isset($member->family_member_cnic) ? $member->family_member_cnic : '',
                ],
                [
                    'key' => 'Religion',
                    'value' => isset($member->religion) ? $member->religion : '',
                ],
                [
                    'key' => 'check',
                    'value' => 'Check Member Details',
                ]

            );
            return $array;
        }
        return $array;
    }

    public static function parseMemberByKeyValueSE($application)
    {
        $array = [];
        if (!empty($application)) {
            $array = array(
                [
                    'key' => 'CNIC',
                    'value' => isset($application->member->cnic) ? $application->member->cnic : '',
                ],
                [
                    'key' => 'Mobile',
                    'value' => isset($application->member->membersMobile->phone) ? $application->member->membersMobile->phone : "",
                ],
                [
                    'key' => 'Purpose of Loan',
                    'value' => isset($application->activity->name) ? $application->activity->name : '',
                ],
                [
                    'key' => 'Sub Activity',
                    'value' => isset($application->sub_activity) ? $application->sub_activity : '',
                ],

            );
            return $array;
        }
        return $array;
    }

    public static function parseApplicationByKeyValueBasic($application)
    {
        $array = new Applications();
        if (!empty($application)) {
            $array = array(
                [
                    'key' => 'Application Fee',
                    'value' => isset($application->fee) ? $application->fee : '',
                ],
                [
                    'key' => 'Application No',
                    'value' => isset($application->application_no) ? $application->application_no : '',
                ],
                [
                    'key' => 'Project',
                    'value' => isset($application->project->name) ? $application->project->name : '',
                ],
                [
                    'key' => 'Product',
                    'value' => isset($application->product->name) ? $application->product->name : '',
                ],
                [
                    'key' => 'Activity',
                    'value' => isset($application->activity->name) ? $application->activity->name : '',
                ],
                [
                    'key' => 'Group',
                    'value' => isset($application->group->grp_no) ? $application->group->grp_no : '',
                ],
                [
                    'key' => 'Requested Amount',
                    'value' => isset($application->req_amount) ? 'Rs. ' . number_format($application->req_amount) : '',
                ],
                [
                    'key' => 'No of Loans',
                    'value' => isset($application->no_of_times) ? $application->no_of_times : '',
                ],
                [
                    'key' => 'Bzns Cond',
                    'value' => isset($application->bzns_cond) ? $application->bzns_cond : '',
                ],
                [
                    'key' => 'Who Will Work',
                    'value' => isset($application->who_will_work) ? $application->who_will_work : '',
                ],
                [
                    'key' => 'Name Of Other',
                    'value' => isset($application->name_of_other) ? $application->name_of_other : '',
                ],
                [
                    'key' => 'Other Cnic',
                    'value' => isset($application->other_cnic) ? $application->other_cnic : '',
                ],
                [
                    'key' => 'Status',
                    'value' => isset($application->status) ? $application->status : '',
                ],

            );

            $project_details = ($application->project_table == '' || empty($application->project_table)) ? [] : ApplicationHelper::getProjectDetail($application->id, $application->project_table);
            if (!empty($project_details)) {
                $array = array_merge($array, CodeHelper::getKeyValue($project_details['data']));
            }
            $action = ActionsHelper::getPendingAction($application->id);
            $array[] = [
                'key' => ($action == 'Active Loan') ? 'Current Action' : 'Pending Action',
                'value' => $action,
            ];;

            return $array;
        }
        return $array;
    }

    public static function parseApplicationByKeyValue($application)
    {
        $array = new Applications();
        if (!empty($application)) {
            $array = array(
                [
                    'key' => 'Application Fee',
                    'value' => isset($application->fee) ? $application->fee : '',
                ],
                [
                    'key' => 'Application No',
                    'value' => isset($application->application_no) ? $application->application_no : '',
                ],
                [
                    'key' => 'Project',
                    'value' => isset($application->project->name) ? $application->project->name : '',
                ],
                [
                    'key' => 'Product',
                    'value' => isset($application->product->name) ? $application->product->name : '',
                ],
                [
                    'key' => 'Activity',
                    'value' => isset($application->activity->name) ? $application->activity->name : '',
                ],
                [
                    'key' => 'Requested Amount',
                    'value' => isset($application->req_amount) ? 'Rs. ' . number_format($application->req_amount) : '',
                ],
                [
                    'key' => 'No of Loans',
                    'value' => isset($application->no_of_times) ? $application->no_of_times : '',
                ],
                [
                    'key' => 'Bzns Cond',
                    'value' => isset($application->bzns_cond) ? $application->bzns_cond : '',
                ],
                [
                    'key' => 'Who Will Work',
                    'value' => isset($application->who_will_work) ? $application->who_will_work : '',
                ],
                [
                    'key' => 'Name Of Other',
                    'value' => isset($application->name_of_other) ? $application->name_of_other : '',
                ],
                [
                    'key' => 'Other Cnic',
                    'value' => isset($application->other_cnic) ? $application->other_cnic : '',
                ],
                [
                    'key' => 'Urban',
                    'value' => isset($application->is_urban) && $application->is_urban == 1 ? 'Yes' : 'No',
                ],
                [
                    'key' => 'Lock',
                    'value' => isset($application->is_lock) && $application->is_lock == 1 ? 'Yes' : 'No',
                ]
            );
            $check_array = array(
                array(
                    'key' => 'check',
                    'value' => 'Check Application Details',
                )
            );

            $project_details = $application->project_table == '' ? [] : ApplicationHelper::getProjectDetail($application->id, $application->project_table);
            if (!empty($project_details)) {
                $array = array_merge($array, CodeHelper::getKeyValue($project_details['data']));
            }
            $array = array_merge($array, $check_array);
            return $array;
        }
        return $array;
   }

    public static function parseApplicationByKeyValueSE($application)
    {
        $array = new Applications();
        if (!empty($application)) {
            $array = array(
                [
                    'key' => 'Application No',
                    'value' => isset($application->application_no) ? $application->application_no : '',
                ],
                [
                    'key' => 'Activity',
                    'value' => isset($application->activity->name) ? $application->activity->name : '',
                ],
                [
                    'key' => 'Sub Activity',
                    'value' => isset($application->sub_activity) ? $application->sub_activity : '',
                ],
                [
                    'key' => 'Requested Amount',
                    'value' => isset($application->req_amount) ? 'Rs. ' . number_format($application->req_amount) : '',
                ],
                [
                    'key' => 'Client Contribution',
                    'value' => isset($application->client_contribution) ? 'Rs. ' . number_format($application->client_contribution) : '',
                ],
            );
            $check_array = array(
                array(
                    'key' => 'check',
                    'value' => 'Check Application Details',
                )
            );

            $array = array_merge($array, $check_array);
            return $array;
        }
        return $array;
    }

   
    public static function parseSocialAppraisalByKeyValue($social_appraisal)
    {
        $array = [];
        if (!empty($social_appraisal)) {
            $array = array(
                [
                    'key' => 'Poverty Index',
                    'value' => isset($social_appraisal->poverty_index) ? $social_appraisal->poverty_index : '',
                ],
                [
                    'key' => 'House Ownership',
                    'value' => isset($social_appraisal->house_ownership) ? $social_appraisal->house_ownership : '',
                ],
                [
                    'key' => ($social_appraisal->house_ownership == 'mortgage') ? 'Mortgage Amount' : 'House Rent Amount',
                    'value' => isset($social_appraisal->house_rent_amount)  && $social_appraisal->house_rent_amount > 0 ? 'Rs. ' . number_format($social_appraisal->house_rent_amount) : '',
                ],
                [
                    'key' => 'Land Size',
                    'value' => isset($social_appraisal->land_size) ? $social_appraisal->land_size : '',
                ],
                [
                    'key' => 'Total Family Members',
                    'value' => isset($social_appraisal->total_family_members) ? $social_appraisal->total_family_members : '',
                ],
                [
                    'key' => 'No Of Earning Hands',
                    'value' => isset($social_appraisal->no_of_earning_hands) ? $social_appraisal->no_of_earning_hands : '',
                ],
                [
                    'key' => 'Ladies',
                    'value' => isset($social_appraisal->ladies) ? $social_appraisal->ladies : '',
                ],
                [
                    'key' => 'Gents',
                    'value' => isset($social_appraisal->gents) ? $social_appraisal->gents : '',
                ],

                [
                    'key' => 'Source Of Income',
                    'value' => isset($social_appraisal->source_of_income) ? $social_appraisal->source_of_income : '',
                ],
                [
                    'key' => 'Total Household Income',
                    'value' => isset($social_appraisal->total_household_income) ? 'Rs. ' . number_format($social_appraisal->total_household_income) : '',
                ],
                [
                    'key' => 'Educational Expenses',
                    'value' => isset($social_appraisal->educational_expenses) ? 'Rs. ' . number_format($social_appraisal->educational_expenses) : '',
                ],
                [
                    'key' => 'Utility Bills',
                    'value' => isset($social_appraisal->utility_bills) ? 'Rs. ' . number_format($social_appraisal->utility_bills) : '',
                ],
                [
                    'key' => 'Medical Expenses',
                    'value' => isset($social_appraisal->medical_expenses) ? 'Rs. ' . number_format(round($social_appraisal->medical_expenses)) : '',
                ],
                [
                    'key' => 'Kitchen Expenses',
                    'value' => isset($social_appraisal->kitchen_expenses) ? 'Rs. ' . number_format(round($social_appraisal->kitchen_expenses)) : '',
                ],
                [
                    'key' => 'Monthly Savings',
                    'value' => isset($social_appraisal->monthly_savings) ? 'Rs. ' . number_format(round($social_appraisal->monthly_savings)) : '',
                ],
                [
                    'key' => 'Saving Amount',
                    'value' => isset($social_appraisal->amount) && $social_appraisal->amount > 0 ? 'Rs. ' . number_format($social_appraisal->amount) : '',
                ],
                [
                    'key' => 'Date Of Maturity',
                    'value' => isset($social_appraisal->date_of_maturity) && $social_appraisal->date_of_maturity != 0 ? date('d-M-Y', $social_appraisal->date_of_maturity) : '',
                ],
                [
                    'key' => 'Other Expenses',
                    'value' => isset($social_appraisal->other_expenses) ? 'Rs. ' . number_format($social_appraisal->other_expenses) : '',
                ],
                [
                    'key' => 'Total Expenses',
                    'value' => isset($social_appraisal->total_expenses) ? 'Rs. ' . number_format($social_appraisal->total_expenses) : '',
                ],
                [
                    'key' => 'Other Loan',
                    'value' => isset($social_appraisal->other_loan) ? 'Rs. ' . number_format($social_appraisal->other_loan) : '',
                ],
                [
                    'key' => 'Other Loan Amount',
                    'value' => isset($social_appraisal->loan_amount) && $social_appraisal->loan_amount > 0 ? 'Rs. ' . number_format($social_appraisal->loan_amount) : '',
                ],
                [
                    'key' => 'Business Income',
                    'value' => isset($social_appraisal->business_income) ? $social_appraisal->business_income : '',
                ],
                [
                    'key' => 'Job Income',
                    'value' => isset($social_appraisal->job_income) ? $social_appraisal->job_income : '',
                ],
                [
                    'key' => 'House Rent Income',
                    'value' => isset($social_appraisal->house_rent_income) ? $social_appraisal->house_rent_income : '',
                ],
                [
                    'key' => 'Other Income',
                    'value' => isset($social_appraisal->other_income) && $social_appraisal->other_income > 0 ? $social_appraisal->other_income : '',
                ],

                [
                    'key' => 'Economic Dealings',
                    'value' => isset($social_appraisal->economic_dealings) ? $social_appraisal->economic_dealings : '',
                ],
                [
                    'key' => 'Social Behaviour',
                    'value' => isset($social_appraisal->social_behaviour) ? $social_appraisal->social_behaviour : '',
                ],
                [
                    'key' => 'check',
                    'value' => 'Check Social Appraisal Details',
                ]

            );
            $data = [];
            if ($social_appraisal->date_of_maturity == 0 ) {
                foreach ($array as $key => $value) {
                    if ($value['key'] != 'Date Of Maturity') {
                        $data[] = $array[$key];
                    }
                }
                $array = $data;
            }
            return $array;
        }
        return $array;
    }

    public static function parseBusinessAppraisalByKeyValue($business_appraisal)
    {
        $array = [];
        if (!empty($business_appraisal)) {
            $array = array(
                [
                    'key' => 'Place of Business',
                    'value' => isset($business_appraisal->place_of_business) ? $business_appraisal->place_of_business : '',
                ],
                [
                    'key' => 'New Required Assets',
                    'value' => $business_appraisal->new_required_assets,
                ],
                [
                    'key' => 'New Required Assets Amount',
                    'value' => $business_appraisal->new_required_assets_amount,
                ],
                [
                    'key' => 'Business Expenses',
                    'value' => $business_appraisal->business_expenses,
                ],
                [
                    'key' => 'Business Expenses Amount',
                    'value' => $business_appraisal->business_expenses_amount,
                ],
                [
                    'key' => 'Running Capital',
                    'value' => $business_appraisal->running_capital,
                ],
                [
                    'key' => 'Running Capital Amount',
                    'value' => $business_appraisal->running_capital_amount,
                ],
                [
                    'key' => 'Fixed Business Assets',
                    'value' => $business_appraisal->fixed_business_assets,
                ],
                [
                    'key' => 'Fixed Business Assets Amount',
                    'value' => $business_appraisal->fixed_business_assets_amount,
                ],
                [
                    'key' => 'check',
                    'value' => 'Check Business Appraisal Details',
                ]
            );
            return $array;
        }
        return $array;
    }

    public static function parseAgricultureAppraisalByKeyValue($agriculture_appraisal)
    {
        $array = [];
        if (!empty($agriculture_appraisal)) {
            $array = array(
                [
                    'key' => 'Water Analysis',
                    'value' => isset($agriculture_appraisal->water_analysis) ? $agriculture_appraisal->water_analysis : '',
                ],
                [
                    'key' => 'Soil Analysis',
                    'value' => isset($agriculture_appraisal->soil_analysis) ? $agriculture_appraisal->soil_analysis : '',
                ],
                [
                    'key' => 'Laser Level',
                    'value' => isset($agriculture_appraisal->laser_level) ? $agriculture_appraisal->laser_level : '',
                ],
                [
                    'key' => 'Irrigation Source',
                    'value' => isset($agriculture_appraisal->irrigation_source) ? $agriculture_appraisal->irrigation_source : '',
                ],
                [
                    'key' => 'Other Source',
                    'value' => isset($agriculture_appraisal->other_source) ? $agriculture_appraisal->other_source : '',
                ],
                [
                    'key' => 'Crop Year',
                    'value' => isset($agriculture_appraisal->crop_year) ? $agriculture_appraisal->crop_year : '',
                ],
                [
                    'key' => 'Crop Production',
                    'value' => isset($agriculture_appraisal->crop_production) ? $agriculture_appraisal->crop_production : '',
                ],
                [
                    'key' => 'Resources',
                    'value' => isset($agriculture_appraisal->resources) ? $agriculture_appraisal->resources : '',
                ],
                [
                    'key' => 'Expenses',
                    'value' => isset($agriculture_appraisal->expenses) ? $agriculture_appraisal->expenses : '',
                ],
                [
                    'key' => 'Available Resources',
                    'value' => isset($agriculture_appraisal->available_resources) ? $agriculture_appraisal->available_resources : '',
                ],
                [
                    'key' => 'Required Resources',
                    'value' => isset($agriculture_appraisal->required_resources) ? $agriculture_appraisal->required_resources : '',
                ],
                [
                    'key' => 'check',
                    'value' => 'Check Agriculture Appraisal Details',
                ]
            );
            return $array;
        }
        return $array;
    }

    public static function parseHousingAppraisalByKeyValueSE($housing_appraisal)
    {
        $array = [];
        if (!empty($housing_appraisal)) {
            $array = array(
                [
                    'key' => 'Property Type',
                    'value' => isset($housing_appraisal->property_type) ? $housing_appraisal->property_type : '',
                ],
                [
                    'key' => 'Ownership',
                    'value' => $housing_appraisal->ownership,
                ],
                [
                    'key' => 'Land Area',
                    'value' => $housing_appraisal->land_area,
                ],
                [
                    'key' => 'Residential Area',
                    'value' => $housing_appraisal->residential_area,
                ],
                [
                    'key' => 'Purchase Price',
                    'value' => number_format($housing_appraisal->purchase_price),
                ],
                [
                    'key' => 'Current Price',
                    'value' => number_format($housing_appraisal->current_price),
                ],
                /*[
                    'key' => 'Estimated Figure',
                    'value' => $housing_appraisal->estimated_figures,
                ],
                [
                    'key' => 'Estimated Start Date',
                    'value' => date('Y-m-d', $housing_appraisal->estimated_start_date),
                ],
                [
                    'key' => 'Estimated Completion Time',
                    'value' => $housing_appraisal->estimated_completion_time,
                ],*/
                [
                    'key' => 'Address',
                    'value' => $housing_appraisal->address,
                ]
            );
            if ($housing_appraisal->property_type != "plot") {
                $data = array(
                    [
                        'key' => 'Living Duration',
                        'value' => $housing_appraisal->living_duration,
                    ],
                    [
                        'key' => 'Duration Type',
                        'value' => $housing_appraisal->duration_type,
                    ],

                    [
                        'key' => 'No of Rooms',
                        'value' => $housing_appraisal->no_of_rooms,
                    ],
                    [
                        'key' => 'No. of Kitchens',
                        'value' => $housing_appraisal->no_of_kitchens,
                    ],
                    [
                        'key' => 'No. of Toilets',
                        'value' => $housing_appraisal->no_of_toilets,
                    ],
                );
                $array = array_merge($array, $data);
            }
            return $array;
        }
        return $array;
    }

    public static function parseHousingAppraisalByKeyValue($housing_appraisal)
    {
        $array = [];
        if (!empty($housing_appraisal)) {
            $array = array(
                [
                    'key' => 'Property Type',
                    'value' => isset($housing_appraisal->property_type) ? $housing_appraisal->property_type : '',
                ],
                [
                    'key' => 'Ownership',
                    'value' => $housing_appraisal->ownership,
                ],
                [
                    'key' => 'Land Area',
                    'value' => $housing_appraisal->land_area,
                ],
                [
                    'key' => 'Residential Area',
                    'value' => $housing_appraisal->residential_area,
                ],
                [
                    'key' => 'Living Duration',
                    'value' => $housing_appraisal->living_duration,
                ],
                [
                    'key' => 'Duration Type',
                    'value' => $housing_appraisal->duration_type,
                ],
                [
                    'key' => 'No of Rooms',
                    'value' => $housing_appraisal->no_of_rooms,
                ],
                [
                    'key' => 'No. of Kitchens',
                    'value' => $housing_appraisal->no_of_kitchens,
                ],
                [
                    'key' => 'No. of Toilets',
                    'value' => $housing_appraisal->no_of_toilets,
                ],
                [
                    'key' => 'Purchase Price',
                    'value' => number_format($housing_appraisal->purchase_price),
                ],
                [
                    'key' => 'Current Price',
                    'value' => number_format($housing_appraisal->current_price),
                ],
                /*[
                    'key' => 'Estimated Figure',
                    'value' => $housing_appraisal->estimated_figures,
                ],
                [
                    'key' => 'Estimated Start Date',
                    'value' => date('Y-m-d', $housing_appraisal->estimated_start_date),
                ],
                [
                    'key' => 'Estimated Completion Time',
                    'value' => $housing_appraisal->estimated_completion_time,
                ],*/
                [
                    'key' => 'Address',
                    'value' => $housing_appraisal->address,
                ],
                [
                    'key' => 'check',
                    'value' => 'Check Housing Appraisal Details',
                ]
            );
            return $array;
        }
        return $array;
    }

    public static function parseBaAssets($baAssets)
    {
        $array = array();
        $array[] = array(
            'asset_name' => isset($baAssets->assets_list) ? $baAssets->assets_list : '',
            'value' => isset($baAssets->total_amount) ? $baAssets->total_amount : '',
        );

        return $array;
    }

    public static function parseBaBusinessExpensesByKeyValue($baBusinessExpenses)
    {
        $array = array();
        if (!empty($baBusinessExpenses)) {
            foreach ($baBusinessExpenses as $b) {
                $array[] = array(
                    'asset_name' => isset($b->expense_title) ? $b->expense_title : '',
                    'value' => isset($b->value) ? $b->value : '',
                    'quantity' => isset($b->quantity) ? $b->quantity : '',
                );
            }
            return $array;
        }
        return $array;
    }

    public static function parseBaFixedBusinessAssetsByKeyValue($baFixedBusinessAssets)
    {
        $array = array();
        if (!empty($baFixedBusinessAssets)) {
            foreach ($baFixedBusinessAssets as $b) {
                $array[] = array(
                    'asset_name' => isset($b->expense_title) ? $b->expense_title : '',
                    'value' => isset($b->value) ? $b->value : '',
                    'quantity' => isset($b->quantity) ? $b->quantity : '',
                );
            }
            return $array;
        }
        return $array;
    }

    public static function parseBaNewRequiredAssetsByKeyValue($baNewRequiredAssets)
    {
        $array = array();
        if (!empty($baNewRequiredAssets)) {
            foreach ($baNewRequiredAssets as $b) {
                $array[] = array(
                    'asset_name' => isset($b->expense_title) ? $b->expense_title : '',
                    'value' => isset($b->value) ? $b->value : '',
                    'quantity' => isset($b->quantity) ? $b->quantity : '',
                );
            }
            return $array;
        }
        return $array;
    }

    public static function parseBaRunningCapitalsByKeyValue($baRunningCapitals)
    {
        $array = array();
        if (!empty($baRunningCapitals)) {
            foreach ($baRunningCapitals as $b) {
                $array[] = array(
                    'asset_name' => isset($b->expense_title) ? $b->expense_title : '',
                    'value' => isset($b->value) ? $b->value : '',
                    'quantity' => isset($b->quantity) ? $b->quantity : '',
                );
            }
            return $array;
        }
        return $array;
    }

    public static function parseAppraisal($data, $type)
    {
        $appraisal = [];
        if ($type == 'appraisals_social') {
            $appraisal = self::parseSocialAppraisal($data);
        } else if ($type == 'appraisals_business') {
            $appraisal = self::parseBusinessAppraisal($data);
        } else if ($type == 'appraisals_agriculture') {
            $appraisal = self::parseAgricultureAppraisal($data);
        } else if ($type == 'appraisals_housing') {
            $appraisal = self::parseHousingAppraisal($data);
        }else if ($type == 'appraisals_emergency') {
            $appraisal = self::parseEmergencyAppraisal($data);
        }

        return $appraisal;
    }

    public static function parseAppraisalTest($data, $type)
    {
        $appraisal = [];
        if ($type == 'appraisals_social') {
            $appraisal = self::parseSocialAppraisalTest($data);
        } else if ($type == 'appraisals_business') {
            $appraisal = self::parseBusinessAppraisalTest($data);
        }  else if ($type == 'appraisals_housing') {
            $appraisal = self::parseHousingAppraisal($data);
        }

        return $appraisal;
    }

    public static function parseSocialAppraisal($social_appraisal)
    {
        $array = new SocialAppraisal();
        if (!empty($social_appraisal)) {
            $array = array(
                'id' => (string)$social_appraisal->id,
                'application_id' => isset($social_appraisal->application_id) ? (string)$social_appraisal->application_id : "0",
                'poverty_index' => isset($social_appraisal->poverty_index) ? $social_appraisal->poverty_index : "",
                'house_ownership' => isset($social_appraisal->house_ownership) ? $social_appraisal->house_ownership : "",
                'house_rent_amount' => isset($social_appraisal->house_rent_amount) ? (string)$social_appraisal->house_rent_amount : "0",
                'land_size' => isset($social_appraisal->land_size) ? (string)$social_appraisal->land_size : "0",
                'total_family_members' => isset($social_appraisal->total_family_members) ? (string)$social_appraisal->total_family_members : "0",
                'no_of_earning_hands' => isset($social_appraisal->no_of_earning_hands) ? (string)$social_appraisal->no_of_earning_hands : "0",
                'ladies' => isset($social_appraisal->ladies) ? (string)$social_appraisal->ladies : "0",
                'gents' => isset($social_appraisal->gents) ? (string)$social_appraisal->gents : "0",
                'source_of_income' => isset($social_appraisal->source_of_income) ? $social_appraisal->source_of_income : "",
                'total_household_income' => isset($social_appraisal->total_household_income) ? (string)$social_appraisal->total_household_income : "0",
                'utility_bills' => isset($social_appraisal->utility_bills) ? number_format($social_appraisal->utility_bills, 0, '.', '') : "0",
                'educational_expenses' => isset($social_appraisal->educational_expenses) ? number_format($social_appraisal->educational_expenses, 0, '.', '') : "0",
                'medical_expenses' => isset($social_appraisal->medical_expenses) ? number_format($social_appraisal->medical_expenses, 0, '.', '') : "0",
                'kitchen_expenses' => isset($social_appraisal->kitchen_expenses) ? number_format($social_appraisal->kitchen_expenses, 0, '.', '') : "0",
                'monthly_savings' => isset($social_appraisal->monthly_savings) ? $social_appraisal->monthly_savings : "",
                'amount' => isset($social_appraisal->amount) ? number_format($social_appraisal->amount, 0, '.', '') : "0",
                'date_of_maturity' => ($social_appraisal->monthly_savings == 'no_saving') ? "" : (isset($social_appraisal->date_of_maturity) && $social_appraisal->date_of_maturity != 0 ? date('d-F-Y', $social_appraisal->date_of_maturity) : ""),
                'other_expenses' => isset($social_appraisal->other_expenses) ? number_format($social_appraisal->other_expenses, 0, '.', '') : "0",
                'total_expenses' => isset($social_appraisal->total_expenses) ? number_format($social_appraisal->total_expenses, 0, '.', '') : "0",
                'other_loan' => isset($social_appraisal->other_loan) ? (string)$social_appraisal->other_loan : "",
                'loan_amount' => isset($social_appraisal->loan_amount) ? number_format($social_appraisal->loan_amount, 0, '.', '') : "0",
                'economic_dealings' => isset($social_appraisal->economic_dealings) ? $social_appraisal->economic_dealings : "",
                'social_behaviour' => isset($social_appraisal->social_behaviour) ? $social_appraisal->social_behaviour : "",
                'latitude' => isset($social_appraisal->latitude) ? (string)$social_appraisal->latitude : "0",
                'longitude' => isset($social_appraisal->longitude) ? (string)$social_appraisal->longitude : "0",
                'fatal_disease' => isset($social_appraisal->fatal_disease) ? (string)$social_appraisal->fatal_disease : "0",
                'business_income' => isset($social_appraisal->business_income) ? (string)$social_appraisal->business_income : "0",
                'job_income' => isset($social_appraisal->job_income) ? (string)$social_appraisal->job_income : "0",
                'house_rent_income' => isset($social_appraisal->house_rent_income) ? (string)$social_appraisal->house_rent_income : "0",
                'other_income' => isset($social_appraisal->other_income) ? (string)$social_appraisal->other_income : "0",
                'expected_increase_in_income' => isset($social_appraisal->expected_increase_in_income) ? (string)$social_appraisal->expected_increase_in_income : "0",
                'parent' => isset($social_appraisal->parent) ? $social_appraisal->parent : "",
                'family_member_info' => isset($social_appraisal->family_member_info) ? $social_appraisal->family_member_info : "",
                'earning_hands_data' => isset($social_appraisal->earning_hands_data) ? $social_appraisal->earning_hands_data : "",
                //'description' => isset($social_appraisal->description) ? $social_appraisal->description : '',
                //'description_image' => isset($social_appraisal->description_image) ? $social_appraisal->description_image : '',
                'status' => isset($social_appraisal->status) ? $social_appraisal->status : "",
            );

            return $array;
        }
        return $array;
    }

    public static function parseSocialAppraisalTest($social_appraisal)
    {
        $array = new SocialAppraisal();
        if (!empty($social_appraisal)) {
            $array = array(
                'id' => (string)$social_appraisal->id,
                'application_id' => isset($social_appraisal->application_id) ? (string)$social_appraisal->application_id : "0",
                'poverty_index' => isset($social_appraisal->poverty_index) ? $social_appraisal->poverty_index : "",
                'house_ownership' => isset($social_appraisal->house_ownership) ? $social_appraisal->house_ownership : "",
                'house_rent_amount' => isset($social_appraisal->house_rent_amount) ? (string)$social_appraisal->house_rent_amount : "0",
                'land_size' => isset($social_appraisal->land_size) ? (string)$social_appraisal->land_size : "0",
                'total_family_members' => isset($social_appraisal->total_family_members) ? (string)$social_appraisal->total_family_members : "0",
                'no_of_earning_hands' => isset($social_appraisal->no_of_earning_hands) ? (string)$social_appraisal->no_of_earning_hands : "0",
                'ladies' => isset($social_appraisal->ladies) ? (string)$social_appraisal->ladies : "0",
                'gents' => isset($social_appraisal->gents) ? (string)$social_appraisal->gents : "0",
                'source_of_income' => isset($social_appraisal->source_of_income) ? $social_appraisal->source_of_income : "",
                'total_household_income' => isset($social_appraisal->total_household_income) ? (string)$social_appraisal->total_household_income : "0",
                'utility_bills' => isset($social_appraisal->utility_bills) ? number_format($social_appraisal->utility_bills, 0, '.', '') : "0",
                'educational_expenses' => isset($social_appraisal->educational_expenses) ? number_format($social_appraisal->educational_expenses, 0, '.', '') : "0",
                'medical_expenses' => isset($social_appraisal->medical_expenses) ? number_format($social_appraisal->medical_expenses, 0, '.', '') : "0",
                'kitchen_expenses' => isset($social_appraisal->kitchen_expenses) ? number_format($social_appraisal->kitchen_expenses, 0, '.', '') : "0",
                'monthly_savings' => isset($social_appraisal->monthly_savings) ? $social_appraisal->monthly_savings : "",
                'amount' => isset($social_appraisal->amount) ? number_format($social_appraisal->amount, 0, '.', '') : "0",
                'date_of_maturity' => ($social_appraisal->monthly_savings == 'no_saving') ? "" : (isset($social_appraisal->date_of_maturity) && $social_appraisal->date_of_maturity != 0 ? date('d-F-Y', $social_appraisal->date_of_maturity) : ""),
                'other_expenses' => isset($social_appraisal->other_expenses) ? number_format($social_appraisal->other_expenses, 0, '.', '') : "0",
                'total_expenses' => isset($social_appraisal->total_expenses) ? number_format($social_appraisal->total_expenses, 0, '.', '') : "0",
                'other_loan' => isset($social_appraisal->other_loan) ? (string)$social_appraisal->other_loan : "",
                'loan_amount' => isset($social_appraisal->loan_amount) ? number_format($social_appraisal->loan_amount, 0, '.', '') : "0",
                'economic_dealings' => isset($social_appraisal->economic_dealings) ? $social_appraisal->economic_dealings : "",
                'social_behaviour' => isset($social_appraisal->social_behaviour) ? $social_appraisal->social_behaviour : "",
                'latitude' => isset($social_appraisal->latitude) ? (string)$social_appraisal->latitude : "0",
                'longitude' => isset($social_appraisal->longitude) ? (string)$social_appraisal->longitude : "0",
                'fatal_disease' => isset($social_appraisal->fatal_disease) ? (string)$social_appraisal->fatal_disease : "0",
                'business_income' => isset($social_appraisal->business_income) ? (string)$social_appraisal->business_income : "0",
                'job_income' => isset($social_appraisal->job_income) ? (string)$social_appraisal->job_income : "0",
                'house_rent_income' => isset($social_appraisal->house_rent_income) ? (string)$social_appraisal->house_rent_income : "0",
                'other_income' => isset($social_appraisal->other_income) ? (string)$social_appraisal->other_income : "0",
                'expected_increase_in_income' => isset($social_appraisal->expected_increase_in_income) ? (string)$social_appraisal->expected_increase_in_income : "0",
                'parent' => isset($social_appraisal->parent) ? $social_appraisal->parent : "",
                'family_member_info' => isset($social_appraisal->family_member_info) ? $social_appraisal->family_member_info : "",
                'earning_hands_data' => isset($social_appraisal->earning_hands_data) ? $social_appraisal->earning_hands_data : "",
                //'description' => isset($social_appraisal->description) ? $social_appraisal->description : '',
                //'description_image' => isset($social_appraisal->description_image) ? $social_appraisal->description_image : '',
                'status' => isset($social_appraisal->status) ? $social_appraisal->status : "",
            );

            return $array;
        }
        return $array;
    }

    public static function parseBusinessAppraisal($business_appraisal)
    {
        $array = [];
        if (!empty($business_appraisal)) {
            $array = array(
                'id' => $business_appraisal->id,
                'application_id' => isset($business_appraisal->application_id) ? $business_appraisal->application_id : "0",
                'place_of_business' => isset($business_appraisal->place_of_business) ? $business_appraisal->place_of_business : "",
                'period_of_business' => isset($business_appraisal->period_of_business) ? (string)$business_appraisal->period_of_business : "",
                'who_are_customers' => isset($business_appraisal->who_are_customers) ? $business_appraisal->who_are_customers : "",
                'emp_before_loan' => isset($business_appraisal->emp_before_loan) ? (string)$business_appraisal->emp_before_loan : "",
                'emp_after_loan' => isset($business_appraisal->emp_after_loan) ? (string)$business_appraisal->emp_after_loan : "",
                'fixed_business_assets' => isset($business_appraisal->fixed_business_assets) ? $business_appraisal->fixed_business_assets : "",
                'fixed_business_assets_amount' => isset($business_appraisal->fixed_business_assets_amount) ? (string)$business_appraisal->fixed_business_assets_amount : "0",
                'running_capital' => isset($business_appraisal->running_capital) ? $business_appraisal->running_capital : "",
                'running_capital_amount' => isset($business_appraisal->running_capital_amount) ? (string)$business_appraisal->running_capital_amount : "0",
                'business_expenses' => isset($business_appraisal->business_expenses) ? $business_appraisal->business_expenses : "",
                'business_expenses_amount' => isset($business_appraisal->business_expenses_amount) ? (string)$business_appraisal->business_expenses_amount : "0",
                'new_required_assets' => isset($business_appraisal->new_required_assets) ? $business_appraisal->new_required_assets : "",
                'new_required_assets_amount' => isset($business_appraisal->new_required_assets_amount) ? (string)$business_appraisal->new_required_assets_amount : "0",
                'place_of_buying' => isset($business_appraisal->place_of_buying) ? $business_appraisal->place_of_buying : "",
                'latitude' => isset($business_appraisal->latitude) ? (string)$business_appraisal->latitude : "0",
                'longitude' => isset($business_appraisal->longitude) ? (string)$business_appraisal->longitude : "0",
                'status' => isset($business_appraisal->status) ? $business_appraisal->status : '',
            );
            return $array;
        }
        return $array;
    }

    public static function parseAgricultureAppraisal($agriculture_appraisal)
    {
        $array = [];
        if (!empty($agriculture_appraisal)) {
            $array = array(
                'id' => $agriculture_appraisal->id,
                'application_id' => isset($agriculture_appraisal->application_id) ? $agriculture_appraisal->application_id : "0",
                'water_analysis' => isset($agriculture_appraisal->water_analysis) ? $agriculture_appraisal->water_analysis : "",
                'soil_analysis' => isset($agriculture_appraisal->soil_analysis) ? $agriculture_appraisal->soil_analysis : "",
                'laser_level' => isset($agriculture_appraisal->laser_level) ? $agriculture_appraisal->laser_level : "",
                'irrigation_source' => isset($agriculture_appraisal->irrigation_source) ? $agriculture_appraisal->irrigation_source : "",
                'other_source' => isset($agriculture_appraisal->other_source) ? $agriculture_appraisal->other_source : "",
                'crop_year' => isset($agriculture_appraisal->crop_year) ? $agriculture_appraisal->crop_year : "",
                'crop_production' => isset($agriculture_appraisal->crop_production) ? $agriculture_appraisal->crop_production : "",
                'resources' => isset($agriculture_appraisal->resources) ? $agriculture_appraisal->resources : "",
                'expenses' => isset($agriculture_appraisal->expenses) ? $agriculture_appraisal->expenses : "",
                'available_resources' => isset($agriculture_appraisal->available_resources) ? $agriculture_appraisal->available_resources : "",
                'required_resources' => isset($agriculture_appraisal->required_resources) ? $agriculture_appraisal->required_resources : "",
                'latitude' => isset($agriculture_appraisal->latitude) ? (string)$agriculture_appraisal->latitude : "0",
                'longitude' => isset($agriculture_appraisal->longitude) ? (string)$agriculture_appraisal->longitude : "0",
                'status' => isset($agriculture_appraisal->status) ? $agriculture_appraisal->status : '',
            );
            return $array;
        }
        return $array;
    }

    public static function parseHousingAppraisal($housing_appraisal)
    {
        $array = [];
        if (!empty($housing_appraisal)) {
            $array = array(
                'id' => $housing_appraisal->id,
                'application_id' => isset($housing_appraisal->application_id) ? $housing_appraisal->application_id : "0",
                'property_type' => isset($housing_appraisal->property_type) ? $housing_appraisal->property_type : "",
                'ownership' => isset($housing_appraisal->ownership) ? $housing_appraisal->ownership : "",
                'land_area' => isset($housing_appraisal->land_area) ? (string)$housing_appraisal->land_area : "0",
                'residential_area' => isset($housing_appraisal->residential_area) ? (string)$housing_appraisal->residential_area : "",
                'living_duration' => isset($housing_appraisal->living_duration) ? (string)$housing_appraisal->living_duration : "",
                'duration_type' => isset($housing_appraisal->duration_type) ? $housing_appraisal->duration_type : "",
                'no_of_rooms' => isset($housing_appraisal->no_of_rooms) ? (string)$housing_appraisal->no_of_rooms : "0",
                'no_of_kitchens' => isset($housing_appraisal->no_of_kitchens) ? (string)$housing_appraisal->no_of_kitchens : "0",
                'no_of_toilets' => isset($housing_appraisal->no_of_toilets) ? (string)$housing_appraisal->no_of_toilets : "0",
                'purchase_price' => isset($housing_appraisal->purchase_price) ? (string)$housing_appraisal->purchase_price : "0",
                'current_price' => isset($housing_appraisal->current_price) ? (string)$housing_appraisal->current_price : "0",
                'address' => isset($housing_appraisal->address) ? $housing_appraisal->address : "",
                /*'estimated_figures' => isset($housing_appraisal->estimated_figures) ? $housing_appraisal->estimated_figures : "",
                'estimated_start_date' => isset($housing_appraisal->estimated_start_date) ? date('d-F-Y',$housing_appraisal->estimated_start_date) : "",
                'estimated_completion_time' => isset($housing_appraisal->estimated_completion_time) ? $housing_appraisal->estimated_completion_time : "",*/
                'latitude' => isset($housing_appraisal->latitude) ? (string)$housing_appraisal->latitude : "0",
                'longitude' => isset($housing_appraisal->longitude) ? (string)$housing_appraisal->longitude : "0",
                'status' => isset($housing_appraisal->status) ? $housing_appraisal->status : '',
            );
            return $array;
        }
        return $array;
    }
    public static function parseEmergencyAppraisal($emergency_appraisal)
    {
        $array = [];
        if (!empty($emergency_appraisal)) {
            $array = array(
                'id' => $emergency_appraisal->id,
                'application_id' => isset($emergency_appraisal->application_id) ? $emergency_appraisal->application_id : "0",
                'house_ownership' => isset($emergency_appraisal->house_ownership) ? $emergency_appraisal->house_ownership : "",
                'total_family_members' => isset($emergency_appraisal->total_family_members) ? (string)$emergency_appraisal->total_family_members : "0",
                'no_of_earning_hands' => isset($emergency_appraisal->no_of_earning_hands) ? (string)$emergency_appraisal->no_of_earning_hands : "0",
                'ladies' => isset($emergency_appraisal->ladies) ? (string)$emergency_appraisal->ladies : "0",
                'gents' => isset($emergency_appraisal->gents) ? (string)$emergency_appraisal->gents : "0",
                'income_before_corona' => isset($emergency_appraisal->income_before_corona) ? number_format($emergency_appraisal->income_before_corona, 0, '.', '') : "0",
                'income_after_corona' => isset($emergency_appraisal->income_after_corona) ? number_format($emergency_appraisal->income_after_corona, 0, '.', '') : "0",
                'expenses_in_corona' => isset($emergency_appraisal->expenses_in_corona) ? number_format($emergency_appraisal->expenses_in_corona, 0, '.', '') : "0",
                'economic_dealings' => isset($emergency_appraisal->economic_dealings) ? $emergency_appraisal->economic_dealings : "",
                'social_behaviour' => isset($emergency_appraisal->social_behaviour) ? $emergency_appraisal->social_behaviour : "",
                'address' => isset($emergency_appraisal->address) ? $emergency_appraisal->address : "",
                'latitude' => isset($emergency_appraisal->latitude) ? (string)$emergency_appraisal->latitude : "0",
                'longitude' => isset($emergency_appraisal->longitude) ? (string)$emergency_appraisal->longitude : "0",
                'status' => isset($emergency_appraisal->status) ? $emergency_appraisal->status : '',
            );
            return $array;
        }
        return $array;
    }

    public static function parseBusinessAppraisalTest($business_appraisal)
    {
        $array = [];
        if (!empty($business_appraisal)) {
            $array = array(
                'id' => $business_appraisal->id,
                'application_id' => isset($business_appraisal->application_id) ? $business_appraisal->application_id : "0",
                'place_of_business' => isset($business_appraisal->place_of_business) ? $business_appraisal->place_of_business : "",
                'period_of_business' => isset($business_appraisal->period_of_business) ? (string)$business_appraisal->period_of_business : "",
                'who_are_customers' => isset($business_appraisal->who_are_customers) ? $business_appraisal->who_are_customers : "",
                'emp_before_loan' => isset($business_appraisal->emp_before_loan) ? (string)$business_appraisal->emp_before_loan : "",
                'emp_after_loan' => isset($business_appraisal->emp_after_loan) ? (string)$business_appraisal->emp_after_loan : "",
                'fixed_business_assets' => isset($business_appraisal->fixed_business_assets) ? $business_appraisal->fixed_business_assets : "",
                'fixed_business_assets_amount' => isset($business_appraisal->fixed_business_assets_amount) ? (string)$business_appraisal->fixed_business_assets_amount : "0",
                'running_capital' => isset($business_appraisal->running_capital) ? $business_appraisal->running_capital : "",
                'running_capital_amount' => isset($business_appraisal->running_capital_amount) ? (string)$business_appraisal->running_capital_amount : "0",
                'business_expenses' => isset($business_appraisal->business_expenses) ? $business_appraisal->business_expenses : "",
                'business_expenses_amount' => isset($business_appraisal->business_expenses_amount) ? (string)$business_appraisal->business_expenses_amount : "0",
                'new_required_assets' => isset($business_appraisal->new_required_assets) ? $business_appraisal->new_required_assets : "",
                'new_required_assets_amount' => isset($business_appraisal->new_required_assets_amount) ? (string)$business_appraisal->new_required_assets_amount : "0",
                'place_of_buying' => isset($business_appraisal->place_of_buying) ? $business_appraisal->place_of_buying : "",
                'latitude' => isset($business_appraisal->latitude) ? (string)$business_appraisal->latitude : "0",
                'longitude' => isset($business_appraisal->longitude) ? (string)$business_appraisal->longitude : "0",
                'status' => isset($business_appraisal->status) ? $business_appraisal->status : '',
            );
            return $array;
        }
        return $array;
    }


    public static function parseVersions($versions)
    {
        $array = array();
        foreach ($versions as $version) {
            $array[$version->type] = (int)$version->version_no;
        }
        return $array;
    }
    public static function parseHousingLoanDetail($application){
        $arr=[];
        $member = isset($application->member) ? $application->member : '';
        if (!empty($member)) {
            $array['member_details'] = array(
                'name' => isset($member->full_name) ? $member->full_name : "",
                'profile_pic' => self::parseImageWeb(MemberHelper::getProfileImage($application->member->id), $application->member->id),
                'cnic' => isset($member->cnic) ? $member->cnic : "",
                'gender' => isset($member->gender) ? $member->gender : "",
                'dob' => isset($member->dob) ? date('d M,Y',$member->dob) : "",
                'marital_status' => isset($member->marital_status) ? $member->marital_status : "",
                'mobile' => isset($member->membersMobile->phone) ?  '0'.substr($member->membersMobile->phone,2): "",
                'address' => isset($member->businessAddress->address) ? $member->businessAddress->address : "",
                'country' =>  "Pakistan",
                'province' => isset($application->branch->province->name) ? $application->branch->province->name : "",
                'district' => isset($application->branch->district->name) ? $application->branch->district->name : "",
                'city' => isset($application->branch->city->name) ? $application->branch->city->name : "",
            );
        }
        $array['application_details'] = array(
            'application_id' => isset($application->id) ? $application->id : "",
            'application_no' => isset($application->application_no) ? $application->application_no : "",
            'application_date' => isset($application->application_date) ? date('d M,Y',$application->application_date) : "",
            'loan_purpose' => isset($application->activity->name) ? $application->activity->name : "",
            'region' => isset($application->region->name) ? $application->region->name : "",
            'area' => isset($application->area->name) ? $application->area->name : "",
            'branch' => isset($application->branch->name) ? $application->branch->name : "",
            'covered_area' => isset($application->housingAppraisal->residential_area) ? $application->housingAppraisal->residential_area : "",
        );
        if(!empty($application->loan)){
            $recovery=Recoveries::find()->where(['loan_id'=>$application->loan->id,'deleted'=>0])->sum('amount');
            $recovery=isset($recovery)?$recovery:0;
            $balance=$application->loan->disbursed_amount-($recovery);
            $array['loan_details'] = array(
                'loan_amount' => isset($application->loan->loan_amount) ? $application->loan->loan_amount : "",
                'disbursed_amount' => isset($application->loan->disbursed_amount) ? $application->loan->disbursed_amount : "",
                'outstanding_balance' => $balance,
                'date_disbursed' => date('d M,Y',$application->loan->date_disbursed),
                'no_of_tranches' => ($application->loan->loan_amount>200000)?2:1,
                'installment_amount' => isset($application->loan->inst_amnt)?$application->loan->inst_amnt:0,
            );
        }else{
            $array['loan_details'] = [];
        }
        if($application->loan->loan_amount>200000 && isset($application->loan->tranches[0]) && ($application->loan->tranches[0]->status==6)) {
            $array['loan_details']['second_tranche_disbursement'] = ($application->loan->tranches[0]->date_disbursed > 0) ? date('d M,Y', ($application->loan->tranches[0]->date_disbursed)) : '';
        }
        $array['visits_detail']=[];
        $details=ApplicationHelper::getVisitsByRole($application->id,true);
        $array['visits_detail']=$details;
        return $array;
    }
    public static function parseProfiles($headers){

        $cond='';
        $array=[];
        if(isset($headers['purpose']) && !empty($headers['purpose'])){
            $cond.=' and act.name="'.$headers['purpose'].'"';
        }
        if(isset($headers['province']) && !empty($headers['province'])){
            $cond.=' and b.province_id='.$headers['province'];
        }
        if(isset($headers['city']) && !empty($headers['city'])){
            $cond.=' and b.city_id='.$headers['city'];
        }
        if(isset($headers['city']) && !empty($headers['city'])){
            $cond.=' and b.city_id='.$headers['city'];
        }
        if(isset($headers['gender']) && !empty($headers['gender'])){
            $cond.=' and m.gender="'.$headers['gender'].'"';
        }
        if(isset($headers['branch']) && !empty($headers['branch'])){
            $cond.=' and b.id='.$headers['branch'];
        }
        if(isset($headers['date']) && !empty($headers['date'])){
            $dates = explode(' - ', $headers['date']);
            $cond.=' and l.date_disbursed between '.strtotime($dates[0]).' and '.strtotime($dates[1]);
        }
        if(isset($headers['date_single']) && !empty($headers['date_single'])){
            $date1=($headers['date_single']);
            $date2=date('Y-m-d 23:59',strtotime($headers['date_single']));
            //$dates = explode(' - ', $headers['date']);
            $cond.=' and l.date_disbursed between '.strtotime($date1).' and '.strtotime($date2);
        }
        $loans_query='select l.id from loans l 
                        inner join applications app on l.application_id=app.id 
                        inner join members m on m.id=app.member_id
                        inner join branches b on b.id=l.branch_id 
                        inner join activities act on act.id=app.activity_id
                        where 1 and l.status in ("collected")'.$cond;
        $loan_data = \Yii::$app->db->createCommand($loans_query)->queryAll();

        foreach($loan_data as $l){
            $loan=Loans::find()->where(['id'=>$l['id']])->one();
            $array[] = array(
                'name' => isset($loan->application->member->full_name) ? $loan->application->member->full_name : "",
                //'parentage' => isset($loan->application->member->parentage) ? $loan->application->member->parentage : "",
                'profileImage' => self::parseImageWeb(MemberHelper::getProfileImage($loan->application->member->id), $loan->application->member->id),
                'cnic' => isset($loan->application->member->cnic) ? $loan->application->member->cnic : "",
                'gender' => isset($loan->application->member->gender) ? $loan->application->member->gender : "",
                'dob' => isset($loan->application->member->dob) ? date('d M,Y',$loan->application->member->dob) : "",
                //'marital_status' => isset($loan->application->member->marital_status) ? $loan->application->member->marital_status : "",
                //'education' => isset($loan->application->member->education) ? $loan->application->member->education : "",
                //'mobile' => isset($loan->application->member->membersMobile->phone) ? $loan->application->member->membersMobile->phone : "",
                //'address' => isset($loan->application->member->businessAddress->address) ? $loan->application->member->businessAddress->address : "",
                'purpose' => isset($loan->application->activity->name) ? $loan->application->activity->name : "",
                'businrssType' => isset($loan->application->bzns_cond) ? $loan->application->bzns_cond : "",
                'sanctionNo' => isset($loan->sanction_no) ? $loan->sanction_no : "",
                'amount' => isset($loan->loan_amount) ? $loan->loan_amount : "",
                'city' => isset($loan->branch->city->name) ? $loan->branch->city->name : "",
                'age' => isset($loan->age) ? $loan->age : "",
            );
        }
        return $array;
    }

    public static function parseVigaProfiles($headers)
    {
        $viga_loans = VigaLoans::find()->where(['status' => 0, 'is_sync' => 0])->all();
        foreach ($viga_loans as $l) {
            $loan = Loans::find()->where(['id' => $l->loan_id])->one();
            $array[] = array(
                'name' => isset($loan->application->member->full_name) ? $loan->application->member->full_name : "",
                //'parentage' => isset($loan->application->member->parentage) ? $loan->application->member->parentage : "",
                'profileImage' => self::parseImageWeb(MemberHelper::getProfileImage($loan->application->member->id), $loan->application->member->id),
                'cnic' => isset($loan->application->member->cnic) ? $loan->application->member->cnic : "",
                'gender' => isset($loan->application->member->gender) ? $loan->application->member->gender : "",
                'dob' => isset($loan->application->member->dob) ? date('d M,Y',$loan->application->member->dob) : "",
                //'marital_status' => isset($loan->application->member->marital_status) ? $loan->application->member->marital_status : "",
                //'education' => isset($loan->application->member->education) ? $loan->application->member->education : "",
                //'mobile' => isset($loan->application->member->membersMobile->phone) ? $loan->application->member->membersMobile->phone : "",
                //'address' => isset($loan->application->member->businessAddress->address) ? $loan->application->member->businessAddress->address : "",
                'purpose' => isset($loan->application->activity->name) ? $loan->application->activity->name : "",
                'businrssType' => isset($loan->application->bzns_cond) ? $loan->application->bzns_cond : "",
                'sanctionNo' => isset($loan->sanction_no) ? $loan->sanction_no : "",
                'amount' => isset($loan->loan_amount) ? $loan->loan_amount : "",
                'city' => isset($loan->branch->city->name) ? $loan->branch->city->name : "",
                'age' => isset($loan->age) ? $loan->age : "",
            );
            $l->is_sync = 1;
            $l->updated_by = 1;
            $l->save();
        }
        return $array;
    }
    public static function parseListing(){
        $branches=Branches::find()->all();
        foreach($branches as $b){
            $array['branches'][] = array(
                'text' => isset($b->name) ? $b->name : "",
                'value' => isset($b->id) ? $b->id : "",

            );
        }
        $provinces=Provinces::find()->all();
        foreach($provinces as $p){
            $array['provinces'][] = array(
                'text' => isset($p->name) ? $p->name : "",
                'value' => isset($p->id) ? $p->id : "",
            );
        }
        return $array;
    }

}
