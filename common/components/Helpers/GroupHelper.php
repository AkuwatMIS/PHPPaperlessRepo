<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;


use common\components\Parsers\ApiParser;
use common\models\ApplicationDetails;
use common\models\Applications;
use common\models\Branches;
use common\models\Groups;
use common\models\Guarantors;
use common\models\Lists;
use common\models\Loans;
use common\models\SmsLogs;
use common\models\Images;
use yii\helpers\ArrayHelper;

class GroupHelper
{
    public static function getGroup($id)
    {
        $group = Groups::find()->where(['id' => $id])->one();
        return $group;

    }

    public static function getGroupsListFromApplications($applications){
        $groups_list = array();
        foreach ($applications as $application){
            if(!in_array($application->group_id,$groups_list)){
                $groups_list[] = $application->group_id;
            }
        }
        $groups = Groups::find()->where(['in','id',$groups_list])->all();
        return $groups;
    }

    public static function getGroupMembersLoans($id)
    {
        $members = [];
        $loans = Loans::find()->where(['group_id' => $id])->where(['!=','status','collected'])->all();
        $data = [];
        foreach ($loans as $loan)
        {

            $member = ApiParser::parseMemberBasicInfo($loan->application->member);
            $member['thumb_impression'] = '';
            $application_data = self::getMemberApplication($loan->application_id);
            $loan_data = ApiParser::parseLoanBasicInfo($loan);
            $application_data = array_merge($application_data, ['loan' => $loan_data]);
            $data[] = array_merge($member, ['application' => $application_data]);
            //$members[] = array_merge($member, ['application_id' => $application->id,'application_no' => $application->application_no]);
        }
        return $data;
    }

    public static function getGroupMembers($id)
    {
        $members = [];
        $applications = Applications::find()/*->joinWith('loan')*/->where(['applications.group_id' => $id])/*->andWhere(['!=','loans.status','collected'])*/->all();
        $data = [];
        foreach ($applications as $application)
        {
            if(isset($application->loan) && !empty($application->loan))
            {
                if(!isset($application->loan->active) || empty($application->loan->active))
                {
                    $member = ApiParser::parseMemberBasicInfo($application->member);
                    $member['thumb_impression'] = '';
                    $application_data = self::getMemberApplication($application->id);
                    $loan_data = ApiParser::parseLoanBasicInfo($application->loan);
                    $application_data = array_merge($application_data, ['loan' => $loan_data]);
                    $data[] = array_merge($member, ['application' => $application_data]);
                }
            }
            else {
                $member = ApiParser::parseMemberBasicInfo($application->member);
                $member['thumb_impression'] = '';
                $application_data = self::getMemberApplication($application->id);
                $loan_data = ApiParser::parseLoanBasicInfo($application->loan);
                $application_data = array_merge($application_data, ['loan' => $loan_data]);
                $data[] = array_merge($member, ['application' => $application_data]);
            }
           //$members[] = array_merge($member, ['application_id' => $application->id,'application_no' => $application->application_no]);
        }
        return $data;
    }

    public static function getGroupMembersBasicData($id)
    {
        $members = [];
        $applications = Applications::find()->where(['group_id' => $id])->all();
        $data = [];
        foreach ($applications as $application)
        {
            $member = ApiParser::parseApplicationInfo($application);
            $member = array_merge($member, ['verification_details' => ApplicationHelper::getInfoByApplicationForSE($application)]);
            $loan = Loans::find()
                ->join('inner join','loan_tranches','loans.id= loan_tranches.loan_id')
                ->where(['loans.status' => 'collected','loans.deleted'=>0, 'loan_tranches.status' =>1])
                ->andWhere(['in','project_id',StructureHelper::trancheInProjects()])->andWhere(['loans.application_id' => $application->id])->one();
            $tranch_details = null;
            if(isset($loan) && !empty($loan))
            {
                    $tranch_details['tranches'] =  ApiParser::parseLoanTranches($loan->tranches);
                    $tranch_details['percent'] =  LoanHelper::getCompletionPercentage($loan->application_id);
                    $tranch_details['is_shifted'] =  ApplicationDetails::getShifted($loan->application_id);
            }
            $data[] = array_merge($member, ['tranche_details' => $tranch_details]);
        }
        return $data;
    }


    public static function getGroupMembersCount($id)
    {
        $members_count = 0;
        $members_count = Applications::find()->where(['group_id' => $id])->count('id');
        return $members_count;
    }

    public static function getGroupMembersCountByLoanId($id)
    {
        $members_count = 0;
        $members_count = Applications::find()->where(['group_id' => $id])->count('id');
        return $members_count;
    }

    public static function getGaurantors($id)
    {
        $gaurantor_list = [];
        $guarantors = Guarantors::find()->where(['group_id' => $id, 'deleted' => 0])->all();
        foreach ($guarantors as $guarantor)
        {
            $data = ApiParser::parseGuarantor($guarantor);
            $data['cnic_front'] = ApiParser::parseImage(self::getFCNIC($guarantor->id), $guarantor->id);
            $data['cnic_back'] = ApiParser::parseImage(self::getBCNIC($guarantor->id), $guarantor->id);
            $gaurantor_list[] = $data ;
        }
        return $gaurantor_list;
    }

    public static function getGroupLoans($id)
    {
        $loan_data = [];
        $loans = Loans::find()->where(['group_id' => $id])->all();
        foreach ($loans as $loan)
        {
            $loan_data[] = ApiParser::parseLoan($loan);
        }
        return $loan_data;
    }

    public static function getGroupApplications($id)
    {
        $application_data = [];
        $applications = Applications::find()->where(['group_id' => $id])->all();
        foreach ($applications as $application)
        {
            $application_data[] = ApiParser::parseApplication($application);
        }
        return $application_data;
    }

