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
use common\models\ApiKeys;
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
use common\models\UserProjectsMapping;
use common\models\Users;
use common\models\BranchProjectsMapping;
use common\models\UserStructureMapping;
use common\models\Versions;
use Yii;

class StructureHelper
{

    static public function getStructure($key)
    {
        $list = CacheHelper::getStructure($key);
        if(empty($list)) {
            if ($key == 'divisions') {
                CacheHelper::setStructure($key, Divisions::find()->select('id,name,province_id')->asArray()->all());
            } else if ($key == 'regions') {
                CacheHelper::setStructure($key, Regions::find()->select('regions.id,regions.name,regions.code,regions.cr_division_id')->asArray()->all());
            } else if ($key == 'areas') {
                CacheHelper::setStructure($key, Areas::find()->select('areas.id,areas.name,areas.code,areas.region_id,regions.name as region_name,regions.cr_division_id')->join('inner join', 'regions', 'regions.id=areas.region_id')->asArray()->all());
            } else if ($key == 'branches') {
                CacheHelper::setStructure($key, Branches::find()->select('branches.id,branches.name,branches.code,branches.area_id,branches.region_id,areas.name as area_name,regions.name as region_name,regions.cr_division_id')->join('inner join', 'areas', 'areas.id=branches.area_id')->join('inner join', 'regions', 'regions.id=branches.region_id')->where(['branches.status' => 1])->asArray()->all());
            } else if ($key == 'teams') {
                CacheHelper::setStructure($key, Teams::find()->select('teams.id,teams.name,teams.branch_id,branches.name as branch_name, branches.area_id,branches.region_id,areas.name as area_name,regions.name as region_name,regions.cr_division_id')->join('inner join', 'branches', 'branches.id=teams.branch_id')->join('inner join', 'areas', 'areas.id=branches.area_id')->join('inner join', 'regions', 'regions.id=branches.region_id')->asArray()->all());
            } else if ($key == 'fields') {
                CacheHelper::setStructure($key, Fields::find()->select('fields.id,fields.name,fields.team_id,teams.name as team_name,teams.branch_id,branches.name as branch_name, branches.area_id,branches.region_id,areas.name as area_name,regions.name as region_name,regions.cr_division_id')->join('inner join', 'teams', 'teams.id=fields.team_id')->join('inner join', 'branches', 'branches.id=teams.branch_id')->join('inner join', 'areas', 'areas.id=branches.area_id')->join('inner join', 'regions', 'regions.id=branches.region_id')->asArray()->all());
            } else if ($key == 'projects') {
                CacheHelper::setStructure($key, Fields::find()->asArray()->all());
            } else if ($key == 'branch_projects') {
                CacheHelper::setStructure($key , BranchProjectsMapping::find()->select('branch_id,project_id,branches.name as branch_name,projects.name as project_name')->join('inner join', 'branches', 'branches.id=branch_projects_mapping.branch_id')->join('inner join', 'projects', 'projects.id=branch_projects_mapping.project_id')->asArray()->all());
            } else if ($key == 'countries') {
                CacheHelper::setStructure($key,Countries::find()->select('id,name,continent')->orderBy(['name'=>SORT_ASC])->asArray()->all());
            } else if ($key == 'provinces') {
                CacheHelper::setStructure($key,Provinces::find()->select('id,name,country_id')->orderBy(['name'=>SORT_ASC])->asArray()->all());
            } else if ($key == 'districts') {
                CacheHelper::setStructure($key, Districts::find()->select('districts.id,districts.name,districts.code,districts.division_id,divisions.name as disvision_name,divisions.province_id')->join('inner join', 'divisions', 'divisions.id=districts.division_id')->join('inner join', 'provinces', 'provinces.id=divisions.province_id')->orderBy(['name'=>SORT_ASC])->asArray()->all());
            }
        }
        $list = CacheHelper::getStructure($key);
        return $list;
    }

