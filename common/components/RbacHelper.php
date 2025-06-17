<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components;

use backend\models\UserProjects;
use common\components\ApiParser;
use common\models\Actions;
use common\models\Areas;
use common\models\AuthItem;
use common\models\Loans;
use common\models\ProgressReportDetails;
use common\models\Projects;
use common\models\UserProjectsMapping;
use Yii;
use common\models\BranchProjectsMapping;
use common\models\Branches;
use common\models\Regions;
use yii\helpers\ArrayHelper;
use common\models\AuthItemChild;
use common\models\UserStructureMapping;

class RbacHelper{
    private static $user_projects;
    private static $user_branches;
    private static $user_areas;
    private static $user_regions;

    function __construct() {
       $this->user_projects = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' == 'projects'])->all();
       $this->user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' == 'branches'])->all();
       $this->user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' == 'areas'])->all();
       $this->user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId() , 'obj_type' == 'regions'])->all();
    }

    public static function checkModel($m)
    {
        $model_name = $m;
        $r = array();
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        $model = 'common\models\\'.ucfirst ($model_name);
        if(file_exists(Yii::getAlias('@anyname').'/common/models/'.ucfirst($model_name). '.php')) {
            $r =  ['model' => $model::findOne(Yii::$app->request->get('id'))];
        } else {
            $r =  ['model' => ''];
        }
        /*print_r($model_name);
        print_r($model);
        print_r(Yii::getAlias('@anyname').'\common\models\\'.ucfirst($model_name). '.php');
        print_r($r);
        die();*/
        return $r;
    }

    public static function getRules($controller)
    {
        $arr = array();
        $str = array();
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser(Yii::$app->user->getId());
        $i = 0;
        $cont = str_replace('-','', $controller);
        $array = explode('-',$controller);
        $model_name = '';
        foreach ($array as $a){
            $model_name .= ucfirst($a);
        }

       /* die($model_name);*/
        $model = 'common\models\\'.ucfirst ($model_name);

        foreach ($roles as $r) {
            $actions = self::getActions($r->name,$cont);
            if(!empty($actions)){
                foreach ($actions as $action) {
                    $arr[$i]['controllers'] = [$controller];
                    $arr[$i]['actions'] = [$action];
                    $arr[$i]['allow'] = true;
                    if ($action == 'view' || $action == 'update' || $action == 'delete' || $action == 'ledger') {
                        $arr[$i]['roles'] = [$action.$cont];
                    } else {
                        $arr[$i]['roles'] = [$r->name];
                    }
                    $arr[$i]['roleParams'] = self::checkModel($model_name);
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

    public static function getActions($role,$controller){
        $list = AuthItemChild::find()->where(['parent'=>$role])->andWhere(['like','child',$controller])->asArray()->all();
        $action = array();
        $arr = array();
        $i = 0;
        foreach ($list as $l){
           // $str = str_replace($controller,'',$l['child']);
            $str = self::lreplace($controller,$l['child']);
            $arr[] = preg_split('/(?=[A-Z])/',$str);
            $action[]= $arr[$i][0];

            $i++;
        }
        return $action;
    }

    public static function getSearchFilter($dataProvider, $controller, $method)
    {

        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule)
        {
            if($rule == 'isBranch')
            {
                foreach (self::$user_branches as $user_branch) {
                    $branch_ids[] = $user_branch->branch_id;
                }
                if(!self::$user_branches)
                {
                    $branch_ids = 0;
                }
                if($controller == 'branches')
                {
                    return $dataProvider->query->andFilterWhere(['in', $controller . ".id", $branch_ids]);
                }
                else {
                    return $dataProvider->query->andFilterWhere(['in', $controller . ".branch_id", $branch_ids]);
                }
            } else if ($rule == 'isArea')
            {
                foreach (self::$user_areas as $user_area) {
                    $area_ids[] = $user_area->area_id;
                }
                if(!self::$user_areas)
                {
                    $area_ids = 0;
                }
                return $dataProvider->query->andFilterWhere(['in', $controller . ".area_id", $area_ids]);

                //return $dataProvider->query->andWhere($controller.'.area_id = '.Yii::$app->user->identity->area_id);
            }
            else if ($rule == 'isRegion')
            {
                foreach (self::$user_regions as $user_region) {
                    $region_ids[] = $user_region->region_id;
                }
                if(!self::$user_regions)
                {
                    $region_ids = 0;
                }
                return $dataProvider->query->andFilterWhere(['in', $controller . ".region_id", $region_ids]);
            } else if ($rule == 'isProject')
            {
                foreach (self::$user_projects as $user_project) {
                    $project_ids[] = $user_project->project_id;
                }
                if(!self::$user_projects)
                {
                    $project_ids = 0;
                }
                if($controller == 'branches')
                {
                    $dataProvider->query->joinWith('branchProjects');
                    return $dataProvider->query->andFilterWhere(['in',"branch_projects.project_id", $project_ids]);
                } else if($controller == 'groups' || $controller == 'borrowers')
                {
                    $dataProvider->query->joinWith('branch.branchProjects');
                    return $dataProvider->query->andFilterWhere(['in',"branch_projects.project_id", $project_ids]);
                }
                else {
                    return $dataProvider->query->andFilterWhere(['in', $controller . ".project_id", $project_ids]);
                }
            }
        }
    }

    public static function getSearchFilterQuery($query, $controller, $method)
    {
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule)
        {
            if($rule == 'isBranch')
            {
                foreach (self::$user_branches as $user_branch) {
                    $branch_ids[] = $user_branch->branch_id;
                }
                if(!self::$user_branches)
                {
                    $branch_ids = 0;
                }
                if($controller == 'branches')
                {
                    return $query->andFilterWhere(['in', $controller . ".id", $branch_ids]);
                }
                else {
                    return $query->andFilterWhere(['in', $controller . ".branch_id", $branch_ids]);
                }
            } else if ($rule == 'isArea')
            {
                foreach (self::$user_areas as $user_area) {
                    $area_ids[] = $user_area->area_id;
                }
                if(!self::$user_areas)
                {
                    $area_ids = 0;
                }
                return $query->andFilterWhere(['in', $controller . ".area_id", $area_ids]);

                //return $dataProvider->query->andWhere($controller.'.area_id = '.Yii::$app->user->identity->area_id);
            }
            else if ($rule == 'isRegion')
            {
                foreach (self::$user_regions as $user_region) {
                    $region_ids[] = $user_region->region_id;
                }
                if(!self::$user_regions)
                {
                    $region_ids = 0;
                }
                return $query->andFilterWhere(['in', $controller . ".region_id", $region_ids]);
            } else if ($rule == 'isProject')
            {
                foreach (self::$user_projects as $user_project) {
                    $project_ids[] = $user_project->project_id;
                }
                if(!self::$user_projects)
                {
                    $project_ids = 0;
                }
                if($controller == 'branches')
                {
                    $query->joinWith('branchProjects');
                    return $query->andFilterWhere(['in',"branch_projects.project_id", $project_ids]);
                } else if($controller == 'groups' || $controller == 'borrowers')
                {
                    $query->joinWith('branch.branchProjects');
                    return $query->andFilterWhere(['in',"branch_projects.project_id", $project_ids]);
                }
                else {
                    return $query->andFilterWhere(['in', $controller . ".project_id", $project_ids]);
                }
            }
        }
    }

    public  static function searchDisbursmentSummaryFilters()
    {
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/disbursement-summaryloans/',$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule) {
            $cond = '';
            $project_ids = '';
            $branch_ids = '';
            $area_ids = '';
            $region_ids = '';
            if ($rule == 'isRegion') {
                if (self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $region_ids .= $user_region->region_id . ',';
                    }
                    $cond .= "&& loans.region_id in (" . trim($region_ids, ',') . ")";
                    return $cond;
                }
            } else if ($rule == 'isArea') {
                if(self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $area_ids .= $user_area->area_id.',';
                    }
                    $cond .= "&& loans.area_id in (" .trim($area_ids,',').")";
                    return $cond;
                }
            } else if ($rule == 'isBranch') {
                if(self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branch_ids .= $user_branch->branch_id.',';
                    }
                    $cond .= "&& loans.branch_id in (" .trim($branch_ids,',').")";
                    return $cond;
                }
            } else if ($rule == 'isProject') {
                if(self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $project_ids .= $user_project->project_id.',';
                    }
                    $cond .= "&& loans.project_id in (" .trim($project_ids,',').")";
                    return $cond;
                }
            }
        }
    }

    public  static function searchRecoverySummaryFilters()
    {
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/recovery-summaryrecoveries/',$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule) {
            $cond = '';
            $branch_ids = '';
            $area_ids = '';
            $region_ids = '';
            $project_ids = '';
            if ($rule == 'isRegion') {
                if (self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $region_ids .= $user_region->region_id . ',';
                    }
                    $cond .= "&& recoveries.region_id in (" . trim($region_ids, ',') . ")";
                    return $cond;
                }
            } else if ($rule == 'isArea') {
                if(self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $area_ids .= $user_area->area_id.',';
                    }
                    $cond .= "&& recoveries.area_id in (" .trim($area_ids,',').")";
                    return $cond;
                }
            } else if ($rule == 'isBranch') {
                if(self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branch_ids .= $user_branch->branch_id.',';
                    }
                    $cond .= "&& recoveries.branch_id in (" .trim($branch_ids,',').")";
                    return $cond;
                }
            } else if ($rule == 'isProject') {
                if(self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $project_ids .= $user_project->project_id.',';
                    }
                    $cond .= "&& recoveries.project_id in (" .trim($project_ids,',').")";
                    return $cond;
                }
            }
        }
    }

    public  static function searchProgressReportsFilters($query)
    {
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/indexprogressreports/',$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule) {
            if($rule == 'isBranch') {
                if (self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branch_ids[] = $user_branch->branch_id;
                    }
                    if (!self::$user_branches) {
                        $branch_ids = 0;
                    }
                    return $query->andFilterWhere(['in','branches.id',$branch_ids]);
                }
            } else if ($rule == 'isArea') {
                if (self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $area_ids[] = $user_area->area_id;
                    }
                    if(!self::$user_areas)
                    {
                        $area_ids = 0;
                    }
                    return $query->andFilterWhere(['in','areas.id',$area_ids]);
                }
            } else if ($rule == 'isRegion') {
                if (self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $region_ids[] = $user_region->region_id;
                    }
                    if(!self::$user_regions)
                    {
                        $region_ids = 0;
                    }
                    return $query->andFilterWhere(['in','regions.id',$region_ids]);
                }
            } else if ($rule == 'isProject') {
                if (self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $project_ids[] = $user_project->project_id;
                    }
                    if(!self::$user_projects)
                    {
                        $project_ids = 0;
                    }
                    return $query->andFilterWhere(['in','projects.id',$project_ids]);
                }
            }
        }
    }

    public static function searchBranchListCodeWise($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $branch_list = [];
        $branchlist = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule) {
            if ($rule == 'isArea') {
                if (self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->area_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                    }
                   /* foreach ($branchlist as $branch) {
                        $branch_list = array_merge($branch_list, $branch);
                    }*/

                }
                return $branchlist;
            } else  if ($rule == 'isBranch') {
                if (self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->branch_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        $branch_list = array_merge($branch_list, $branch);
                    }
                }

                return $branch_list;
            } else  if ($rule == 'isRegion') {
                if (self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->region_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'code', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        $branch_list = array_merge($branch_list, $branch);
                    }
                }
                return $branch_list;
            } else  if ($rule == 'isProject') {
                if (self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $branches[] = BranchProjects::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branchlist[] = Branches::find()->where(['id' => $branch->branch_id])->one();
                        }
                    }
                    $branchlist = ArrayHelper::map($branchlist, 'code', 'name');
                }
                return $branchlist;
            }
        } else {
            $branches = Branches::find()->all();
            $branchlist = ArrayHelper::map($branches, 'code', 'name');
        }
        return $branchlist;
    }

    public static function searchBranchList($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $branch_list = [];
        $branchlist = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule) {
            if ($rule == 'isBranch') {
                if (self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->branch_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        $branch_list = array_merge($branch_list, $branch);
                    }
                }
                return $branch_list;
            } else if ($rule == 'isArea') {
                if (self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->area_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        $branch_list = array_merge($branch_list, $branch);
                    }
                }
                return $branch_list;
            } else if ($rule == 'isRegion') {
                if (self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->region_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'name', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        $branch_list = array_merge($branch_list, $branch);
                    }
                }
                return $branch_list;
            } else if ($rule == 'isProject') {
                if (self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $branches[] = BranchProjects::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branchlist[] = Branches::find()->where(['id' => $u_branch->branch_id])->one();
                        }
                    }
                    $branchlist = ArrayHelper::map($branchlist, 'name', 'name');
                }
                return $branchlist;
            }
        }  else {
                $branchlist = Branches::find()->all();
                $branchlist = ArrayHelper::map($branchlist, 'name', 'name');
                return $branchlist;
            }
    }

    public static function searchBranchListIdWise($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $branch_list = [];
        $branchlist = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule) {
            if ($rule == 'isBranch') {
                if (self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->branch_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        $branch_list = array_merge($branch_list, $branch);
                    }
                }
                return $branch_list;
            } else if ($rule == 'isArea') {
                if (self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->area_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        $branch_list = array_merge($branch_list, $branch);
                    }
                }
                return $branch_list;
            } else if ($rule == 'isRegion') {
                if (self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->region_id])->all();
                        $branchlist[] = ArrayHelper::map($branches, 'id', 'name');
                    }
                    foreach ($branchlist as $branch) {
                        $branch_list = array_merge($branch_list, $branch);
                    }
                }
                return $branch_list;
            } else if ($rule == 'isProject') {
                if (self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $branches[] = BranchProjects::find()->where(['project_id' => $user_project->project_id])->all();
                    }
                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branchlist[] = Branches::find()->where(['id' => $u_branch->branch_id])->one();
                        }
                    }
                    $branchlist = ArrayHelper::map($branchlist, 'id', 'name');
                }
                return $branchlist;
            }
        }  else {
            $branchlist = Branches::find()->all();
            $branchlist = ArrayHelper::map($branchlist, 'id', 'name');
            return $branchlist;
        }
    }

    public static function searchProjectList($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $projectlist = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule)
        {
            if ($rule == 'isBranch') {
                if(self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->branch_id])->all();
                    }

                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'name', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isArea') {
                if(self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->area_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projects_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($projects_array, 'name', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isRegion') {
                if(self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->region_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'name', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isProject') {
                if(self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $projectlist[] = Projects::find()->where(['id' => $user_project->project_id])->one();
                    }

                    $projectlist = ArrayHelper::map($projectlist, 'name', 'name');
                }
                return $projectlist;
            }
        } else {
                $projects = BranchProjects::find()->all();
                foreach ($projects as $project) {
                    $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                }
                $projectlist = ArrayHelper::map($projectlist, 'name', 'name');
                return $projectlist;
            }

    }

    public static function searchProjectListById($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $projectlist = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule)
        {
            if ($rule == 'isBranch') {
                if(self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->branch_id])->all();
                    }

                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isArea') {
                if(self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->area_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projects_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($projects_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isRegion') {
                if(self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->region_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isProject') {
                if(self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $projectlist[] = Projects::find()->where(['id' => $user_project->project_id])->one();
                    }

                    $projectlist = ArrayHelper::map($projectlist, 'id', 'name');
                }
                return $projectlist;
            }
        } else {
            $projects = BranchProjects::find()->all();
            foreach ($projects as $project) {
                $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
            }
            $projectlist = ArrayHelper::map($projectlist, 'id', 'name');
            return $projectlist;
        }

    }

    public static function searchProjectListIdWise($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $projectlist = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule)
        {
            if ($rule == 'isBranch') {
                if(self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->branch_id])->all();
                    }

                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isArea') {
                if(self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->area_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projects_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($projects_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isRegion') {
                if(self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->region_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'name');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isProject') {
                if(self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $projectlist[] = Projects::find()->where(['id' => $user_project->project_id])->one();
                    }

                    $projectlist = ArrayHelper::map($projectlist, 'id', 'name');
                }
                return $projectlist;
            }
        } else {
            $projects = BranchProjects::find()->all();
            foreach ($projects as $project) {
                $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
            }
            $projectlist = ArrayHelper::map($projectlist, 'id', 'name');
            return $projectlist;
        }

    }

    public static function searchProjectListIdCodeWise($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $projectlist = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';

        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule)
        {
            if ($rule == 'isBranch') {
                if(self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->branch_id])->all();
                    }

                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'code');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isArea') {
                if(self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->area_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projects_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($projects_array, 'id', 'code');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isRegion') {
                if(self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->region_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjects::find()->where(['branch_id' => $branch->id])->all();
                        //print_r($projects);
                        foreach ($projects as $project) {
                            $project_array[] = Projects::find()->where(['id' => $project->project_id])->one();
                            $projectlist = ArrayHelper::map($project_array, 'id', 'code');
                        }
                    }
                }
                return $projectlist;
            } else if ($rule == 'isProject') {
                if(self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $projectlist[] = Projects::find()->where(['id' => $user_project->project_id])->one();
                    }
                    $projectlist = ArrayHelper::map($projectlist, 'id', 'code');
                }
               /* print_r($projectlist);
                die();*/
                return $projectlist;
            }
        } else {
            $projects = BranchProjects::find()->all();
            foreach ($projects as $project) {
                $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
            }
            $projectlist = ArrayHelper::map($projectlist, 'id', 'code');
            return $projectlist;
        }

    }

    public static function searchProjectListFundingLineWise($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $projectlist = [];
        $myprojects = array();
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule)
        {
            if ($rule == 'isBranch') {
                if(self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branches = Branches::find()->where(['id' => $user_branch->branch_id])->all();
                    }

                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                        }
                        foreach ($projectlist as $p){
                            if($p)
                            {
                                $myprojects[$p->funding_line.'_'.$p->name] = $p->name;
                            }
                        }
                    }
                }
                return $myprojects;
            } else if ($rule == 'isArea') {
                if(self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $branches = Branches::find()->where(['area_id' => $user_area->area_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                        }
                        foreach ($projectlist as $p){
                            if($p)
                            {
                                $myprojects[$p->funding_line.'_'.$p->name] = $p->name;
                            }
                        }
                    }
                }
                return $myprojects;
            } else if ($rule == 'isRegion') {
                if(self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $branches = Branches::find()->where(['region_id' => $user_region->region_id])->all();
                    }
                    foreach ($branches as $branch) {
                        $projects = BranchProjectsMapping::find()->where(['branch_id' => $branch->id])->all();
                        foreach ($projects as $project) {
                            $projectlist[] = Projects::find()->where(['id' => $project->project_id])->one();
                        }
                        foreach ($projectlist as $p){
                            if($p)
                            {
                                $myprojects[$p->funding_line.'_'.$p->name] = $p->name;
                            }
                        }
                    }
                }
                return $myprojects;
            } else if ($rule == 'isProject') {
                if(self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $projectlist[] = Projects::find()->where(['id' => $user_project->project_id])->one();
                    }

                    foreach ($projectlist as $p){
                        if($p)
                        {
                            $myprojects[$p->funding_line.'_'.$p->name] = $p->name;
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

            foreach ($projectlist as $p){
                if($p)
                {
                    $myprojects[$p->funding_line.'_'.$p->name] = $p->name;
                }
            }
            return $myprojects;
        }
    }

    public static function searchAreaList($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $arealist =[];
        $area_list = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        $area_ids = [];
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule) {
            if ($rule == 'isBranch') {
                if (self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                    }
                    foreach ($area_ids as $area_id)
                    {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k=> $val)
                        {
                            $area_list[$k] =  $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isArea') {
                if (self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $areas = Areas::find()->where(['id' => $user_area->area_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    /* foreach ($area_ids as $area_id)
                     {
                         $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                         $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                     }*/
                    foreach ($arealist as $area) {
                        foreach ($area as $k=> $val)
                        {
                            $area_list[$k] =  $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isRegion') {
                if (self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $areas = Areas::find()->where(['region_id' => $user_region->region_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                        //$area_ids[] = Regions::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k=> $val)
                        {
                            $area_list[$k] =  $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isProject') {
                if (self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $branches[] = BranchProjects::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                    }
                    foreach ($area_ids as $area_id)
                    {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k=> $val)
                        {
                            $area_list[$k] =  $val;
                        }
                    }
                }
                return $area_list;
            }
        }
        else {
            $arealist = Areas::find()->all();
            $arealist = ArrayHelper::map($arealist, 'id', 'name');
            return $arealist;
        }
    }

    public static function searchAreaListNameWise($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $arealist =[];
        $area_list = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        $area_ids = [];
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule) {
            if ($rule == 'isBranch') {
                if (self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                    }
                    foreach ($area_ids as $area_id)
                    {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k=> $val)
                        {
                            $area_list[$k] =  $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isArea') {
                if (self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $areas = Areas::find()->where(['id' => $user_area->area_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                    }
                    /* foreach ($area_ids as $area_id)
                     {
                         $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                         $arealist[] = ArrayHelper::map($areas, 'id', 'name');
                     }*/
                    foreach ($arealist as $area) {
                        foreach ($area as $k=> $val)
                        {
                            $area_list[$k] =  $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isRegion') {
                if (self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $areas = Areas::find()->where(['region_id' => $user_region->region_id])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k=> $val)
                        {
                            $area_list[$k] =  $val;
                        }
                    }
                }
                return $area_list;
            } else if ($rule == 'isProject') {
                if (self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $branches[] = BranchProjects::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                    }
                    foreach ($area_ids as $area_id)
                    {
                        $areas = Areas::find()->where(['id' => $area_id[0]['area_id']])->all();
                        $arealist[] = ArrayHelper::map($areas, 'name', 'name');
                    }
                    foreach ($arealist as $area) {
                        foreach ($area as $k=> $val)
                        {
                            $area_list[$k] =  $val;
                        }
                    }
                }
                return $area_list;
            }
        }
        else {
            $arealist = Areas::find()->all();
            $arealist = ArrayHelper::map($arealist, 'name', 'name');
            return $arealist;
        }
    }

    //////
    ///////
    public static function searchRegionList($controller, $method)
    {
        $controller = str_replace('-','', strtolower($controller));
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        $regionlist =[];
        $region_list = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        $region_ids = [];
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule) {
            if ($rule == 'isBranch') {
                if (self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                    }
                    foreach ($regions_ids as $region_id)
                    {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k=> $val)
                        {
                            $region_list[$k] =  $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isArea') {
                if (self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $regions_ids[] = Areas::find()->select(['region_id'])->where(['id' => $user_area->area_id])->asArray()->all();
                    }
                    foreach ($regions_ids as $region_id)
                    {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k=> $val)
                        {
                            $region_list[$k] =  $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isRegion') {
                if (self::$user_regions) {
                    foreach (self::$user_regions as $user_region) {
                        $regions = Regions::find()->where(['id' => $user_region->region_id])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k=> $val)
                        {
                            $region_list[$k] =  $val;
                        }
                    }
                }
                return $region_list;
            } else if ($rule == 'isProject') {
                if (self::$user_projects) {

                    foreach (self::$user_projects as $user_project) {
                        $branches[] = BranchProjects::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch){
                        foreach ($branch as $user_branch) {
                            $regions_ids[] = Branches::find()->select(['region_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                    }
                    foreach ($regions_ids as $region_id)
                    {
                        $regions = Regions::find()->where(['id' => $region_id[0]['region_id']])->all();
                        $regionlist[] = ArrayHelper::map($regions, 'id', 'name');
                    }
                    foreach ($regionlist as $region) {
                        foreach ($region as $k=> $val)
                        {
                            $region_list[$k] =  $val;
                        }
                    }
                }
                return $region_list;
            }
        }
        else {
            $regionlist = Regions::find()->all();
            $regionlist = ArrayHelper::map($regionlist, 'id', 'name');
            return $regionlist;
        }
    }

    public static function searchAreasByRegions($controller, $method, $rgId)
    {
        $controller = str_replace('-','', strtolower($controller));
        $user_regions = \common\models\UserRegions::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_areas = \common\models\UserAreas::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_branches = \common\models\UserBranches::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_projects = \common\models\UserProjects::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $regionlist =[];
        $region_list = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }

        if($rule) {
            if ($rule == 'isBranch') {
                if (self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branch_ids[] = $user_branch->branch_id;
                    }
                }
                $area_ids = Branches::find()->select(['area_id'])->andFilterWhere(['in', 'id', $branch_ids])->all();
                foreach ($area_ids as $area_id) {
                    $area_id_list[] = $area_id->area_id;
                }
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andFilterWhere(['in', 'id', $area_id_list])->asArray()->all();

            } else if ($rule == 'isArea') {
                if (self::$user_areas) {
                    foreach (self::$user_areas as $user_area) {
                        $area_ids[] = $user_area->area_id;
                    }
                }
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andFilterWhere(['in', "id", $area_ids])->asArray()->all();
            } else if ($rule == 'isRegion') {
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->asArray()->all();
            } else if ($rule == 'isProject') {
                if (self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $branches[] = BranchProjects::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $user_branch) {
                            $area_ids[] = Branches::find()->select(['area_id'])->where(['id' => $user_branch->branch_id])->asArray()->all();
                        }
                    }
                    foreach ($area_ids as $area_id)
                    {
                        $areas[] = $area_id[0]['area_id'];
                    }
                }
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->andFilterWhere(['in', "id", $areas])->asArray()->all();
                }
            } else {
                return Areas::find()->select(['id', 'name'])->where(['region_id' => $rgId])->asArray()->all();
                }
        }

    public static function searchBranchesByArea($controller, $method, $rgId)
    {
        $controller = str_replace('-','', strtolower($controller));
        $user_regions = \common\models\UserRegions::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_areas = \common\models\UserAreas::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_branches = \common\models\UserBranches::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_projects = \common\models\UserProjects::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $regionlist =[];
        $region_list = [];
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/'.$method.$controller.'/',$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule) {
            if ($rule == 'isBranch') {
                if (self::$user_branches) {
                    foreach (self::$user_branches as $user_branch) {
                        $branch_ids[] = $user_branch->branch_id;
                    }
                }
                return Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId])->andFilterWhere(['in','id', $branch_ids])->all();
            } else if ($rule == 'isArea' || $rule == 'isRegion') {
                return Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId])->asArray()->all();
            } else if ($rule == 'isProject') {
                if (self::$user_projects) {
                    foreach (self::$user_projects as $user_project) {
                        $branches[] = BranchProjects::find()->where(['project_id' => $user_project->project_id])->all();
                    }

                    foreach ($branches as $branch) {
                        foreach ($branch as $u_branch) {
                            $branchlist[] = Branches::find()->where(['id' => $u_branch->branch_id])->one();
                        }
                    }
                    foreach ($branchlist as $branch_id)
                    {
                        $branch_ids[] = $branch_id->id;
                    }
                }
                return Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId])->andFilterWhere(['in','id', $branch_ids])->all();
            }
        }
        else {
            return Branches::find()->select(['id', 'name'])->where(['area_id'=>$rgId])->asArray()->all();
        }
    }
    public  static function searchAwpRegionWiseFilters($query)
    {
        //$user_areas = \common\models\UserAreas::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        //$user_projects = \common\models\UserProjects::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        //$user_branches = \common\models\UserBranches::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        //$user_regions = \common\models\UserRegions::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_projects = UserProjectsMapping::find()->where(['user_id' => Yii::$app->user->getId()])->all();
        $user_regions = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'region'])->all();
        $user_areas = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'area'])->all();
        $user_branches = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'branch'])->all();
        $user_teams = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'team'])->all();
        $user_fields = UserStructureMapping::find()->where(['user_id' => Yii::$app->user->getId(), 'obj_type' => 'field'])->all();
        $permission = Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->getId());
        $rule = '';
        foreach ($permission as $key => $value)
        {
            if(preg_match('/awp-reportawp/',$key))
            {
                $rule = $value->ruleName;
            }
        }
        if($rule) {
            if($rule == 'isBranch') {
                foreach ($user_branches as $user_branch) {
                    $branch_ids[] = $user_branch->obj_id;
                }
                if (!$user_branches) {
                    $branch_ids = 0;
                }
                return $query->andFilterWhere(['in','branches.id',$area_ids]);
            } else if ($rule == 'isArea') {
                foreach ($user_areas as $user_area) {
                    $area_ids[] = $user_area->obj_id;
                }
                if (!$user_areas) {
                    $area_ids = 0;
                }
                return $query->andFilterWhere(['in','areas.id',$area_ids]);
            }  else if ($rule == 'isRegion') {
                foreach ($user_regions as $user_region) {
                    $region_ids[] = $user_region->obj_id;
                }
                if (!$user_regions) {
                    $region_ids = 0;
                }
                return $query->andFilterWhere(['in','regions.id',$region_ids]);
            } else if ($rule == 'isProject') {
                foreach ($user_projects as $user_project) {
                    $project_ids[] = $user_project->project_id;
                }
                if (!$user_projects) {
                    $project_ids = 0;
                }
                return $query->andFilterWhere(['in','projects.id',$project_ids]);
            }
        }
    }
    public static function lreplace($search, $subject){
        $pos = strrpos($subject, $search);
        if($pos !== false){
            $subject = substr_replace($subject, '', $pos, strlen($search));
        }
       return $subject;
    }
}