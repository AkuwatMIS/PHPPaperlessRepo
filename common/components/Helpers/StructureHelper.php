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
use common\models\Loans;
use common\models\Products;
use common\models\Projects;
use common\models\Provinces;
use common\models\Referrals;
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
    public static function accountVerifyProjects()
    {
        $project_ids = [52, 61, 62, 64, 67, 76, 77, 78, 79, 90, 83, 97, 103, 109, 105, 106, 118, 127, 132, 136, 134];
        return $project_ids;
    }

    public static function kamyaabPakitanProjects()
    {
        $project_ids = [77, 78, 79, 105, 106, 132];
        return $project_ids;
    }

    public static function accountkamyaabPakitanProjects()
    {
        $project_ids = [77, 78, 79, 105, 106, 132,];
        return $project_ids;
    }

    public static function kamyaabPakitanKarobarKistan()
    {
        $project_ids = [78, 79, 105, 106, 132];
        return $project_ids;
    }

    public static function trancheProjectsExclude()
    {
        $project_ids = [52, 61, 62, 64, 67, 76, 77, 90, 97, 113, 98, 103, 108, 109, 127, 132];
        return $project_ids;
    }

    public static function trancheProjects()
    {
        $project_ids = [52, 61, 62, 64, 67, 76, 77, 83, 90, 97, 113, 98, 103, 108, 109, 110, 114, 100, 118, 119, 127, 24, 132, 131,35,138,143/*,90,78,79*/];
        return $project_ids;
    }

    public static function trancheProjectsNotIn()
    {
        $project_ids = [52, 61, 62, 64, 67, 76, 77, 83, 90, 97, 103, 109, 127, 132 /*,90,78,79*/];
        return $project_ids;
    }

    public static function trancheInProjects()
    {
        $project_ids = [52, 61, 62, 64, 67, 76, 77, 83, 90, 97, 113, 98, 103, 108, 109, 127, 132, 136];
        return $project_ids;
    }

    public static function trancheProjectsLac()
    {
        $project_ids = [52, 61, 62, 64, 67, 76, 77, 90, 97, 113, 98, 100, 103, 108, 109, 114, 110, 127, 132, 136,138/*,78,79*/];
        return $project_ids;
    }

    public static function trancheProjectsReject()
    {
        $project_ids = [52, 97, 61, 62, 64, 67, 76, 77, 90, 78, 79, 98, 108, 103, 109, 105, 106, 127, 132];
        return $project_ids;
    }

    public static function tranchesProjects()
    {
        $project_ids = [52, 61, 62, 64, 67, 76, 83, 90, 97, 113, 98, 100, 103, 109, 110, 114, 118,35, 119, 126, 127, 24, 132, 136, 131 ,138,143];
        return $project_ids;
    }

    public static function verifyProjectsDocument()
    {
        $project_ids = [52, 61, 62, 64, 67, 76, 77, 78, 79, 97, 103, 127, 132];
        return $project_ids;
    }

    public static function trancheProjectsList()
    {
        $project_ids = [52, 61, 62, 64, 67, 76, 77, 83, 90, 97, 103, 127, 132];
        return $project_ids;
    }

    public static function installmentProjectsList()
    {
        $project_ids = [52, 61, 62, 64, 67, 77, 83, 90, 97, 98, 103, 109, 110, 114, 127, 132, 136,138];
        return $project_ids;
    }

    public static function ChequeFlow()
    {
        $ids = [1];
        return $ids;
    }

    public static function autoMonthProjects()
    {
        $project_ids = [59];
        return $project_ids;
    }

    public static function autoSelectMonthProjects()
    {
        $project_ids = [59, 52, 61, 62, 64, 67, 76, 77, 78, 87, 89, 83, 90, 97, 113, 98, 103, 109, 127, 132, 135];
        return $project_ids;
    }

    public static function withoutCheckProjects()
    {
//        $project_ids = [59,60];
//        $project_ids = [59,30];
        return $project_ids;
    }

    public static function closedBranches()
    {
        return $arr = [
            109,
            1808,
            1202,
            1904,
            309,
            711,
            1111,
            3303,
            820,
            1737,
            822,
            1742,
            1006,
            1411,
            1911,
            114,
            2821,
            834,
            2311,
            612,
            3125,
            1611,
            1118,
            1753,
            1754,
            2116,
            1213,
            1214,
            314,
            2831,
            2933,
            839,
            3014,
            3018,
            1757,
            1758,
            3517,
            1509,
            3123,
            3142,
            3144,
            2117,
            1613,
            1009,
            1011,
            2612,
            1418,
            1759,
            1215,
            3601,
            1704,
            403,
            14302,
            1730,
            1732,
            3503,
            109,
            1808,
            1202,
            1904,
            309,
            711,
            1111,
            3303,
            820,
            1737,
            822,
            1742,
            1006,
            1411,
            1911,
            114,
            2821,
            834,
            2311,
            612,
            3125,
            1611,
            1118,
            1753,
            1754,
            2116,
            1213,
            1214,
            314,
            2831,
            2933,
            839,
            3014,
            3018,
            1757,
            1758,
            3517,
            1509,
            3123,
            3142,
            3144,
            2117,
            1613,
            1009,
            1011,
            2612,
            1418,
            1759,
            1215,
            1718,
            3018,
            1738
        ];
    }

    static public function getStructure($key)
    {
        Yii::$app->cache->flush();
        $list = [];
        $list = CacheHelper::getStructure($key);
        if (empty($list)) {
            if ($key == 'divisions') {
                $list = Divisions::find()->select('id,name,province_id')->asArray()->all();
            } else if ($key == 'regions') {
                $list = Regions::find()->select('regions.id,regions.name,regions.code,regions.cr_division_id')->asArray()->all();
            } else if ($key == 'areas') {
                $list = Areas::find()->select('areas.id,areas.name,areas.code,areas.region_id,regions.name as region_name,regions.cr_division_id')->join('inner join', 'regions', 'regions.id=areas.region_id')->asArray()->all();
            } else if ($key == 'branches') {
                $list = Branches::find()->select('branches.id,branches.name,branches.code,branches.area_id,branches.region_id,areas.name as area_name,regions.name as region_name,regions.cr_division_id')->join('inner join', 'areas', 'areas.id=branches.area_id')->join('inner join', 'regions', 'regions.id=branches.region_id')->where(['branches.status' => 1])->asArray()->all();
            } else if ($key == 'teams') {
                $list = Teams::find()->select('teams.id,teams.name,teams.branch_id,branches.name as branch_name, branches.area_id,branches.region_id,areas.name as area_name,regions.name as region_name,regions.cr_division_id')->join('inner join', 'branches', 'branches.id=teams.branch_id')->join('inner join', 'areas', 'areas.id=branches.area_id')->join('inner join', 'regions', 'regions.id=branches.region_id')->asArray()->all();
            } else if ($key == 'fields') {
                $list = Fields::find()->select('fields.id,fields.name,fields.team_id,teams.name as team_name,teams.branch_id,branches.name as branch_name, branches.area_id,branches.region_id,areas.name as area_name,regions.name as region_name,regions.cr_division_id')->join('inner join', 'teams', 'teams.id=fields.team_id')->join('inner join', 'branches', 'branches.id=teams.branch_id')->join('inner join', 'areas', 'areas.id=branches.area_id')->join('inner join', 'regions', 'regions.id=branches.region_id')->asArray()->all();
            } else if ($key == 'projects') {
                $list = Projects::find()->asArray()->all();
            } else if ($key == 'branch_projects') {
                $list = BranchProjectsMapping::find()->select('branch_id,project_id,branches.name as branch_name,projects.name as project_name')->join('inner join', 'branches', 'branches.id=branch_projects_mapping.branch_id')->join('inner join', 'projects', 'projects.id=branch_projects_mapping.project_id')->asArray()->all();
            } else if ($key == 'countries') {
                $list = Countries::find()->select('id,name,continent')->orderBy(['name' => SORT_ASC])->asArray()->all();
            } else if ($key == 'provinces') {
                $list = Provinces::find()->select('id,name,logo,country_id')->orderBy(['name' => SORT_ASC])->asArray()->all();
                unset($list[5]); // to remove x-FATA from application list
                foreach ($list as $k => $v) {
                    $list[$k]['logo'] = ImageHelper::getAttachmentApiPath() . '?type=provinces&file_name=' . $v['logo'] . '&download=true';
                }
            } else if ($key == 'districts') {
                $list = Districts::find()->select('provinces.name as province_name,districts.id,districts.name,districts.code,districts.division_id,divisions.name as disvision_name,divisions.province_id')->join('inner join', 'divisions', 'divisions.id=districts.division_id')->join('inner join', 'provinces', 'provinces.id=divisions.province_id')->orderBy(['name' => SORT_ASC])->asArray()->all();
            }
            CacheHelper::setStructure($key, $list);
        }
        return $list;
    }

    static public function getStructureList($list, $column, $ids)
    {
        $array = [];
        $structure_data = StructureHelper::getStructure($list);
        if (!empty($structure_data)) {

            foreach ($structure_data as $data) {
                if (is_array($ids)) {
                    if (in_array($data[$column], $ids)) {
                        $array[] = $data;
                    }
                } else if ($data[$column] == $ids) {
                    $array[] = $data;
                }
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

    static public function getUserStructure($user_id, $key)
    {
        $data = CacheHelper::getUserIdentity($user_id, $key);
        if (empty($data)) {
            $structure_type = ['divisions' => 'division', 'regions' => 'region', 'areas' => 'area', 'branches' => 'branch', 'teams' => 'team', 'fields' => 'field'];
            if ($key == 'divisions' || $key == 'regions' || $key == 'areas' || $key == 'branches' || $key == 'teams' || $key == 'fields') {
                $user_structure = UserStructureMapping::find()->where(['user_id' => $user_id])->andWhere(['obj_type' => $structure_type[$key]])->all();
                foreach ($user_structure as $key => $item) {
                    $data[] = $item['obj_id'];
                }
                CacheHelper::setUserIdentity($user_id, $key, $data);
            } else if ($key == 'projects') {
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
        if (!isset($app_version)) {
            $app_version = Versions::find()->select('version_no')->where(['type' => 'App Version'])->one();
            CacheHelper::setConfig($key, $app_version->version_no);
        }

        return $app_version;
    }

    public static function getApiKey()
    {

        $key = 'api_keys';
        $api_keys = CacheHelper::getConfig($key);

        if (empty($api_keys)) {
            $api_keys_data = ApiKeys::find()->select('api_key')->all();
            $api_keys = [];
            foreach ($api_keys_data as $k) {
                $api_keys[] = $k->api_key;
            }
            CacheHelper::setConfig($key, $api_keys);
        }

        return $api_keys;
    }

    public static function getVersions()
    {

        /*$key = 'api_versions';
        $versions = CacheHelper::getConfig($key);
        if(empty($versions)) {*/
        $versions = Versions::find()->all();
        /*CacheHelper::setConfig($key,$versions);
    }*/

        return $versions;
    }

    static public function getDesignations()
    {
        return AuthItem::find()->select('name as id,description as name')->where(['type' => 1])->all();

    }

    static public function getProjects()
    {
        return Projects::find()
//            ->where(['status' => 1])
            ->all();
    }

    static public function getHousingProjects()
    {
        $projects = array(52, 61, 62, 64, 67, 76, 77, 83, 90, 103, 132);
        return Projects::find()->where(['status' => 1])->andWhere(['in', 'id', $projects])->all();
    }

    static public function getReferrals()
    {
        return Referrals::find()->where(['status' => 1])->all();
    }

    static public function getProducts()
    {
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

    static public function getAccounts()
    {
        return Accounts::find()->all();
    }

    static public function getActivities()
    {
        return Activities::find()->where(['deleted' => 0])->all();
    }

    static public function getRegions()
    {
        return Regions::find()->where(['deleted' => 0])->all();
    }

    static public function getAreas()
    {
        return Areas::find()->where(['deleted' => 0])->all();
    }

    static public function getBranches($id = 0)
    {
        if ($id != 0) {
            return Branches::find()->where(['id' => $id, 'deleted' => 0]);
        } else {
            return Branches::find()->where(['deleted' => 0])->all();
        }
    }

    static public function getBranch($id = 0)
    {
        if ($id != 0) {
            return Branches::find()->where(['id' => $id, 'deleted' => 0])->one();
        } else {
            return Branches::find()->where(['deleted' => 0])->all();
        }
    }

    static public function getCities()
    {
        return Cities::find()->where(['deleted' => 0])->all();
    }

    static public function getCreditDivision()
    {
        return CreditDivisions::find()->all();
    }

    static public function getDistricts()
    {
        return Districts::find()->all();
    }

    static public function getProvinces()
    {
        return Provinces::find()->all();
    }

    static public function getCountries()
    {
        return Countries::find()->all();
    }

    static public function getDivisions()
    {
        return Divisions::find()->all();
    }

    static public function getAreasByRegion($region_id)
    {
        return Areas::find()->select(['id', 'name'])->where(['region_id' => $region_id])->all();
    }

    static public function getBranchesByArea($area_id)
    {
        return Branches::find()->select(['id', 'name'])->where(['area_id' => $area_id])->all();
    }

    static public function getTeams($branch_id)
    {
        return Teams::find()->select(['id', 'name'])->where(['branch_id' => $branch_id])->all();
    }

    static public function getFields($team_id)
    {
        return Fields::find()->select(['id', 'name'])->where(['team_id' => $team_id])->all();
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

    static public function getBranchAccount($branch_id)
    {
        $list = Yii::$app->db->createCommand('select acc_no,branch_id,bank_info,dt_opening,funding_line,purpose from accounts where branch_id = "' . $branch_id . '" ')->queryAll();

        $accounts = array();

        foreach ($list as $one) {
            $accounts[$one['purpose']] = $one;
        }

        return $accounts;
    }

    public static function getUserNameFromId($user_id)
    {
        return Users::find()->select(['id', 'username'])->where(['id' => $user_id])->one();

    }

    public static function getRegionAreaFromBranch($branch_id)
    {
        return Branches::find()->select(['region_id', 'area_id'])->where(['id' => $branch_id])->one();

    }

    public static function transferBranch($branch_id, $old_area, $new_area)
    {
        $db = Yii::$app->db;
        $applications_query = "update applications set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($applications_query)->execute();
        $disbursements_query = "update disbursements set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($disbursements_query)->execute();
        $donations_query = "update donations set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($donations_query)->execute();
        $groups_query = "update groups set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($groups_query)->execute();
        $fund_requests_query = "update fund_requests set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($fund_requests_query)->execute();
        $loans_query = "update loans set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($loans_query)->execute();
        $members_query = "update members set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($members_query)->execute();
        $progress_report_details_query = "update progress_report_details set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($progress_report_details_query)->execute();
        $operations_query = "update operations set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($operations_query)->execute();
        $recoveries_query = "update recoveries set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where branch_id = " . $branch_id . "";
        $db->createCommand($recoveries_query)->execute();
        /*$social_appraisals_query = "update appraisals_social set area_id = '".$new_area->id."', region_id = '".$new_area->region_id."' where branch_id = '".$branch_id."'";
        $db->createCommand()->execute($social_appraisals_query);*/
        /*$transactions_query = "update transactions set area_id = ".$new_area->id.", region_id = ".$new_area->region_id." where branch_id = ".$branch_id."";
        $db->createCommand($transactions_query)->execute();*/
        /*$user_structure_mapping_area_query = "update user_structure_mapping set obj_id = ".$new_area->id." where obj_id = ".$old_area->id." and obj_type = 'area'";
        $db->createCommand($user_structure_mapping_area_query)->execute();
        $user_structure_mapping_region_query = "update user_structure_mapping set obj_id = ".$new_area->region_id." where obj_id = ".$old_area->region_id." and obj_type = 'region'";
        $db->createCommand($user_structure_mapping_region_query)->execute();*/

//        $user_structure_mapping_area_query = "update user_structure_mapping usm INNER JOIN users u ON u.id=usm.user_id set usm.obj_id = ".$new_area->id." where u.designation_id IN (9,19) and usm.obj_id = ".$old_area->id." and usm.obj_type = 'area'";
//        $db->createCommand($user_structure_mapping_area_query)->execute();
        //$user_structure_mapping_region_query = "update user_structure_mapping um INNER JOIN users ur ON ur.id=um.user_id set um.obj_id = ".$new_area->region_id." where ur.designation_id IN (9,19) and um.obj_id = ".$old_area->region_id." and um.obj_type = 'region'";
        //$db->createCommand($user_structure_mapping_region_query)->execute();

        $branches_query = "update branches set area_id = " . $new_area->id . ", region_id = " . $new_area->region_id . " where id = " . $branch_id . "";
        $db->createCommand($branches_query)->execute();
        $awp_query = "update awp set area_id = '" . $new_area->id . "', region_id = '" . $new_area->region_id . "' where branch_id = '" . $branch_id . "'";
        $db->createCommand($awp_query)->execute();
        $awp_branch_sustainability_query = "update awp_branch_sustainability set area_id = '" . $new_area->id . "', region_id = '" . $new_area->region_id . "' where branch_id = '" . $branch_id . "'";
        $db->createCommand($awp_branch_sustainability_query)->execute();
        $awp_overdue_query = "update awp_overdue set area_id = '" . $new_area->id . "', region_id = '" . $new_area->region_id . "' where branch_id = '" . $branch_id . "'";
        $db->createCommand($awp_overdue_query)->execute();
        $awp_recovery_percentage_query = "update awp_recovery_percentage set area_id = '" . $new_area->id . "', region_id = '" . $new_area->region_id . "' where branch_id = '" . $branch_id . "'";
        $db->createCommand($awp_recovery_percentage_query)->execute();
        $awp_target_vs_achievement_query = "update awp_target_vs_achievement set area_id = '" . $new_area->id . "', region_id = '" . $new_area->region_id . "' where branch_id = '" . $branch_id . "'";
        $db->createCommand($awp_target_vs_achievement_query)->execute();
        $awp_loan_management_cost_query = "update awp_loan_management_cost set area_id = '" . $new_area->id . "', region_id = '" . $new_area->region_id . "' where branch_id = '" . $branch_id . "'";
        $db->createCommand($awp_loan_management_cost_query)->execute();
        $awp_loans_um_query = "update awp_loans_um set area_id = '" . $new_area->id . "', region_id = '" . $new_area->region_id . "' where branch_id = '" . $branch_id . "'";
        $db->createCommand($awp_loans_um_query)->execute();
        $arc_account_report_details = "update arc_account_report_details set area_id = '" . $new_area->id . "', region_id = '" . $new_area->region_id . "' where branch_id = '" . $branch_id . "'";
        $db->createCommand($arc_account_report_details)->execute();

    }

    public static function transferArea($area_id, $old_region, $new_region)
    {
        $db = Yii::$app->db;
        $applications_query = "update applications set region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($applications_query)->execute();
        $disbursements_query = "update disbursements set  region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($disbursements_query)->execute();
        $donations_query = "update donations set  region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($donations_query)->execute($donations_query);
        $groups_query = "update groups set region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($groups_query)->execute($groups_query);
        $fund_requests_query = "update fund_requests set  region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($fund_requests_query)->execute();
        $loans_query = "update loans set  region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($loans_query)->execute();
        $members_query = "update members set region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($members_query)->execute();
        $progress_report_details_query = "update progress_report_details set  region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($progress_report_details_query)->execute();
        $operations_query = "update operations set  region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($operations_query)->execute();
        $recoveries_query = "update recoveries set  region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($recoveries_query)->execute();
        /*$social_appraisals_query = "update appraisals_social set  region_id = '".$new_region->id."' where area_id = '".$area_id."'";
        $db->createCommand($social_appraisals_query)->execute();
        $transactions_query = "update transactions set region_id = '".$new_region->id."' where area_id = '".$area_id."'";
        $db->createCommand($transactions_query)->execute();*/
//        $user_structure_mapping_region_query = "update user_structure_mapping set obj_id = '".$new_region->id."' where obj_id = '".$old_region->id."' and obj_type = 'region'";
//        $db->createCommand($user_structure_mapping_region_query)->execute();
        $branches_query = "update branches set region_id = '" . $new_region->id . "' where area_id = '" . $area_id . "'";
        $db->createCommand($branches_query)->execute();
        $branches_query = "update areas set region_id = '" . $new_region->id . "' where id = '" . $area_id . "'";
        $db->createCommand($branches_query)->execute();
        $awp_query = "update awp set region_id = '" . $new_region->id . "' where id = '" . $area_id . "'";
        $db->createCommand($awp_query)->execute();
        $awp_branch_sustainability_query = "update awp_branch_sustainability set region_id = '" . $new_region->id . "' where id = '" . $area_id . "'";
        $db->createCommand($awp_branch_sustainability_query)->execute();
        $awp_overdue_query = "update awp_overdue set region_id = '" . $new_region->id . "' where id = '" . $area_id . "'";
        $db->createCommand($awp_overdue_query)->execute();
        $awp_recovery_percentage_query = "update awp_recovery_percentage set region_id = '" . $new_region->id . "' where id = '" . $area_id . "'";
        $db->createCommand($awp_recovery_percentage_query)->execute();
        $awp_target_vs_achievement_query = "update awp_target_vs_achievement set region_id = '" . $new_region->id . "' where id = '" . $area_id . "'";
        $db->createCommand($awp_target_vs_achievement_query)->execute();
        $awp_loan_management_cost_query = "update awp_loan_management_cost set region_id = '" . $new_region->id . "' where id = '" . $area_id . "'";
        $db->createCommand($awp_loan_management_cost_query)->execute();
        $awp_loans_um_query = "update awp_loans_um set region_id = '" . $new_region->id . "' where id = '" . $area_id . "'";
        $db->createCommand($awp_loans_um_query)->execute();
    }

    public static function getTotal($provider, $fieldName)
    {
        $total = 0;

        foreach ($provider as $item) {
            $total += $item[$fieldName];
        }

        return $total;
    }

    public static function getMemberaccountstatus($status)
    {
        if ($status == 1) {
            $status = "Verified";
        } elseif ($status == 2) {
            $status = "Rejected";
        } else {
            $status = "Unverified";
        }
        return $status;
    }

    public static function getEmergencyLoanstatus($status)
    {
        if ($status == 1) {
            $status = "Donated";
        } else {
            $status = "Not Donated";
        }
        return $status;
    }

    public static function getCIBType($status)
    {
        if ($status == 1) {
            $status = "Tasdeek";
        } else if ($status == 2) {
            $status = "Data check";
        }
        return $status;
    }

    public static function getFilesaccountsstatus($status)
    {
        if ($status == 1) {
            $status = "Completed";
        } elseif($status == 0) {
            $status = "Pending";
        }elseif ($status == 2){
            $status = "Review";
        }elseif ($status == 3){
            $status = "Approved";
        }
        return $status;
    }
}
