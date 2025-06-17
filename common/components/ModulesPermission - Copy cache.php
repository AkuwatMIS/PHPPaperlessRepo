<?php

/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 2/15/2018
 * Time: 4:30 PM
 */

namespace common\components;

use common\components\Helpers\CacheHelper;
use common\components\Helpers\ReportsHelper\UserHelper;
use common\components\Helpers\StructureHelper;
use common\components\Helpers\UsersHelper;
use common\models\AuthItemChildApi;
use common\models\AuthRule;
use common\models\BranchProjectsMapping;
use common\models\Fields;
use common\models\Teams;
use common\models\UserProjectsMapping;
use common\models\UserStructureMapping;
use common\components\Helpers\RbacHelper;
use Yii;
use yii\base\Component;
use common\models\Areas;
use common\models\AuthItem;
use common\models\Loans;
use common\models\Projects;
use common\models\Branches;
use common\models\Regions;
use yii\helpers\ArrayHelper;
use common\models\AuthItemChild;

class ModulesPermission extends Component
{
    public function checkModel($m)
    {
        $model_name = $m;
        $r = array();
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $model = 'common\models\\' . ucfirst($model_name);
        if (file_exists(Yii::getAlias('@anyname') . '/common/models/' . ucfirst($model_name) . '.php')) {
            $r = ['model' => $model::findOne(Yii::$app->request->get('id'))];
        } else {
            $r = ['model' => ''];
        }
        return $r;
    }

    public function getRules($controller,$rbac_type,$user_id = '')
    {
        $arr = array();
        $str = array();
        $auth = Yii::$app->authManager;
        if(!empty($user_id))
        {
            $roles = UsersHelper::getRoles($user_id);
        } else {
            $roles = UsersHelper::getRoles(Yii::$app->user->getId());
        }

        /*print_r($roles);
        die();*/
        $i = 0;
        $cont = str_replace('-', '', $controller);
        $array = explode('-', $controller);
        $model_name = '';
        foreach ($array as $a) {
            $model_name .= ucfirst($a);
        }

        foreach ($roles as $r) {
            $actions = self::getActions($r->name, $cont,$rbac_type);
            if (!empty($actions)) {
                foreach ($actions as $action) {
                    if($rbac_type == 'frontend') {
                        $arr[$i]['controllers'] = [$controller];
                    }
                    $arr[$i]['actions'] = [$action];
                    $arr[$i]['allow'] = true;
                    if ($action == 'view' || $action == 'delete' || $action == 'ledger' || $action == 'logs' || $action == 'show' ) {
                        $arr[$i]['roles'] = [$rbac_type.'_'.$action . $cont];
                    } else if(($action == 'processed' || $action == 'update') && $rbac_type == 'frontend') {
                        $arr[$i]['roles'] = [$rbac_type.'_'.$action . $cont];
                    } else {
                        $arr[$i]['roles'] = [$r->name];
                    }
                    if ($action == 'processed' || $action == 'update' || $action == 'view' || $action == 'delete' || $action == 'ledger' || $action == 'logs' || $action == 'show' ) {
                        $arr[$i]['roleParams'] = self::checkModel($model_name);
                    }
                    //$arr[$i]['roleParams'] = ['model' => $model];
                    $str[] = $arr[$i];
                }
                $i++;
            }
        }
       /*print_r($str);
        die();*/
        return $str;
    }

    public function getActions($role, $controller,$rbac_type)
    {
        $key = $role.'_'.$rbac_type.'_child_items';
        $auth_child_items = CacheHelper::getStructure($key);

        if(empty($auth_child_items)) {
            $auth_child_items = AuthItemChild::find()->where(['parent' => $role])->andWhere(['like','child',$rbac_type.'_'.'%', false])->asArray()->all();
            CacheHelper::setStructure($key, $auth_child_items);
        }

        foreach ($auth_child_items as $item)
        {
            if(stripos( strtolower($item['child']) , strtolower($controller) ) !== false )
            {
               $list[]= $item;
            }
        }

       // $list = AuthItemChild::find()->where(['parent' => $role])->andWhere(['like','child',$rbac_type.'_'.'%', false])->andWhere(['like', 'child', $controller])->asArray()->all();
        $action = array();
        $arr = array();
        $i = 0;
        foreach ($list as $l) {
            $str = self::lreplace($controller, $l['child']);
            $arr[] = preg_split('/(?=[A-Z])/', $str);
            $api_action = explode($rbac_type.'_',$arr[$i][0]);
            $action[] = $api_action[1];
            $i++;
        }
        return $action;
    }