    public static function getMemberApplication($id)
    {
        $application_data = "";
        $application = Applications::find()->where(['id' => $id])->one();
        $application_data = ApiParser::parseApplicationBasicInfo($application);
        return $application_data;
    }

    public static function getApplicationLoan($id, $group_id)
    {
        $loan_data = '';
        $loan = Loans::find()->where(['application_id' => $id,'group_id' => $group_id])->one();
        $loan_data = ApiParser::parseLoan($loan);
        return $loan_data;
    }

    public static function getFCnic($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'guarantors', 'image_type' => 'cnic_front']);
        return $image;
    }

    public static function getBCnic($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'guarantors', 'image_type' => 'cnic_back']);
        return $image;
    }

    public static function createGroup($branch, $group){
        $last_group = Groups::find()->where(['branch_id'=>$branch->id, 'grp_type'=>$group->grp_type,'deleted'=>0])->orderBy('br_serial desc')->one();
        $new_br_serial = (isset($last_group->br_serial) ? (int)$last_group->br_serial : 0) + 1;
        $grpno =  $group->grp_type . '-' . $branch->code . '-' . str_pad($new_br_serial, 4, '0', STR_PAD_LEFT);
        $group->region_id = $branch->region_id;
        $group->area_id = $branch->area_id;
        $group->br_serial = $new_br_serial;
        $group->grp_no = $grpno;
        if($group->save()){
            return $group;
        }else{
            return $group;
            /*print_r($group->getErrors());
            die();*/
        }
    }
    public static function validateGroupSize($applications){
        foreach($applications as $app){
           $project_id= $app->project_id;
        }
        if(count($applications) != 1 && (count($applications) < 3 || count($applications) > 6) && $project_id!=124 && $project_id!=79 && $project_id!=115){
            return false;
        }
        else if (in_array($applications->project_id, [124, 115]) && count($applications) != 1 && ((count($applications) < 3) || (count($applications) > 15))){
            return false;
        }
        else{
           return true;
        }
    }

    public static function checkGuarantors($attributes,$application_id){
        $guarantorVerify = false;
        $guarantorArray = [];
        foreach ($attributes as $key => $guarantor) {
            if (!empty($guarantor['cnic'])) {
                $guarantorArray[$key] = $guarantor['cnic'];
            }
        }

        $applicantModel = Applications::find()->select(['members.cnic','applications.branch_id'])
            ->joinWith('member')
            ->where(['applications.id'=>$application_id])
            ->one();
        if(!empty($applicantModel) && $applicantModel!=null){
            $applicantAsGuarantorIndividual = Guarantors::find()
                ->where(['cnic' => $applicantModel->cnic, 'deleted' => '0'])
                ->one();
            if(!empty($applicantAsGuarantorIndividual) && $applicantAsGuarantorIndividual!=null){
                $group = Groups::find()
                    ->where(['id'=>$applicantAsGuarantorIndividual->group_id])
                    ->one();
                if(!empty($group) && $group!=null){

                    $applicantAsGuarantorWithSecondGuarantor = Guarantors::find()
                        ->where(['group_id' => $group->id, 'deleted' => '0'])
                        ->all();

                    foreach ($applicantAsGuarantorWithSecondGuarantor as $guarantor){
                        if($guarantor->cnic == $applicantModel->cnic){
                            $GuarantorForApplicant = Applications::find()->select(['members.cnic','applications.branch_id'])
                                ->joinWith('member')
                                ->where(['applications.group_id'=>$guarantor->group_id])
                                ->one();

                            if($GuarantorForApplicant->branch_id == $applicantModel->branch_id){
                                if(in_array($GuarantorForApplicant->cnic,$guarantorArray)){
                                    $guarantorVerify = true;
                                }else{
                                    $guarantorVerify = false;
                                }
                            }else{
                                $guarantorVerify = false;
                            }
                        }else{

                            if(in_array($guarantor->cnic,$guarantorArray)){
                                $guarantorVerify = true;
                            }else{
                                $guarantorVerify = false;
                            }

                        }
                    }

                }else{
                    $guarantorVerify = true;
                }

            }else{
                $guarantorVerify = true;
            }
        }

        return $guarantorVerify;
    }

    public static function saveGuarantor($attributes, $group_id, $platform = 1,$loan_check=0,$application_id)
    {
//        $guarantorLoan = Applications::find()->select(['members.id', 'members.cnic', 'applications.branch_id'])
//            ->joinWith('member')
//            ->joinWith('loan')
//            ->where(['cnic' => $attributes['cnic'], 'loans.status' => "collected"])
//            ->one();


        if (isset($attributes['id']) && !empty($attributes['id'])) {
            $model = Guarantors::find()->where(['id' => $attributes['id'], 'deleted' => '0'])->one();
        } else {
            $model = new Guarantors();
        }
        $model->group_id = $group_id;
        $model->attributes = $attributes;
        if($platform == 2)
        {
            $model->phone = '92'.ltrim($attributes['phone'],'0');
        }
        $model->platform = $platform;
        $blacklist_member = BlacklistHelper::checkBlacklist($model->cnic);
        if(!empty($blacklist_member)){
            $model->reject_reason='cnic of guarantor is in blacklist'.$model->cnic."(".$blacklist_member->reason.')';
            $model->addError('cnic', "Guarantor's CNIC is blacklisted So Group not generated and application rejected");
        }
//        if (isset($loan) && $loan_check==0) {
//            $model->addError('cnic', 'Guarantor have active loan So Group not generated.');
//        }

        if ($model->validate(null, false) && $model->save()) {
            return $model;
        } else {
            return $model;
        }
    }
}