    static public function getStructureList($list,$column,$ids)
    {
        $array = [];
        $structure_data = self::getStructure($list);

        foreach ($structure_data as $data)
        {
            /*if($list == 'teams')
            {
                print_r(is_array($ids) .' fg ');
                print_r($data["$column"] .' j ');
                print_r($ids);
                print_r($data);
                print_r((string)$data["$column"] == (string)$ids);
                die();
            }*/
            if(is_array($ids))
            {
                if(in_array($data["$column"] ,$ids))
                {
                    $array[] =  $data;
                }
            }
            else if($data["$column"] == $ids) {


                $array[] = $data;
            }
        }

        return $array;
    }
    /*static public function setUserStructure($user_id)
    {
        $user_structure = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        foreach ($user_structure as $key => $item) {
            $arr[$item['obj_type']][] = $item['obj_id'];
        }
        $user_projects = UserProjectsMapping::find()->select('project_id')->where(['user_id' => Yii::$app->user->getId()])->all();
        foreach ($user_projects as $item) {
            $arr['project'][] = $item['project_id'];
        }
        CacheHelper::setUserIdentity($user_id, 'divisions', $arr['devision']);
        CacheHelper::setUserIdentity($user_id, 'regions', $arr['region']);
        CacheHelper::setUserIdentity($user_id, 'areas', $arr['area']);
        CacheHelper::setUserIdentity($user_id, 'branches', $arr['branch']);
        CacheHelper::setUserIdentity($user_id, 'teams', $arr['team']);
        CacheHelper::setUserIdentity($user_id, 'fields', $arr['field']);
        CacheHelper::setUserIdentity($user_id, 'projects', $arr['project']);
    }*/

    static public function getUserStructure($user_id,$key)
    {
        $data = CacheHelper::getUserIdentity($user_id,$key);
        if(empty($data))
        {
            $structure_type = ['divisions' => 'division','regions' => 'region','areas' => 'area','branches' => 'branch','teams' => 'team','fields' => 'field'];
            if($key == 'divisions' || $key == 'regions' || $key == 'areas' || $key == 'branches' || $key == 'teams' || $key == 'fields') {
                $user_structure = UserStructureMapping::find()->where(['user_id' => $user_id])->andWhere(['obj_type' => $structure_type[$key]])->all();
                foreach ($user_structure as $key => $item) {
                    $data[] = $item['obj_id'];
                }
                CacheHelper::setUserIdentity($user_id, $key, $data);
            }
            else if($key == 'projects') {
                $user_projects = UserProjectsMapping::find()->select('project_id')->where(['user_id' => $user_id])->all();
                foreach ($user_projects as $item) {
                    $data[] = $item['project_id'];
                }
                CacheHelper::setUserIdentity($user_id, $key, $data);
            }
        }
        return $data;
    }

    public static function getAppVersion()
    {
        $key = 'app_version';
        $app_version = CacheHelper::getConfig($key);
        if(!isset($app_version)) {
            $app_version = Versions::find()->select('version_no')->where(['type' => 'App Version'])->one();
            CacheHelper::setConfig($key, $app_version->version_no);
        }

        return $app_version;
    }

    public static function getApiKey()
    {

        $key = 'api_keys';
        $api_keys = CacheHelper::getConfig($key);

        if(empty($api_keys)) {
            $api_keys_data = ApiKeys::find()->select('api_key')->all();
            $api_keys=[];
            foreach ($api_keys_data as $k)
            {
                $api_keys[] =$k->api_key;
            }
            CacheHelper::setConfig($key, $api_keys);
        }

        return $api_keys;
    }

    public static function getVersions()
    {

        $key = 'api_versions';
        $versions = CacheHelper::getConfig($key);
        if(empty($versions)) {
            $versions = Versions::find()->all();
            CacheHelper::setConfig($key,$versions);
        }

        return $versions;
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
        return Activities::find()->where(['deleted'=>0])->all();
    }
    static public function getRegions(){
        return Regions::find()->where(['deleted'=>0])->all();
    }
    static public function getAreas(){
        return Areas::find()->where(['deleted'=>0])->all();
    }
    static public function getBranches($id=0){
        if($id!=0){
            return Branches::find()->where(['id'=>$id,'deleted'=>0]);
        }else{
            return Branches::find()->where(['deleted'=>0])->all();
        }
    }
    static public function getBranch($id=0){
        if($id!=0){
            return Branches::find()->where(['id'=>$id,'deleted'=>0])->one();
        }else{
            return Branches::find()->where(['deleted'=>0])->all();
        }
    }
    static public function getCities(){
        return Cities::find()->where(['deleted'=>0])->all();
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