    private function lreplace($search, $subject)
    {
        $pos = strrpos($subject, $search);
        if ($pos !== false) {
            $subject = substr_replace($subject, '', $pos, strlen($search));
        }
        return $subject;
    }

    public static function getSearchFilter($dataProvider, $controller, $method,$type,$user_id = '')
    {
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
       /* print_r($rule);
        die();*/
        $controller = str_replace('-','_',$controller);
        if ($rule) {
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdList();
            if(empty($list_ids))
            {
                $list_ids = 0;

            }
            if ($auth_rule == 'isProject') {
                if ($controller == 'branches') {
                    $dataProvider->query->joinWith('branchProjects');
                    return $dataProvider->query->andFilterWhere(['in', "branch_projects." . $auth_rule->getField(), $list_ids]);
                } else if ($controller == 'groups' || $controller == 'borrowers') {
                    $dataProvider->query->joinWith('branch.branchProjects');
                    return $dataProvider->query->andFilterWhere(['in', "branch_projects." . $auth_rule->getField(),$list_ids]);
                } else {
                    return $dataProvider->query->andFilterWhere(['in', $controller . "." . $auth_rule->getField(), $list_ids]);
                }
            } else if ($controller == 'users'){
                $dataProvider->query->joinWith(str_replace('_id','',$auth_rule->getField()));
                return $dataProvider->query->andFilterWhere(['in','obj_id', $list_ids]);
            }
            else {
                return $dataProvider->query->andFilterWhere(['in', $controller . "." . $auth_rule->getField(), $list_ids]);
            }
        } else {
            return $dataProvider;
        }
    }

    public static function getSearchFilterQueryAPI($query,$controller,$method,$type)
    {
        $rule = RbacHelper::getRule($controller,$method,$type);
        if($rule) {
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdList();
            if(empty($list_ids))
            {
                $list_ids = 0;

            }
            return $query->andFilterWhere(['in', $controller . "." . $auth_rule->getField(), $list_ids]);
        } else {
            return $query;
        }
    }

    public static function getSearchFilterQuery($query, $controller, $method,$type,$user_id = '')
    {
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        $controller = str_replace('-','_',$controller);
        if ($rule) {
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdList();
            if(empty($list_ids))
            {
                $list_ids = 0;
            }
            if($controller == 'branches' && $auth_rule == 'isBranch')
            {
                return $query->andFilterWhere(['in', $controller . ".id", $list_ids]);
            }
            if ($auth_rule == 'isProject') {
                if ($controller == 'branches') {
                    $query->joinWith('branchProjects');
                    return $query->andFilterWhere(['in', "branch_projects." . $auth_rule->getField(), $list_ids]);
                } else if ($controller == 'groups' || $controller == 'borrowers') {
                    $query->joinWith('branch.branchProjects');
                    return $query->andFilterWhere(['in', "branch_projects." . $auth_rule->getField(), $list_ids]);
                } else {
                    return $query->andFilterWhere(['in', $controller . "." . $auth_rule->getField(), $list_ids]);
                }
            } else {
                return $query->andFilterWhere(['in', $controller . "." . $auth_rule->getField(), $list_ids]);
            }
        } else {
            return $query;
        }
    }

    public static function searchReportsFilters($controller,$method,$type,$user_id ='')
    {
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        if($rule) {
            $cond = '';
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdListForReports();
            $cond .= "&& ".$controller.".".$auth_rule->getField()." in (" . trim($list_ids, ',') . ")";
            return $cond;
        }
    }

    public static function searchProgressReportsFilters($query,$type,$user_id ='')
    {
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule('progressreports','index',$type,$user_id);
        if($rule) {
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdList();
            if(empty($list_ids))
            {
                $list_ids = 0;
            }

            return $query->andFilterWhere(['in', $auth_rule->getField(), $list_ids]);
        }
    }

    public static function searchArcAccountReportsFilters($query,$controller,$method,$type,$user_id ='')
    {
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        if($rule) {
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdList();
            if(empty($list_ids))
            {
                $list_ids = 0;
            }

            return $query->andFilterWhere(['in', $auth_rule->getField(), $list_ids]);
        }
    }

