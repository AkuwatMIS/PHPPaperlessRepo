<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;
use common\models\Accounts;
use common\models\Activities;
use common\models\Areas;
use common\models\AuthItem;
use common\models\BranchAccount;
use common\models\BranchAccountMapping;
use common\models\Branches;
use common\models\Cities;
use common\models\Countries;
use common\models\CreditDivisions;
use common\models\Districts;
use common\models\Divisions;
use common\models\Fields;
use common\models\Products;
use common\models\Projects;
use common\models\Provinces;
use common\models\Regions;
use common\models\Teams;
use common\models\Users;
use common\models\BranchProjectsMapping;
use common\models\UserTransferHierarchy;
use Yii;

class StructureHelper
{

    static public function getDesignationTransfer($role){
        $sub_user_roles = UserTransferHierarchy::find()->select('value')->where(['role' => $role,'type' => 'promotion'])->asArray()->all();
        $roles = [];
        foreach ($sub_user_roles as $role)
        {
            $role_name = explode( ',',$role['value']);
            $roles = array_unique(array_merge($roles,$role_name));
        }
        return $roles;

    }

    static public function getDesignations(){
         return AuthItem::find()->select('name as id,description as name')->where(['type'=>1])->all();

    }
    static public function getProjects(){
        return Projects::find()->all();
    }
    static public function getProducts(){
        return Products::find()->all();
    }
    static public function BranchProjects($branch_id)
    {
            $rows = BranchProjectsMapping::find()
                ->where(['branch_id' => $branch_id])
                ->all();
            return $rows;

    }
    static public function BranchAccounts($branch_id)
    {
        $rows = BranchAccountMapping::find()
            ->where(['branch_id' => $branch_id])
            ->all();
        return $rows;

    }
    static public function getAccounts(){
        return Accounts::find()->all();
    }
    static public function getActivities(){
        return Activities::find()->all();
    }
    static public function getRegions(){
        return Regions::find()->all();
    }
    static public function getAreas(){
        return Areas::find()->all();
    }
    static public function getBranches($id=0){
        if($id!=0){
            return Branches::findOne($id);
        }else{
            return Branches::find()->all();
        }

    }
    static public function getCities(){
        return Cities::find()->all();
    }
    static public function getCreditDivision(){
        return CreditDivisions::find()->all();
    }
    static public function getDistricts(){
        return Districts::find()->all();
    }
    static public function getProvinces(){
        return Provinces::find()->all();
    }
    static public function getCountries(){
        return Countries::find()->all();
    }
    static public function getDivisions(){
        return Divisions::find()->all();
    }

    static public function getAreasByRegion($region_id){
        return Areas::find()->select(['id','name'])->where(['region_id'=>$region_id])->all();
    }
    static public function getBranchesByArea($area_id){
        return Branches::find()->select(['id','name'])->where(['area_id'=>$area_id])->all();
    }
    static public function getTeams($branch_id){
        return Teams::find()->select(['id','name'])->where(['branch_id'=>$branch_id])->all();
    }
    static public function getFields($team_id){
        return Fields::find()->select(['id','name'])->where(['team_id'=>$team_id])->all();
    }
    public static function getBranchprojects($branch)
    {
        return BranchProjectsMapping::find()->select('project_id')->where(['branch_id' => $branch])->asArray()->all();
    }

    public static function getProjectname($branch)
    {
        return Projects::find(/*['name']*/)->select('funding_line as id,name')->where(['id' => $branch])->asArray()->all();

    }
    public static function getProject($branch)
    {
        return Projects::find(/*['name']*/)->select('id,name')->where(['id' => $branch])->asArray()->all();

    }
    public static function getBranchidfromcode($branch_code)
    {
        return Branches::find()->select('id')->where(['code' => $branch_code])->asArray()->one();

    }
    static public function getBranchAccount($branch_id){
        $list = Yii::$app->db->createCommand('select acc_no,branch_id,bank_info,dt_opening,funding_line,purpose from accounts where branch_id = "'.$branch_id.'" ')->queryAll();

        $accounts = array();

        foreach($list as $one){
            $accounts[$one['purpose']] = $one;
        }

        return $accounts;
    }
    public static function getUserNameFromId($user_id)
    {
        return Users::find()->select(['id','username'])->where(['id' => $user_id])->one();

    }
    public static function getRegionAreaFromBranch($branch_id){
        return  Branches::find()->select(['region_id','area_id'])->where(['id' => $branch_id])->one();

    }
    public static function transferBranch($branch_id,$old_area,$new_area){
        $db = Yii::$app->db;
        $applications_query = "update applications set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($applications_query);
        $disbursements_query = "update disbursements set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($disbursements_query);
        $donations_query = "update donations set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($donations_query);
        $groups_query = "update groups set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($groups_query);
        $fund_requests_query = "update fund_requests set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($fund_requests_query);
        $loans_query = "update loans set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($loans_query);
        $members_query = "update members set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($members_query);
        $progress_report_details_query = "update progress_report_details set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($progress_report_details_query);
        $operations_query = "update operations set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($operations_query);
        $recoveries_query = "update recoveries set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($recoveries_query);
        $social_appraisals_query = "update appraisals_social set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($social_appraisals_query);
        $transactions_query = "update transactions set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($transactions_query);
        $user_structure_mapping_area_query = "update user_structure_mapping set obj_id = '".$new_area->id."' where obj_id = '".$old_area->id."' and obj_type = 'area'";
        $db->createCommand()->execute($user_structure_mapping_area_query);
        $user_structure_mapping_region_query = "update user_structure_mapping set obj_id = '".$new_area->region_id."' where obj_id = '".$old_area->region_id."' and obj_type = 'region'";
        $db->createCommand()->execute($user_structure_mapping_region_query);
        $branches_query = "update branches set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where id = '".$branch_id."'";
        $db->createCommand()->execute($branches_query);
    }
    public static function transferArea($area_id,$old_region,$new_region){
        $db = Yii::$app->db;
        $applications_query = "update applications set region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($applications_query);
        $disbursements_query = "update disbursements set  region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($disbursements_query);
        $donations_query = "update donations set  region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($donations_query);
        $groups_query = "update groups set region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($groups_query);
        $fund_requests_query = "update fund_requests set  region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($fund_requests_query);
        $loans_query = "update loans set  region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($loans_query);
        $members_query = "update members set region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($members_query);
        $progress_report_details_query = "update progress_report_details set  region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($progress_report_details_query);
        $operations_query = "update operations set  region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($operations_query);
        $recoveries_query = "update recoveries set  region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($recoveries_query);
        $social_appraisals_query = "update appraisals_social set  region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($social_appraisals_query);
        $transactions_query = "update transactions set region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($transactions_query);
        $user_structure_mapping_region_query = "update user_structure_mapping set obj_id = '".$new_region->region_id."' where obj_id = '".$old_region->region_id."' and obj_type = 'region'";
        $db->createCommand()->execute($user_structure_mapping_region_query);
        $branches_query = "update branches set region_id = '".$new_region->region_id."' where area_id = '".$area_id."'";
        $db->createCommand()->execute($branches_query);
        $branches_query = "update areas set region_id = '".$new_region->region_id."' where id = '".$area_id."'";
        $db->createCommand()->execute($branches_query);
    }
    public static function getTotal($provider, $fieldName)
    {
        $total = 0;

        foreach ($provider as $item) {
            $total += $item[$fieldName];
        }

        return $total;
    }
}