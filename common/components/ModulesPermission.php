<?php

/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 2/15/2018
 * Time: 4:30 PM
 */

namespace common\components;

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
            $roles = $auth->getRolesByUser($user_id);
        } else {
            $roles = $auth->getRolesByUser(Yii::$app->user->getId());
        }
        $i = 0;
        $cont = str_replace('-', '', $controller);
        $array = explode('-', $controller);
        $model_name = '';
        foreach ($array as $a) {
            $model_name .= ucfirst($a);
        }
        $model = self::checkModel($model_name);
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
                    //$arr[$i]['roleParams'] = self::checkModel($model_name);
                    $arr[$i]['roleParams'] = $model;
                    //$arr[$i]['roleParams'] = ['model' => $model];
                    $str[] = $arr[$i];
                }
                $i++;
            }
        }
        return $str;
    }

    public function getActions($role, $controller,$rbac_type)
    {
        $list = AuthItemChild::find()->where(['parent' => $role])->andWhere(['like','child',$rbac_type.'_'.'%', false])->andWhere(['like', 'child', $controller])->asArray()->all();
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
        /*print_r($rule);
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
                if ($auth_rule == 'isArea') {
                    return $dataProvider->query->andFilterWhere(['in', 'user_area.obj_id', $list_ids]);
                } else {
                    return $dataProvider->query->andFilterWhere(['in', 'user_structure_mapping.obj_id', $list_ids]);
                }
            } else if ($controller == 'disbursement_details'){
                return $dataProvider->query->andFilterWhere(['in', "loans." . $auth_rule->getField(), $list_ids]);
            } else {
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

    public static function getSearchFilterQueryAPIView($model)
    {
        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();
        $rule = '';
        $role = UsersHelper::getDesignation(Yii::$app->user->getId());
        if(isset($role)) {
            $auth_item = Yii::$app->authManager->getRole($role);

            if (isset($auth_item)) {
                $rule = $auth_item->ruleName;
            }
        }
        if ($rule) {
            if ($rule == 'isField') {
                foreach ($user_fields as $user_field) {
                    $field_ids[] = $user_field->obj_id;
                }
                if (!$user_fields) {
                    $field_ids = 0;
                }
                return in_array($model->field_id, $field_ids) || ($model->assigned_to ==  Yii::$app->user->getId());
            } else if ($rule == 'isTeam') {
                foreach ($user_teams as $user_team) {
                    $team_ids[] = $user_team->obj_id;
                }
                if (!$user_teams) {
                    $team_ids = 0;
                }
                return in_array($model->team_id, $team_ids) || ($model->assigned_to ==  Yii::$app->user->getId());
            } else if ($rule == 'isBranch') {
                foreach ($user_branches as $user_branch) {
                    $branch_ids[] = $user_branch->obj_id;
                }
                if (!$user_branches) {
                    $branch_ids = 0;
                }
                return in_array($model->branch_id, $branch_ids) || ($model->assigned_to ==  Yii::$app->user->getId());
            } else if ($rule == 'isArea') {
                foreach ($user_areas as $user_area) {
                    $area_ids[] = $user_area->obj_id;
                }
                if (!$user_areas) {
                    $area_ids = 0;
                }
                return in_array($model->area_id, $area_ids) || ($model->assigned_to ==  Yii::$app->user->getId());
            } else if ($rule == 'isRegion') {
                foreach ($user_regions as $user_region) {
                    $region_ids[] = $user_region->obj_id;
                }
                if (!$user_regions) {
                    $region_ids = 0;
                }
                return in_array($model->region_id, $region_ids) || ($model->assigned_to ==  Yii::$app->user->getId());
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                foreach ($user_divisions as $user_division) {
                    $division_ids[] = $user_division->obj_id;
                }
                if(!$user_divisions)
                {
                    $division_ids = 0;
                }
                $user_regions = Regions::find()->where(['in','cr_division_id' , $division_ids])->all();
                foreach ($user_regions as $user_region) {
                    $region_ids[] = $user_region->id;
                }
                if (!$user_regions) {
                    $region_ids = 0;
                }
                return in_array($model->region_id, $region_ids) || ($model->assigned_to ==  Yii::$app->user->getId());
            } else if ($rule == 'isProject') {
                foreach ($user_projects as $user_project) {
                    $project_ids[] = $user_project->project_id;
                }
                if (!$user_projects) {
                    $project_ids = 0;
                }
                return in_array($model->project_id, $project_ids) || ($model->assigned_to ==  Yii::$app->user->getId());
            }
        } else {
            return true;
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
            if ($controller == 'disbursement_details'){
                return $query->andFilterWhere(['in', "loans." . $auth_rule->getField(), $list_ids]);
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

    /*public static function searchDisbursmentSummaryFilters($type)
    {
        $rule = RbacHelper::getRule('loans','disbursement-summary',$type);
        if($rule) {
            $cond = '';
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdListForReports();
            $cond .= "&& loans.".$auth_rule->getField()." in (" . trim($list_ids, ',') . ")";
            return $cond;
        }
    }

    public static function searchRecoverySummaryFilters($type)
    {
        $rule = RbacHelper::getRule('recoveries','recovery-summary',$type);
        if($rule) {
            $cond = '';
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdListForReports();
            $cond .= "&& recoveries.".$auth_rule->getField()." in (" . trim($list_ids, ',') . ")";
            return $cond;
        }
    }

    public static function searchDonationSummaryFilters($type)
    {
        $rule = RbacHelper::getRule('donations','donation-summary',$type);
        if($rule) {
            $cond = '';
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdListForReports();
            $cond .= "&& donations.".$auth_rule->getField()." in (" . trim($list_ids, ',') . ")";
            return $cond;
        }
    }*/

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

    public static function searchFundRequestReportsFilters($controller,$method,$type,$user_id ='',$prefix)
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
            $cond .= "&& ".$prefix.".".$auth_rule->getField()." in (" . trim($list_ids, ',') . ")";
            return $cond;
        }
    }


    public static function getFieldList($controller, $method,$type,$user_id = '')
    {
        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $field_list = [];
        $fieldlist = [];

        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isField') {
                if ($user_fields) {
                    foreach ($user_fields as $user_field) {
                        $fields = Fields::find()->where(['id' => $user_field->obj_id])->all();
                        $fieldlist[] = ArrayHelper::map($fields, 'id', 'name');
                    }
                    foreach ($fieldlist as $field) {
                        foreach ($field as $k => $val) {
                            $field_list[$k] = $val;
                        }
                    }
                }
                return $field_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $fields = Fields::find()->where(['team_id' => $user_team->obj_id])->all();
                        $fieldlist[] = ArrayHelper::map($fields, 'id', 'name');
                    }
                    foreach ($fieldlist as $field) {
                        foreach ($field as $k => $val) {
                            $field_list[$k] = $val;
                        }
                    }
                }
                return $field_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $team_ids[] = Teams::find()->select(['id'])->where(['branch_id' => $user_branch->obj_id])->asArray()->all();
                    }
                    foreach ($team_ids as $team_id) {
                        $fields = Fields::find()->where(['team_id' => $team_id[0]['id']])->all();
                        $fieldlist[] = ArrayHelper::map($fields, 'id', 'name');
                    }
                    foreach ($fieldlist as $field) {
                        foreach ($field as $k => $val) {
                            $field_list[$k] = $val;
                        }
                    }
                }
                return $field_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $branch_ids[] = Branches::find()->select(['id'])->where(['area_id' => $user_area->obj_id])->asArray()->all();
                    }

                    foreach ($branch_ids as $branch_id) {
                        $team_ids[] = Teams::find()->select(['id'])->where(['branch_id' => $branch_id[0]['id']])->asArray()->all();
                    }
                    foreach ($team_ids as $team_id) {
                        $fields = Fields::find()->where(['team_id' => $team_id[0]['id']])->all();
                        $fieldlist[] = ArrayHelper::map($fields, 'id', 'name');
                    }
                    foreach ($fieldlist as $field) {
                        foreach ($field as $k => $val) {
                            $field_list[$k] = $val;
                        }
                    }
                }
                return $field_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $branch_ids[] = Branches::find()->select(['id'])->where(['region_id' => $user_region->id])->asArray()->all();
                        }
                        foreach ($branch_ids as $branch_id) {
                            $team_ids[] = Teams::find()->select(['id'])->where(['branch_id' => $branch_id[0]['id']])->asArray()->all();
                        }
                        foreach ($team_ids as $team_id) {
                            $fields = Fields::find()->where(['team_id' => $team_id[0]['id']])->all();
                            $fieldlist[] = ArrayHelper::map($fields, 'id', 'name');
                        }
                        foreach ($fieldlist as $field) {
                            foreach ($field as $k => $val) {
                                $field_list[$k] = $val;
                            }
                        }
                    }
                }
                return $field_list;
            }  else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $branch_ids[] = Branches::find()->select(['id'])->where(['region_id' => $user_region->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $team_ids[] = Teams::find()->select(['id'])->where(['branch_id' => $branch_id[0]['id']])->asArray()->all();
                    }
                    foreach ($team_ids as $team_id) {
                        $fields = Fields::find()->where(['team_id' => $team_id[0]['id']])->all();
                        $fieldlist[] = ArrayHelper::map($fields, 'id', 'name');
                    }
                    foreach ($fieldlist as $field) {
                        foreach ($field as $k => $val) {
                            $field_list[$k] = $val;
                        }
                    }
                }
                return $field_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $team_ids[] = Teams::find()->where(['branch_id' => $u_branch->branch_id])->all();
                        }
                    }
                    foreach ($team_ids as $team_id) {
                        $fields = Fields::find()->where(['team_id' => $team_id[0]['id']])->all();
                        $fieldlist[] = ArrayHelper::map($fields, 'id', 'name');
                    }
                    foreach ($fieldlist as $field) {
                        foreach ($field as $k => $val) {
                            $field_list[$k] = $val;
                        }
                    }
                }
                return $field_list;
            }
        } else {
            $fieldlist = Fields::find()->all();
            $fieldlist = ArrayHelper::map($fieldlist, 'id', 'name');
            return $fieldlist;
        }
    }

    public static function getTeamList($controller, $method,$type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $team_list = [];
        $teamlist = [];
        if(empty($user_id))
        {
            $user_id = Yii::$app->user->getId();
        }
        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        if ($rule) {
            if ($rule == 'isField') {
                if ($user_fields) {
                    foreach ($user_fields as $user_field) {
                        $field_ids[] = Fields::find()->select(['team_id'])->where(['id' => $user_field->obj_id])->all();
                    }
                    foreach ($field_ids as $field_id) {
                        $teams = Teams::find()->where(['id' => $field_id[0]['team_id']])->all();
                        $teamlist[] = ArrayHelper::map($teams, 'id', 'name');
                    }

                    foreach ($teamlist as $team) {
                        foreach ($team as $k => $val) {
                            $team_list[$k] = $val;
                        }
                    }
                }
                return $team_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $teams = Teams::find()->where(['id' => $user_team->obj_id])->all();
                        $teamlist[] = ArrayHelper::map($teams, 'id', 'name');
                    }
                    foreach ($teamlist as $team) {
                        foreach ($team as $k => $val) {
                            $team_list[$k] = $val;
                        }
                    }
                }
                return $team_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $teams = Teams::find()->where(['branch_id' => $user_branch->obj_id])->all();
                        $teamlist[] = ArrayHelper::map($teams, 'id', 'name');
                    }
                    foreach ($teamlist as $team) {
                        foreach ($team as $k => $val) {
                            $team_list[$k] = $val;
                        }
                    }
                }
                return $team_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $branch_ids[] = Branches::find()->select(['id'])->where(['area_id' => $user_area->obj_id])->asArray()->all();
                    }

                    foreach ($branch_ids as $branch_id) {
                        $teams = Teams::find()->where(['branch_id' => $branch_id[0]['id']])->asArray()->all();
                        $teamlist[] = ArrayHelper::map($teams, 'id', 'name');
                    }

                    foreach ($teamlist as $team) {
                        foreach ($team as $k => $val) {
                            $team_list[$k] = $val;
                        }
                    }
                }
                return $team_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $branch_ids[] = Branches::find()->select(['id'])->where(['region_id' => $user_region->obj_id])->all();
                    }

                    foreach ($branch_ids as $branch_id) {
                        $teams = Teams::find()->where(['branch_id' => $branch_id[0]['id']])->asArray()->all();
                        $teamlist[] = ArrayHelper::map($teams, 'id', 'name');
                    }

                    foreach ($teamlist as $team) {
                        foreach ($team as $k => $val) {
                            $team_list[$k] = $val;
                        }
                    }
                }
                return $team_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $branch_ids[] = Branches::find()->select(['id'])->where(['region_id' => $user_region->obj_id])->all();
                        }

                        foreach ($branch_ids as $branch_id) {
                            $teams = Teams::find()->where(['branch_id' => $branch_id[0]['id']])->asArray()->all();
                            $teamlist[] = ArrayHelper::map($teams, 'id', 'name');
                        }

                        foreach ($teamlist as $team) {
                            foreach ($team as $k => $val) {
                                $team_list[$k] = $val;
                            }
                        }
                    }
                }
                return $team_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $teams = Teams::find()->where(['branch_id' => $u_branch->branch_id])->all();
                            $teamlist[] = ArrayHelper::map($teams, 'id', 'name');
                        }
                    }
                    foreach ($teamlist as $team) {
                        foreach ($team as $k => $val) {
                            $team_list[$k] = $val;
                        }
                    }
                }
                return $team_list;
            }
        } else {
            $teamlist = Teams::find()->all();
            $teamlist = ArrayHelper::map($teamlist, 'id', 'name');
            return $teamlist;
        }
    }

    public static function getBranchListApi($user_id,$controller,$method,$type)
    {
        $user_projects = UserProjectsMapping::find()->where(['user_id' => $user_id])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'field'])->all();

        $branch_list = [];
        $branchlist = [];

        $rule = RbacHelper::getRule($controller, $method, $type,$user_id);

        /*$role = UsersHelper::getDesignation(Yii::$app->user->getId());
        if(isset($role)) {
            $auth_item = Yii::$app->authManager->getRole($role);

            if (isset($auth_item)) {
                $rule = $auth_item->ruleName;
            }
        }*/

        if ($rule) {
            if ($rule == 'isField') {
                if ($user_fields) {
                    foreach ($user_fields as $user_field) {
                        $team_ids[] = Fields::find()->select(['team_id'])->where(['id' => $user_field->obj_id])->asArray()->all();
                    }
                    foreach ($team_ids as $team_id) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $team_id[0]['team_id']])->asArray()->all();
                    }

                    foreach ($branch_ids as $branch_id) {
                        $branches = Branches::find()->where(['id' => $branch_id[0]['branch_id']])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $branches = Branches::find()->where(['id' => $branch_id[0]['branch_id']])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }

                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }

                return $branch_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $branches = Branches::find()->where(['region_id' => $user_region->id])->all();
                            $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                        }

                        foreach ($branchlist as $branch) {
                            foreach ($branch as $k => $val) {
                                $branch_list[$k] = $val;
                            }
                        }
                    }
                }

                return $branch_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branches = Branches::find()->where(['id' => $u_branch->branch_id])->all();
                            $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                        }
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            }
        } else {
            $branchlist = Branches::find()->all();
            $branchlist = ArrayHelper::map($branchlist, 'id', 'name');
            return $branchlist;
        }
    }

    public static function getAreaListApi($user_id,$controller,$method,$type)
    {
        $user_projects = UserProjectsMapping::find()->where(['user_id' => $user_id])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => $user_id, 'obj_type' => 'field'])->all();

        $arealist = [];
        $area_list = [];

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

                    foreach ($branch_ids as $branch_id) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $branch_id[0]['branch_id']])->all();
                    }

                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $branch_id[0]['branch_id']])->all();
                    }
                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->obj_id])->asArray()->all();
                    }
                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $areas = Areas::find()->where(['id' => $user_area->obj_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $areas = Areas::find()->where(['region_id' => $user_region->obj_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $areas = Areas::find()->where(['region_id' => $user_region->id])->all();
                            $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                            //$area_ids[] = Regions::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                        foreach ($arealist as $area) {
                            foreach ($area as $k => $val) {
                                $area_list[$k] = $val;
                            }
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                    }
                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            }
        } else {
            $arealist = Areas::find()->all();
            $arealist = ArrayHelper::map($arealist, 'id', 'name');
            return $arealist;
        }
    }

    public static function getBranchList($controller, $method,$type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $branch_list = [];
        $branchlist = [];

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

                    foreach ($branch_ids as $branch_id) {
                        $branches = Branches::find()->where(['id' => $branch_id[0]['branch_id']])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $branches = Branches::find()->where(['id' => $branch_id[0]['branch_id']])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }

                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }

                return $branch_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $branches = Branches::find()->where(['region_id' => $user_region->id])->all();
                            $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                        }

                        foreach ($branchlist as $branch) {
                            foreach ($branch as $k => $val) {
                                $branch_list[$k] = $val;
                            }
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branches = Branches::find()->where(['id' => $u_branch->branch_id])->all();
                            $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                        }
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            }
        } else {
            $branchlist = Branches::find()->all();
            $branchlist = ArrayHelper::map($branchlist, 'id', 'name');
            return $branchlist;
        }
    }

    public static function getBranchListNameWise($controller, $method,$type,$user_id='')
    {
        /*$controller = str_replace('-', '', strtolower($controller));
        $rule = RbacHelper::getRule($controller,$method,'frontend');
        if($rule) {
            $auth_rule = AuthRule::findOne(['name' => $rule]);
            $list_ids = $auth_rule->getIdList();
            $auth_rule->getBranches();

        }*/

        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $branch_list = [];
        $branchlist = [];
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

                    foreach ($branch_ids as $branch_id) {
                        $branches = Branches::find()->where(['id' => $branch_id[0]['branch_id']])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $branches = Branches::find()->where(['id' => $branch_id[0]['branch_id']])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                    }

                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }

                return $branch_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $branches = Branches::find()->where(['region_id' => $user_region->id])->all();
                            $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                        }

                        foreach ($branchlist as $branch) {
                            foreach ($branch as $k => $val) {
                                $branch_list[$k] = $val;
                            }
                        }
                    }
                }

                return $branch_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branches = Branches::find()->where(['id' => $u_branch->branch_id])->all();
                            $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                        }
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            }
        } else {
            $branchlist = Branches::find()->all();
            $branchlist = ArrayHelper::map($branchlist, 'name', 'name');
            return $branchlist;
        }
    }

    public static function getBranchListCodeWise($controller, $method,$type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $branch_list = [];
        $branchlist = [];
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

                    foreach ($branch_ids as $branch_id) {
                        $branches = Branches::find()->where(['id' => $branch_id[0]['branch_id']])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $branches = Branches::find()->where(['id' => $branch_id[0]['branch_id']])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->obj_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                    }

                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }

                return $branch_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $branches = Branches::find()->where(['region_id' => $user_region->id])->all();
                            $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                        }

                        foreach ($branchlist as $branch) {
                            foreach ($branch as $k => $val) {
                                $branch_list[$k] = $val;
                            }
                        }
                    }
                }
                return $branch_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branches = Branches::find()->where(['id' => $u_branch->branch_id])->all();
                            $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                        }
                    }
                    foreach ($branchlist as $branch) {
                        foreach ($branch as $k => $val) {
                            $branch_list[$k] = $val;
                        }
                    }
                }
                return $branch_list;
            }
        } else {
            $branchlist = Branches::find()->all();
            $branchlist = ArrayHelper::map($branchlist, 'code', 'name');
            return $branchlist;
        }
    }

    public static function getAreaList($controller, $method,$type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $arealist = [];
        $area_list = [];
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

                    foreach ($branch_ids as $branch_id) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $branch_id[0]['branch_id']])->all();
                    }

                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $branch_id[0]['branch_id']])->all();
                    }
                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->obj_id])->asArray()->all();
                    }
                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $areas = Areas::find()->where(['id' => $user_area->obj_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    /* foreach ($area_ids as $area_id)
                     {
                         $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                         $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                     }*/
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $areas = Areas::find()->where(['region_id' => $user_region->obj_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                        //$area_ids[] = Regions::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $areas = Areas::find()->where(['region_id' => $user_region->id])->all();
                            $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                            //$area_ids[] = Regions::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                        foreach ($arealist as $area) {
                            foreach ($area as $k => $val) {
                                $area_list[$k] = $val;
                            }
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                    }
                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            }
        } else {
            $arealist = Areas::find()->all();
            $arealist = ArrayHelper::map($arealist, 'id', 'name');
            return $arealist;
        }
    }

    public static function getAreaListNameWise($controller, $method,$type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $arealist = [];
        $area_list = [];
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

                    foreach ($branch_ids as $branch_id) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $branch_id[0]['branch_id']])->all();
                    }

                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $branch_id[0]['branch_id']])->all();
                    }
                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->obj_id])->asArray()->all();
                    }
                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $areas = Areas::find()->where(['id' => $user_area->obj_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                    }
                    /* foreach ($area_ids as $area_id)
                     {
                         $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                         $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                     }*/
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $areas = Areas::find()->where(['region_id' => $user_region->obj_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                        //$area_ids[] = Regions::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $areas = Areas::find()->where(['region_id' => $user_region->id])->all();
                            $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                            //$area_ids[] = Regions::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                        foreach ($arealist as $area) {
                            foreach ($area as $k => $val) {
                                $area_list[$k] = $val;
                            }
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                    }
                    foreach ($area_ids as $area_id) {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k => $val) {
                            $area_list[$k] = $val;
                        }
                    }
                }
                return $area_list;
            }
        } else {
            $arealist = Areas::find()->all();
            $arealist = ArrayHelper::map($arealist, 'name', 'name');
            return $arealist;
        }
    }

    public static function getRegionList($controller, $method,$type,$user_id='')
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

                    foreach ($branch_ids as $branch_id) {
                        $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $branch_id[0]['branch_id']])->asArray()->all();
                    }

                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $branch_id[0]['branch_id']])->asArray()->all();
                    }

                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $user_branch->obj_id])->asArray()->all();
                    }
                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $regions_ids[] = Areas::find()->select(['region_id'])->where(['id' => $user_area->obj_id])->asArray()->all();
                    }
                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $regions = Regions::find()->where(['id' => $user_region->obj_id])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $regions = Regions::find()->where(['id' => $user_region->id])->all();
                            $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                        }
                        foreach ($regionlist as $region) {
                            foreach ($region as $k => $val) {
                                $region_list[$k] = $val;
                            }
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {

                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                    }
                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            }
        } else {
            $regionlist = Regions::find()->all();
            $regionlist = ArrayHelper::map($regionlist, 'id', 'name');
            return $regionlist;
        }
    }

    public static function getRegionListNameWise($controller, $method,$type,$user_id='')
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

                    foreach ($branch_ids as $branch_id) {
                        $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $branch_id[0]['branch_id']])->asArray()->all();
                    }

                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'name', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $branch_id[0]['branch_id']])->asArray()->all();
                    }

                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'name', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $user_branch->obj_id])->asArray()->all();
                    }
                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'name', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $regions_ids[] = Areas::find()->select(['region_id'])->where(['id' => $user_area->obj_id])->asArray()->all();
                    }
                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'name', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $regions = Regions::find()->where(['id' => $user_region->obj_id])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'name', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $regions = Regions::find()->where(['id' => $user_region->id])->all();
                            $regionlist[] = ArrayHelper::map($regions, 'name', 'name');
                        }
                        foreach ($regionlist as $region) {
                            foreach ($region as $k => $val) {
                                $region_list[$k] = $val;
                            }
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isProject') {
                if ($user_projects) {

                    foreach ($user_projects as $user_project) {
                        $branches[] = BranchProjectsMapping::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                    }
                    foreach ($regions_ids as $region_id) {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'name', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k => $val) {
                            $region_list[$k] = $val;
                        }
                    }
                }
                return $region_list;
            }
        } else {
            $regionlist = Regions::find()->all();
            $regionlist = ArrayHelper::map($regionlist, 'name', 'name');
            return $regionlist;
        }
    }

    public static function getProjectList($controller, $method,$type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $projectlist = [];
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

                    foreach ($branch_ids as $branch_id) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch_id[0]['branch_id']])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch_id[0]['branch_id']])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->obj_id])->all();
                    }

                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->obj_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projects_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($projects_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->obj_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $branches = Branches::find()->where(['region_id' => $user_region->id])->all();
                        }
                        foreach ($branches as $branch) {
                            $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                            foreach ($projects as $project) {
                                $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                                $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                            }
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $projectlist[] = Projects::find()->where(['id' => $user_project->project_id])->one();
                    }

                    $projectlist = ArrayHelper::map($projectlist, 'id', 'name');
                }
                return $projectlist;
            }
        } else {
            $projects = BranchProjectsMapping::find()->all();
            foreach ($projects as $project) {
                $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
            }
            $projectlist = ArrayHelper::map($projectlist, 'id', 'name');
            return $projectlist;
        }
    }

    public static function getProjectListNameWise($controller, $method,$type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();

        $projectlist = [];
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

                    foreach ($branch_ids as $branch_id) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch_id[0]['branch_id']])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch_id[0]['branch_id']])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->obj_id])->all();
                    }

                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->obj_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projects_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($projects_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->obj_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $branches = Branches::find()->where(['region_id' => $user_region->id])->all();
                        }
                        foreach ($branches as $branch) {
                            $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                            foreach ($projects as $project) {
                                $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                                $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                            }
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $projectlist[] = Projects::find()->where(['id' => $user_project->project_id])->one();
                    }

                    $projectlist = ArrayHelper::map($projectlist, 'id', 'name');
                }
                return $projectlist;
            }
        } else {
            $projects = BranchProjectsMapping::find()->all();
            foreach ($projects as $project) {
                $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
            }
            $projectlist = ArrayHelper::map($projectlist, 'id', 'name');
            return $projectlist;
        }
    }

    public static function getProjectListFundingLineWise($controller, $method, $type,$user_id='')
    {
        $controller = str_replace('-', '', strtolower($controller));

        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();
        $myprojects = array();
        $projectlist = [];
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

                    foreach ($branch_ids as $branch_id) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch_id[0]['branch_id']])->all();
                        foreach ($projects as $project) {
                            $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isTeam') {
                if ($user_teams) {
                    foreach ($user_teams as $user_team) {
                        $branch_ids[] = Teams::find()->select(['branch_id'])->where(['id' => $user_team->obj_id])->asArray()->all();
                    }
                    foreach ($branch_ids as $branch_id) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch_id[0]['branch_id']])->all();
                        foreach ($projects as $project) {
                            $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                        }
                        foreach ($projectlist as $p) {
                            if ($p) {
                                $myprojects[$p->funding_line . '_' . $p->name] = $p->name;
                            }
                        }
                    }
                }
                return $myprojects;
            } else if ($rule == 'isBranch') {
                if ($user_branches) {
                    foreach ($user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->obj_id])->all();
                    }

                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                        }
                        foreach ($projectlist as $p) {
                            if ($p) {
                                $myprojects[$p->funding_line . '_' . $p->name] = $p->name;
                            }
                        }
                    }
                }
                return $myprojects;
            } else if ($rule == 'isArea') {
                if ($user_areas) {
                    foreach ($user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->obj_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                        }
                        foreach ($projectlist as $p) {
                            if ($p) {
                                $myprojects[$p->funding_line . '_' . $p->name] = $p->name;
                            }
                        }
                    }
                }
                return $myprojects;
            } else if ($rule == 'isRegion') {
                if ($user_regions) {
                    foreach ($user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->obj_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                        }
                        foreach ($projectlist as $p) {
                            if ($p) {
                                $myprojects[$p->funding_line . '_' . $p->name] = $p->name;
                            }
                        }
                    }
                }
                return $myprojects;
            } else if ($rule == 'isDivision') {
                $user_divisions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'division'])->all();
                if($user_divisions) {
                    foreach ($user_divisions as $user_division) {
                        $division_ids[] = $user_division->obj_id;
                    }
                    $user_regions = Regions::find()->where(['in', 'cr_division_id', $division_ids])->all();
                    if ($user_regions) {
                        foreach ($user_regions as $user_region) {
                            $branches = Branches::find()->where(['region_id' => $user_region->id])->all();
                        }
                        foreach ($branches as $branch) {
                            $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                            foreach ($projects as $project) {
                                $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                            }
                            foreach ($projectlist as $p) {
                                if ($p) {
                                    $myprojects[$p->funding_line . '_' . $p->name] = $p->name;
                                }
                            }
                        }
                    }
                }
                return $myprojects;
            } else if ($rule == 'isProject') {
                if ($user_projects) {
                    foreach ($user_projects as $user_project) {
                        $projectlist[] = Projects::find()->where(['id' => $user_project->project_id])->one();
                    }
                    foreach ($projectlist as $p) {
                        if ($p) {
                            $myprojects[$p->funding_line . '_' . $p->name] = $p->name;
                        }
                    }
                }
                return $myprojects;
            }
        } else {
            $projects = BranchProjectsMapping::find()->all();
            foreach ($projects as $project) {
                $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
            }
            foreach ($projectlist as $p) {
                if ($p) {
                    $myprojects[$p->funding_line . '_' . $p->name] = $p->name;
                }
            }
            return $myprojects;
        }
    }

    public static function getAreasByRegion($controller, $method, $rgId,$type,$user_id='')
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
    }

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