    public static function searchAccountReportsFilters($controller,$method,$type,$user_id ='')
    {
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        if($rule) {
            $cond = '';
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdListForReports();
            $cond .= "&& d.".$auth_rule->getField()." in (" . trim($list_ids, ',') . ")";
            return $cond;
        }
    }

    static protected function getList($list,$column,$structure_ids,$map = 'id',$value='name')
    {
        $array = [];
        $structure = StructureHelper::getStructure($list);

        $structure_list = [];
        foreach ($structure as $listing)
        {
            $structure_list[] = (array) $listing;
        }

        foreach ($structure_ids as $structure_id) {
            $keys= array_keys(array_column($structure_list, $column), $structure_id);

            foreach ($keys as $k) {
                if ($map == 'funding_line') {
                    $array[$structure_list[$k][$map] . '_' . $structure_list[$k]['name']] = $structure_list[$k][$value];
                } else {
                    $array[$structure_list[$k][$map]] = $structure_list[$k][$value];
                }
            }

        }

        return $array;
    }

    public static function getFieldList($controller, $method,$type,$user_id = '')
    {
        $field_list = [];

        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $field_list = self::getList('fields', 'cr_division_id', $user_divisions);
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $field_list = self::getList('fields', 'region_id', $user_regions);
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $field_list = self::getList('fields','area_id',$user_areas);
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $field_list = self::getList('fields', 'branch_id', $user_branches);
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $field_list = self::getList('fields', 'team_id', $user_teams);
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $field_list = self::getList('fields', 'id', $user_fields);
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $user_branches = self::getList('branch_projects','project_id',$user_projects,'branch_id','branch_id');
                $field_list = self::getList('fields', 'branch_id', $user_branches);
            }
        }
        else{
            $field_list = ArrayHelper::map(StructureHelper::getStructure('fields'), 'id', 'name');
        }

        return $field_list;
    }

    public static function getTeamList($controller, $method,$type,$user_id='')
    {
        $team_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $team_list = self::getList('teams', 'cr_division_id', $user_divisions);
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $team_list = self::getList('teams', 'region_id', $user_regions);
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $team_list = self::getList('teams','area_id',$user_areas);
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $team_list = self::getList('teams', 'branch_id', $user_branches);
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $team_list = self::getList('teams', 'id', $user_teams);
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $team_list = self::getList('fields', 'id', $user_fields,'team_id','team_name');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $user_branches = self::getList('branch_projects','project_id',$user_projects,'branch_id','branch_id');
                $team_list = self::getList('teams', 'branch_id', $user_branches);
            }
        } else {
            $team_list = ArrayHelper::map(StructureHelper::getStructure('teams'), 'id', 'name');
        }
        return $team_list;
    }

    public static function getBranchList($controller, $method,$type,$user_id='')
    {
        $branch_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $branch_list = self::getList('branches', 'cr_division_id', $user_divisions);
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $branch_list = self::getList('branches', 'region_id', $user_regions);
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $branch_list = self::getList('branches','area_id',$user_areas);
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $branch_list = self::getList('branches', 'id', $user_branches);
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $branch_list = self::getList('teams', 'id', $user_teams,'branch_id','branch_name');
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $branch_list = self::getList('fields', 'id', $user_fields,'branch_id','branch_name');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $user_branches = self::getList('branch_projects','project_id',$user_projects,'branch_id','branch_id');
                $branch_list = self::getList('branches', 'id', $user_branches);
            }
        } else {
            $branch_list = ArrayHelper::map(StructureHelper::getStructure('branches'), 'id', 'name');
        }

        return $branch_list;
    }

    public static function getBranchListNameWise($controller, $method,$type,$user_id='')
    {
        $branch_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $branch_list = self::getList('branches', 'cr_division_id', $user_divisions,'name');
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $branch_list = self::getList('branches', 'region_id', $user_regions,'name');
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $branch_list = self::getList('branches','area_id',$user_areas,'name');
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $branch_list = self::getList('branches', 'id', $user_branches,'name');
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $branch_list = self::getList('teams', 'id', $user_teams,'branch_name','branch_name');
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $branch_list = self::getList('fields', 'id', $user_fields,'branch_name','branch_name');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $user_branches = self::getList('branch_projects','project_id',$user_projects,'branch_id','branch_id');
                $branch_list = self::getList('branches', 'id', $user_branches,'name');
            }
        } else {
            $branch_list = ArrayHelper::map(StructureHelper::getStructure('branches'), 'name', 'name');
        }

        return $branch_list;
    }

    public static function getBranchListCodeWise($controller, $method,$type,$user_id='')
    {
        $branch_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $branch_list = self::getList('branches', 'cr_division_id', $user_divisions,'code');
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $branch_list = self::getList('branches', 'region_id', $user_regions,'code');
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $branch_list = self::getList('branches','area_id',$user_areas,'code');
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $branch_list = self::getList('branches', 'id', $user_branches,'code');
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $branch_list = self::getList('teams', 'id', $user_teams,'branch_code','branch_name');
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $branch_list = self::getList('fields', 'id', $user_fields,'branch_code','branch_name');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $user_branches = self::getList('branch_projects','project_id',$user_projects,'branch_id','branch_id');
                $branch_list = self::getList('branches', 'id', $user_branches,'code');
            }
        } else {
            $branch_list = ArrayHelper::map(StructureHelper::getStructure('branches'), 'code', 'name');
        }

        return $branch_list;
    }

    public static function getAreaList($controller, $method,$type,$user_id='')
    {
        $area_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $area_list = self::getList('areas', 'cr_division_id', $user_divisions);
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $area_list = self::getList('areas', 'region_id', $user_regions);
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $area_list = self::getList('areas','id',$user_areas);
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $area_list = self::getList('branches', 'id', $user_branches,'area_id','area_name');
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $area_list = self::getList('teams', 'id', $user_teams,'area_id','area_name');
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $area_list = self::getList('fields', 'id', $user_fields,'area_id','area_name');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $user_branches = self::getList('branch_projects','project_id',$user_projects,'branch_id','branch_id');
                $area_list = self::getList('branches', 'id', $user_branches,'area_id','area_name');
            }
        } else {
            $area_list =ArrayHelper::map(StructureHelper::getStructure('areas'), 'id', 'name');
        }
        return $area_list;
    }

    public static function getAreaListNameWise($controller, $method,$type,$user_id='')
    {
        $area_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $area_list = self::getList('areas', 'cr_division_id', $user_divisions,'name');
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $area_list = self::getList('areas', 'region_id', $user_regions,'name');
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $area_list = self::getList('areas','id',$user_areas,'name');
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $area_list = self::getList('branches', 'id', $user_branches,'area_name','area_name');
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $area_list = self::getList('teams', 'id', $user_teams,'area_name','area_name');
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $area_list = self::getList('fields', 'id', $user_fields,'area_name','area_name');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $user_branches = self::getList('branch_projects','project_id',$user_projects,'branch_id','branch_id');
                $area_list = self::getList('branches', 'id', $user_branches,'area_name','area_name');
            }
        } else {
            $area_list =ArrayHelper::map(StructureHelper::getStructure('areas'), 'name', 'name');
        }
        return $area_list;
    }

    public static function getRegionList($controller, $method,$type,$user_id='')
    {
        $region_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $region_list = self::getList('regions', 'cr_division_id', $user_divisions);
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');

                $region_list = self::getList('regions', 'id', $user_regions);
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $region_list = self::getList('areas','id',$user_areas,'region_id','region_name');
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $region_list = self::getList('branches', 'id', $user_branches,'region_id','region_name');
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $region_list = self::getList('teams', 'id', $user_teams,'region_id','region_name');
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $region_list = self::getList('fields', 'id', $user_fields,'region_id','region_name');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $user_branches = self::getList('branch_projects','project_id',$user_projects,'branch_id','branch_id');
                $region_list = self::getList('branches', 'id', $user_branches,'region_id','region_name');
            }
        } else {
            $region_list = ArrayHelper::map(StructureHelper::getStructure('regions'), 'id', 'name');
        }
        return $region_list;
    }

    public static function getRegionListNameWise($controller, $method,$type,$user_id='')
    {
        $region_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $region_list = self::getList('regions', 'cr_division_id', $user_divisions,'name');
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $region_list = self::getList('regions', 'id', $user_regions,'name');
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $region_list = self::getList('areas','id',$user_areas,'region_name','region_name');
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $region_list = self::getList('branches', 'id', $user_branches,'region_name','region_name');
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $region_list = self::getList('teams', 'id', $user_teams,'region_name','region_name');
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $region_list = self::getList('fields', 'id', $user_fields,'region_name','region_name');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $user_branches = self::getList('branch_projects','project_id',$user_projects,'branch_id','branch_id');
                $region_list = self::getList('branches', 'id', $user_branches,'region_name','region_name');
            }
        } else {
            $region_list = ArrayHelper::map(StructureHelper::getStructure('areas'), 'name', 'name');
        }
        return $region_list;
    }

    public static function getProjectList($controller, $method,$type,$user_id='')
    {
        $project_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $branch_list = self::getList('branches', 'cr_division_id', $user_divisions,'id','id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects);
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $branch_list = self::getList('branches', 'region_id', $user_regions,'id','id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects);
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $branch_list = self::getList('branches','area_id',$user_areas,'id','id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects);
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $user_projects = self::getList('branch_projects','branch_id',$user_branches,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects);
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $branch_list = self::getList('teams', 'id', $user_teams,'branch_id','branch_id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects);
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $branch_list = self::getList('fields', 'id', $user_fields,'branch_id','branch_id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects);
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $project_list = self::getList('projects','id',$user_projects);
            }
        } else {
            $project_list = ArrayHelper::map(StructureHelper::getStructure('projects'), 'id', 'name');
        }
        return $project_list;
    }

    public static function getProjectListNameWise($controller, $method,$type,$user_id='')
    {
        $project_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $branch_list = self::getList('branches', 'cr_division_id', $user_divisions,'id','id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'name');
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $branch_list = self::getList('branches', 'region_id', $user_regions,'id','id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'name');
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $branch_list = self::getList('branches','area_id',$user_areas,'id','id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'name');
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $user_projects = self::getList('branch_projects','branch_id',$user_branches,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'name');
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $branch_list = self::getList('teams', 'id', $user_teams,'branch_id','branch_id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'name');
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $branch_list = self::getList('fields', 'id', $user_fields,'branch_id','branch_id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'name');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $project_list = self::getList('projects','id',$user_projects,'name');
            }
        } else {
            $project_list = ArrayHelper::map(StructureHelper::getStructure('projects'), 'name', 'name');
        }

        return $project_list;
    }

    public static function getProjectListFundingLineWise($controller, $method, $type,$user_id='')
    {
        $project_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isDivision') {
                $user_divisions = StructureHelper::getUserStructure($user_id, 'divisions');
                $branch_list = self::getList('branches', 'cr_division_id', $user_divisions,'id','id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'funding_line');
            }
            else if ($rule == 'isRegion') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'regions');
                $branch_list = self::getList('branches', 'region_id', $user_regions,'id','id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'funding_line');
            }
            else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id,'areas');
                $branch_list = self::getList('branches','area_id',$user_areas,'id','id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'funding_line');
            }
            else if($rule == 'isBranch'){
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $user_projects = self::getList('branch_projects','branch_id',$user_branches,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'funding_line');
            }
            else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $branch_list = self::getList('teams', 'id', $user_teams,'branch_id','branch_id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'funding_line');
            }
            else if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $branch_list = self::getList('fields', 'id', $user_fields,'branch_id','branch_id');
                $user_projects = self::getList('branch_projects','branch_id',$branch_list,'project_id','project_id');
                $project_list = self::getList('projects','id',$user_projects,'funding_line');
            }
            else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                $project_list = self::getList('projects','id',$user_projects,'funding_line');
            }
        } else {
            $project_list = ArrayHelper::map(StructureHelper::getStructure('projects'), 'funding_line', 'name');
        }

        return $project_list;
    }

    protected static function getDependentList($list,$rgId,$rg_column,$structure_ids,$structure_column,$map = 'id',$value='name')
    {
        $array = [];
        $structure_list = StructureHelper::getStructure($list);
        foreach ($structure_list as $list)
        {
            if($list[$rg_column] == $rgId)
            {
                if(in_array($list[$structure_column],$structure_ids)) {
                    $data['id'] = $list[$map];
                    $data['name'] = $list[$value];
                    $array[] = $data;
                }
            }
        }
        return $array;
    }

    public static function getAreasByRegion($controller, $method, $rgId,$type,$user_id='')
    {
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        if ($rule) {
            if ($rule == 'isField') {
                $user_fields = StructureHelper::getUserStructure($user_id, 'fields');
                $areas_list = self::getDependentList('fields',$rgId, 'region_id',$user_fields,'id','area_id','area_name');

            } else if ($rule == 'isTeam') {
                $user_teams = StructureHelper::getUserStructure($user_id, 'teams');
                $areas_list = self::getDependentList('teams',$rgId, 'region_id',$user_teams,'id','area_id','area_name');
            } else if ($rule == 'isBranch') {
                $user_branches = StructureHelper::getUserStructure($user_id, 'branches');
                $areas_list = self::getDependentList('branches',$rgId, 'region_id',$user_branches,'id','area_id','area_name');
            } else if ($rule == 'isArea') {
                $user_areas = StructureHelper::getUserStructure($user_id, 'areas');
                $areas_list = self::getDependentList('areas',$rgId, 'region_id',$user_areas,'id','id','name');
            } else if ($rule == 'isRegion' || $rule == 'isDivision') {
                $user_regions = StructureHelper::getUserStructure($user_id, 'areas');
                $areas_list = self::getDependentList('areas',$rgId, 'region_id',$user_regions,'id','id','name');
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->asArray()->all();
            } else if ($rule == 'isProject') {
                $user_projects = StructureHelper::getUserStructure($user_id, 'projects');
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->andWhere(['status' => '1'])->asArray()->all();
                        }
                    }
                    foreach ($area_ids as $area_id) {
                        $areas[] = $area_id[0]['area_id'];
                    }
                }
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', "id", $areas])->asArray()->all();
            }
        } else {
            return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->asArray()->all();
        }
        return $areas_list;
    }

    /*public static function getAreasByRegion($controller, $method, $rgId,$type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));
        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();
        $regionlist = [];
        $region_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isField') {
                if ($user_fields) {
                    foreach ($user_fields as $user_field) {
                        $team_ids[] = Fields::find()->select(['team_id'])->where(['id' => $user_field->obj_id])->asArray()->all();
                    }
                    foreach ($team_ids as $team_id) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $team_id[0]['team_id']])->asArray()->all();
                    }
                    $area_ids = Branches::find()->select(['area_id'])->where(['status' => '1'])->andFilterWhere(['in', 'id', $branch_ids])->all();
                    foreach ($area_ids as $area_id) {
                        $area_id_list[] = $area_id->area_id;
                    }
                    return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $area_id_list])->asArray()->all();
                }
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    $area_ids = Branches::find()->select(['area_id'])->where(['status' => '1'])->andFilterWhere(['in', 'id', $branch_ids])->all();
                    foreach ($area_ids as $area_id) {
                        $area_id_list[] = $area_id->area_id;
                    }
                    return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $area_id_list])->asArray()->all();
                }
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branch_ids[] = $user_branch->obj_id;
                    }
                }
                $area_ids = Branches::find()->select(['area_id'])->where(['status' => '1'])->andFilterWhere(['in', 'id', $branch_ids])->all();
                foreach ($area_ids as $area_id) {
                    $area_id_list[] = $area_id->area_id;
                }
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $area_id_list])->asArray()->all();

            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $area_ids[] = $user_area->obj_id;
                    }
                }
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', "id", $area_ids])->asArray()->all();
            } else if ($rule == 'isRegion' || $rule == 'isDivision') {
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->asArray()->all();
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->andWhere(['status' => '1'])->asArray()->all();
                        }
                    }
                    foreach ($area_ids as $area_id) {
                        $areas[] = $area_id[0]['area_id'];
                    }
                }
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', "id", $areas])->asArray()->all();
            }
        } else {
            return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andWhere(['status' => '1'])->asArray()->all();
        }
    }*/


    public static function getBranchesByArea($controller, $method, $rgId, $type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $regionlist = [];
        $region_list = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        if ($rule) {
            if ($rule == 'isField') {
                if ($user_fields) {
                    foreach ($user_fields as $user_field) {
                        $team_ids[] = Fields::find()->select(['team_id'])->where(['id' => $user_field->obj_id])->asArray()->all();
                    }
                    foreach ($team_ids as $team_id) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $team_id[0]['team_id']])->asArray()->all();
                    }
                    return Branches::find()->select(['id', 'name'])->where(['area_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $branch_ids])->all();
                }
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    return Branches::find()->select(['id', 'name'])->where(['area_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $branch_ids])->all();
                }
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branch_ids[] = $user_branch->obj_id;
                    }
                }
                return Branches::find()->select(['id', 'name'])->where(['area_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $branch_ids])->all();
            } else if ($rule == 'isArea' || $rule == 'isRegion' || $rule == 'isDivision') {
                return Branches::find()->select(['id', 'name'])->where(['area_id' => $rgId])->andWhere(['status' => '1'])->asArray()->all();
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branchlist[] = Branches::find()->where(['id' => $u_branch->branch_id])->andWhere(['status' => '1'])->one();
                        }
                    }
                    foreach ($branchlist as $branch_id) {
                        $branch_ids[] = $branch_id->id;
                    }
                }
                return Branches::find()->select(['id', 'name'])->where(['area_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $branch_ids])->all();
            }
        } else {
            return Branches::find()->select(['id', 'name'])->where(['area_id' => $rgId])->andWhere(['status' => '1'])->asArray()->all();
        }
    }

    public static function getTeamsByBranch($controller, $method, $rgId,$type,$user_id ='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        /*$user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
         $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId() , 'obj_type' => 'region'])->all();
         $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
         $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();*/
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        if ($rule) {
            if ($rule == 'isField') {
                if ($user_fields) {
                    foreach ($user_fields as $user_field) {
                        $team_ids[] = Fields::find()->select(['team_id'])->where(['id' => $user_field->obj_id])->asArray()->all();
                    }
                    return Teams::find()->select(['id', 'name'])->where(['branch_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $team_ids])->all();
                }
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $team_ids[] = $user_team->obj_id;
                    }
                    return Teams::find()->select(['id', 'name'])->where(['branch_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $team_ids])->all();
                }
            } else if ($rule == 'isBranch' || $rule == 'isArea' || $rule == 'isRegion' || $rule == 'isDivision') {
                return Teams::find()->select(['id', 'name'])->where(['branch_id' => $rgId])->andWhere(['status' => '1'])->asArray()->all();
            } /*else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branchlist[] = Branches::find()->where(['id' => $u_branch->branch_id])->andWhere(['status'=>'1'])->one();
                        }
                    }
                    foreach ($branchlist as $branch_id)
                    {
                        $branch_ids[] = $branch_id->id;
                    }
                }
                return Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId])->andWhere(['status'=>'1'])->andFilterWhere(['in','id', $branch_ids])->all();
            }*/
        } else {
            return Teams::find()->select(['id', 'name'])->where(['branch_id' => $rgId])->andWhere(['status' => '1'])->asArray()->all();
        }
    }

    public static function getFieldsByTeam($controller, $method, $rgId, $type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        /*$user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId() , 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();*/
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);
        if ($rule) {
            if ($rule == 'isField') {
                if ($user_fields) {
                    foreach ($user_fields as $user_field) {
                        $field_ids[] = Fields::find()->select(['id'])->where(['id' => $user_field->obj_id])->asArray()->all();
                    }
                    return Fields::find()->select(['id', 'name'])->where(['team_id' => $rgId])->andWhere(['status' => '1'])->andFilterWhere(['in', 'id', $field_ids])->all();
                }
            } else if ($rule == 'isTeam' || $rule == 'isBranch' || $rule == 'isArea' || $rule == 'isRegion' || $rule == 'isDivision') {
                return Fields::find()->select(['id', 'name'])->where(['team_id' => $rgId])->andWhere(['status' => '1'])->all();
            }/* else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branchlist[] = Branches::find()->where(['id' => $u_branch->branch_id])->andWhere(['status'=>'1'])->one();
                        }
                    }
                    foreach ($branchlist as $branch_id)
                    {
                        $branch_ids[] = $branch_id->id;
                    }
                }
                return Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId])->andWhere(['status'=>'1'])->andFilterWhere(['in','id', $branch_ids])->all();
            }*/
        } else {
            return Fields::find()->select(['id', 'name'])->where(['team_id' => $rgId])->andWhere(['status' => '1'])->all();
        }
    }
}